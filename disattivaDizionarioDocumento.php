<?php session_start();
	require_once('database.php');

    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }elseif(!isset($_SESSION['UserData']['ID_Dizionario'])){
        header("location:adminindex.php"); 
        exit;
    }

    if(isset($_POST['Submit'])){

        $doc = isset($_POST['Doc']) ? $_POST['Doc'] : '';
        $diz = $_SESSION['UserData']['ID_Dizionario'];

        if (empty($doc)) {
            $msg = 'Seleziona un dizionario';
        }
        else{
        	$queryIdDoc = "SELECT ID_documento FROM documento WHERE Nome_documento = :doc";
            $check = $pdo->prepare($queryIdDoc);
        	$check->bindParam(':doc', $doc, PDO::PARAM_STR);
            $check->execute();
            $check->bindColumn(1, $tmp, PDO::PARAM_LOB);
    		while($check->fetch(PDO::FETCH_BOUND))
    			$idDoc = $tmp;

            $queryDeactivateDizionario = "DELETE FROM dizionario_attivo WHERE id_documento = :doc and id_dizionario = :diz";
            $check = $pdo->prepare($queryDeactivateDizionario); 
            $check->bindParam(':diz', $diz, PDO::PARAM_STR);
            $check->bindParam(':doc', $idDoc, PDO::PARAM_STR);
            $check->execute();
            $messaggio = 'Il dizionario è stato scollegato correttamente';
            
        }
        $_POST=null;
    }
    
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ASSIGN DICTIONARY TO DOCUMENT</title>
	<?php define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php") ?>
    	
        <style type="text/css">

            .container{
                margin-bottom: 10%;
            }

            label{
                float: left;
            }

            .col-auto{
                margin: 5%;
                margin-bottom: 0;
            }

            #Submit{
                width: 120px;
            }

        </style>
    </head>

    <body> 

        <div class = "container">
            <h1 class ="display-3"> Disattiva dizionario: </h1>
            <h2 class ="display-4"> <?php echo $_SESSION['UserData']['Nome_Dizionario'] ?> </h2>
            <form action="" method="post">
                <div class = "form-group">

				<?php if(isset($msg)){?>
				<div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
					<strong><i class="fas fa-exclamation-triangle"></i>&emsp; <?php echo $msg ?></strong>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<?php } ?>
                
                <?php if(isset($messaggio)){?>
                    	<div class="alert alert-success" role="alert">
                        	 <strong><?php echo $messaggio ?></strong>
	  						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
						</div>
                <?php } ?>

                <div class="col-auto">
                    <label for="nome"><strong>Documenti collegati al dizionario scelto: <span style="color: red; font-size: 14px;">*</span></strong></label>              
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                        <select class="form-control" id="Doc" name="Doc" >
                            <option value="" disabled selected hidden>Selezionare un documento in cui mettere offline il dizionario</option>
                            <?php            
                               $query = "SELECT Nome_documento FROM documento
                               WHERE ID_documento IN(
                                   SELECT id_documento FROM dizionario_attivo
                                   WHERE id_dizionario = :diz AND Attivo = 1
                                   )";
                               $check = $pdo->prepare($query);
                               $check->bindParam(':diz', $_SESSION['UserData']['ID_Dizionario'], PDO::PARAM_STR);
                               $check->execute();
                               while($doc = $check->fetch(PDO::FETCH_ASSOC)){
                                   echo '<option>' . $doc['Nome_documento'] . '</option>';
                               }  
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-auto" align="center">
                    <button type="submit" class="btn btn-primary" id="Submit" name="Submit">
                            Disattiva
                    </button>
                </div>
            </form>
        </div>
        
        <a href="logout.php" class="btn btn-primary" id="logout">
            Logout <i class="fas fa-sign-out-alt"></i>
        </a>

		<a href="turnBack.php" class="btn btn-primary" id="turnBack">
            Indice 
        </a>

        <?php include PREPATH.'page_builder/_footer.php';?>

    </body>
</html>
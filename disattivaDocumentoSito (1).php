<?php session_start();
	require_once('database.php');

    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }elseif(!isset($_SESSION['UserData']['ID_Documento'])){
        header("location:adminindex.php"); 
        exit;
    }

    if(isset($_POST['Submit'])){

        $Sito = isset($_POST['Sito']) ? $_POST['Sito'] : '';
        $Id = $_SESSION['UserData']['ID_Documento'];

        if (empty($Sito)) {
            $msg = 'Seleziona un sito';
        }
        else{
            $trimSito = substr($Sito, strpos($Sito, ': ')+2);

            //controllo se il documento è già offline
            $queryDocumentoAttivo = "SELECT Attivo FROM documento_attivo WHERE id_documento = :id and Indirizzo_Sito = :sito";
            $check = $pdo->prepare($queryDocumentoAttivo); 
            $check->bindParam(':id', $Id, PDO::PARAM_STR);
            $check->bindParam(':sito', $trimSito, PDO::PARAM_STR);
            $check->execute();
            $attivo = $check->fetch(PDO::FETCH_ASSOC);      

            if (!$attivo || $attivo == 0){
                $msg = 'Errore, il documento non è stato trovato sul sito';
            }
            else{
                $queryDeactivateDocument = "UPDATE documento_attivo SET Attivo = '0' WHERE id_documento = :id and Indirizzo_sito = :sito";
                $check = $pdo->prepare($queryDeactivateDocument); 
                $check->bindParam(':id', $Id, PDO::PARAM_STR);
                $check->bindParam(':sito', $trimSito, PDO::PARAM_STR);
                $check->execute();
                $messaggio = 'Il documento è stato correttamente tolto dal sito';
            }
        }
        $_POST=null;
    }
    
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ASSIGN SITE TO DOCUMENT</title>
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
            <h1 class ="display-3"> Disattiva documento: </h1>
            <h2 class ="display-4"> <?php echo $_SESSION['UserData']['Nome_Documento'] ?> </h2>
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
                    <label for="nome"><strong>Siti in cui il documento scelto è attivo: <span style="color: red; font-size: 14px;">*</span></strong></label>              
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                        <select class="form-control" id="exampleFormControlSelect1" name="Sito" >
                            <option value="" disabled selected hidden>Selezionare un sito in cui mettere offline il documento</option>
                            <?php            
                               $querysiti = "SELECT Nome_sito, Indirizzo_sito FROM sito
                               WHERE Nome_sito <> 'admin' AND Indirizzo_sito IN(
                                   SELECT Indirizzo_sito FROM documento_attivo
                                   WHERE id_documento = :doc AND Attivo = 1
                               )";
                               $checksiti = $pdo->prepare($querysiti);
                               $checksiti->bindParam(':doc', $_SESSION['UserData']['ID_Documento'], PDO::PARAM_STR);
                               $checksiti->execute();
                               while($sito = $checksiti->fetch(PDO::FETCH_ASSOC)){
                                   echo '<option>' . $sito['Nome_sito'] . ' : ' . $sito['Indirizzo_sito'] . '</option>';
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
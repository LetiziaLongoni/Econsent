<?php session_start();
	require_once('database.php');

    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }elseif(empty($_SESSION['UserData']['ID_Dizionario'])){
        header("location:adminIndex.php"); 
        exit;
    }else{
        $queryNomeDic = "SELECT Nome_dizionario FROM dizionario WHERE ID_dizionario = :id";
        $check = $pdo->prepare($queryNomeDic);
        $check->bindParam(':id', $_SESSION['UserData']['ID_Dizionario'], PDO::PARAM_STR);        
        $check->execute();
        $check->bindColumn(1, $nome, PDO::PARAM_STR);
        while($check->fetch(PDO::FETCH_BOUND))
        $_SESSION['UserData']['Nome_Dizionario'] = $nome;
    }


    if(isset($_POST['Register'])){

        $Documento = isset($_POST['Documento']) ? $_POST['Documento'] : '';
        $dizionario = $_SESSION['UserData']['ID_Dizionario'];
		
        //controllo se è stato selezionato un documento
        if (empty($Documento)) {
            $msg = 'Seleziona un documento';
        }
        else{
        	$queryIdDoc = "SELECT ID_documento FROM documento WHERE Nome_documento = :doc";
            $check = $pdo->prepare($queryIdDoc);
            $check->bindParam(':doc', $Documento, PDO::PARAM_STR);        
            $check->execute();
            $check->bindColumn(1, $tmp, PDO::PARAM_STR);
            while($check->fetch(PDO::FETCH_BOUND))
            	$idDoc = $tmp;
            
            $queryCheck = "SELECT * FROM dizionario_attivo WHERE id_documento = :doc";
            $check = $pdo->prepare($queryCheck);
            $check->bindParam(':doc', $idDoc, PDO::PARAM_STR);        
            $check->execute();
            if($check->rowCount() > 0){
            	$docAttivo = $check->fetch(PDO::FETCH_ASSOC);
                $queryUpdate = "UPDATE dizionario_attivo SET id_dizionario = :diz, Attivo = 1 * WHERE id_documento = :doc";
                $check = $pdo->prepare($queryCheck);
                $check->bindParam(':doc', $idDoc, PDO::PARAM_STR);        
                $check->bindParam(':doc', $dizionario, PDO::PARAM_STR);        
                $check->execute();
            }else{
                $queryInsert = "INSERT into dizionario_attivo(id_dizionario, id_documento, Attivo) VALUES (:id, :doc, 1)";
                $check = $pdo->prepare($queryInsert); 
                $check->bindParam(':id', $dizionario, PDO::PARAM_STR);
                $check->bindParam(':doc', $idDoc, PDO::PARAM_STR);
                $check->execute();

                if ($check->rowCount() > 0) {
                    $messaggio = 'Assegnazione eseguita con successo';
                } else {
                    $msg = 'Problemi con l\'assegnamento dei dati %s';
                }
            }
        }
        $_POST=null;
    }
    
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ADMIN PAGE</title>
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
            <h1 class ="display-4"> Assegna il dizionario <?php echo $_SESSION['UserData']['Nome_Dizionario'] ?> ad un documento: </h1>
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
                        <label><strong>Documenti in cui il dizionario non è attivo </strong></label>              
                        <div class="input-group mb-2">
                            <select class="form-control" id="exampleFormControlSelect1" name="Documento" >
                                <option value="" disabled selected hidden>Selezionare documento a cui associare il dizionario</option>
                                <?php     
                                    $queryDoc = "SELECT Nome_documento FROM documento
                                    WHERE ID_documento NOT IN
                                    (SELECT id_documento FROM dizionario_attivo 
                                    WHERE id_dizionario = :id AND Attivo=1
                                    GROUP BY id_documento)";
                                    $checkDoc = $pdo->prepare($queryDoc);
                                    $checkDoc->bindParam(':id', $_SESSION['UserData']['ID_Dizionario'], PDO::PARAM_STR);
                                    $checkDoc->execute();
                                    while($doc = $checkDoc->fetch(PDO::FETCH_ASSOC)){
                                        echo '<option>' . $doc['Nome_documento'] . '</option>';
                                    }  
                                ?>
                            </select>
                        </div>
                    </div>                
                    <div class="col-auto" align="center">
                        <button type="submit" class="btn btn-primary" id="Submit" name="Register">
                                Registra
                        </button>
                    </div>
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

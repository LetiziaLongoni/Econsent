<?php session_start();
	require_once('database.php');

    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }elseif(empty($_SESSION['UserData']['ID_Documento'])){
        header("location:adminIndex.php"); 
        exit;
    }else{
        $queryNomeDoc = "SELECT Nome_documento FROM documento WHERE ID_documento = :id";
        $check = $pdo->prepare($queryNomeDoc);
        $check->bindParam(':id', $_SESSION['UserData']['ID_Documento'], PDO::PARAM_STR);        
        $check->execute();
        $check->bindColumn(1, $nome, PDO::PARAM_STR);
        while($check->fetch(PDO::FETCH_BOUND))
        $_SESSION['UserData']['Nome_Documento'] = $nome;
    }


    if(isset($_POST['Register'])){
        $Sito = isset($_POST['Sito']) ? $_POST['Sito'] : '';
        $Id = $_SESSION['UserData']['ID_Documento'];

        if (empty($Sito)) {
            $msg = 'Seleziona un sito';
        }
        else{
            $trimSito = substr($Sito, strpos($Sito, ': ')+2);
			
            $queryDocument = "SELECT id_documento FROM documento_attivo WHERE id_documento = :id AND Indirizzo_sito = :sito";
            $checkTmp = $pdo->prepare($queryDocument); 
            $checkTmp->bindParam(':id', $Id, PDO::PARAM_STR);
            $checkTmp->bindParam(':sito', $trimSito, PDO::PARAM_STR);
            $checkTmp->execute();
            
            if($checkTmp->rowCount() > 0){
            	$queryUpdateDocument = "UPDATE documento_attivo SET Attivo = 1 WHERE id_documento = :id AND Indirizzo_sito = :sito";
                $check = $pdo->prepare($queryUpdateDocument); 
                $check->bindParam(':id', $Id, PDO::PARAM_STR);
                $check->bindParam(':sito', $trimSito, PDO::PARAM_STR);
                $check->execute();
                
                $messaggio = 'Assegnazione eseguita con successo';
            }
            elseif($documentExists == 0){
                $queryInsertDocument = "INSERT into documento_attivo(id_documento, Indirizzo_sito, Attivo) VALUES (:id, :sito, 1)";
                $check = $pdo->prepare($queryInsertDocument); 
                $check->bindParam(':id', $Id, PDO::PARAM_STR);
                $check->bindParam(':sito', $trimSito, PDO::PARAM_STR);
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
            <h1 class ="display-3"> Assegna sito al documento: </h1>
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
                        <label><strong>Siti in cui il documento non è attivo </strong></label>              
                        <div class="input-group mb-2">
                            <select class="form-control" id="exampleFormControlSelect1" name="Sito" >
                                <option value="" disabled selected hidden>Selezionare sito a cui associare il documento</option>
                                <?php     
                                    $querysiti = "SELECT Nome_sito, Indirizzo_sito FROM sito
                                    WHERE Nome_sito <> 'admin' AND Nome_sito <> 'disattivo' AND Indirizzo_sito NOT IN 
                                    (SELECT Indirizzo_sito FROM documento_attivo 
                                    WHERE id_documento = :doc AND Attivo=1
                                    GROUP BY Indirizzo_sito)";
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
<?php
	session_start(); 
	define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php");	
    require_once('database.php');

    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }elseif(!isset($_GET['id'])){ //se non viene passato un ID valido, rimando alla pagina prima
        header("location:modificaSito.php"); 
    }
    
    //se è valido, prendo l'indirizzo e scarico i dati del relativo sito
    $id=$_GET['id'];
    $queryDati = "SELECT * FROM sito WHERE Indirizzo_sito = :id";
    $check = $pdo->prepare($queryDati);
    $check->bindParam(':id', htmlspecialchars($id), PDO::PARAM_STR);
    $check->execute();
    $sito = $check->fetch(PDO::FETCH_ASSOC);
    if(!$sito || $sito['Indirizzo_sito'] == '1' || $sito['Indirizzo_sito'] == '0.0.0.0'){
    	//se il sito non viene trovato o se si prova a modificare siti inaccessibili, rimando alla pagina prima
    	header("location:modificaSito.php");
    }
    //salvo i dati in variabili di sessione per stamparli a schermo sotto
	$_SESSION['Modify']['Nome'] = $sito['Nome_sito'];	
	$_SESSION['Modify']['Indirizzo'] = $sito['Indirizzo_sito'];	
    $_SESSION['Modify']['R1'] = $sito['Reazione1'];	
    $_SESSION['Modify']['R2'] = $sito['Reazione2'];
    
    //quando premo il pulsante salvo tutti i campi del form
    if(isset($_POST['Modify'])){
		$Nome = isset($_POST['nomemod']) ? $_POST['nomemod'] : '';
        $R1 = isset($_POST['r1mod']) ? $_POST['r1mod'] : '';
        $R2 = isset($_POST['r2mod']) ? $_POST['r2mod'] : '';

        //controllo campi obbligatori
        if (empty($Nome) || empty($R1) || empty($R2)) {
			$msg = 'Compila tutti i campi';
        }elseif ($Nome == "admin"|| $Nome == "disattivo"){
        	$msg = 'Inserire un nome valido';
        } else{
          	$query = "UPDATE sito 
                	  SET Nome_sito = :nomemod, Reazione1 = :r1mod, Reazione2 = :r2mod
                      WHERE Indirizzo_sito = :indirizzo";

            $check = $pdo->prepare($query);
            $check->bindParam(':nomemod', $Nome, PDO::PARAM_STR);
            $check->bindParam(':r1mod', $R1, PDO::PARAM_STR);
            $check->bindParam(':r2mod', $R2, PDO::PARAM_STR);
            $check->bindParam(':indirizzo', $id, PDO::PARAM_STR);
            $check->execute();

            $messaggio="Registrazione eseguita correttamente";
            $id=$_GET['id'];
            $queryDati = "SELECT * FROM sito WHERE Indirizzo_sito = :id";
            $check = $pdo->prepare($queryDati);
            $check->bindParam(':id', htmlspecialchars($id), PDO::PARAM_STR);
            $check->execute();
            $sito = $check->fetch(PDO::FETCH_ASSOC);
            if(!$sito || $sito['Indirizzo_sito'] == '1' || $sito['Indirizzo_sito'] == '0.0.0.0'){
                //se il sito non viene trovato o se si prova a modificare siti inaccessibili, rimando alla pagina prima
                header("location:modificaSito.php");
            }
            //salvo i dati in variabili di sessione per stamparli a schermo sotto
            $_SESSION['Modify']['Nome'] = $sito['Nome_sito'];	
            $_SESSION['Modify']['Indirizzo'] = $sito['Indirizzo_sito'];	
            $_SESSION['Modify']['R1'] = $sito['Reazione1'];	
            $_SESSION['Modify']['R2'] = $sito['Reazione2'];
          }
        $_POST = null;
    }

?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ADMIN PAGE</title>
    	
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
          <h1 class ="display-4"> Modifica dati sito </h1>
            <form action="" method="post">
                <div class = "form-group">

                    <?php if(isset($msg)){?>
                    <div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <strong><i class="fas fa-exclamation-triangle"></i>&emsp; <?php echo $msg ?> </strong>-&emsp;Riprovare
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php } $msg = null; ?>
                    
                    <?php if(isset($messaggio)){?>
                    	<div class="alert alert-success" role="alert">
                        	 <strong><?php echo $messaggio ?></strong>
	  						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
						</div>
                    <?php } ?>
                    
                    <div class="col-auto">
                        <label><strong>Indirizzo sito:<?php echo $_SESSION['Modify']['Indirizzo'] ?></strong></label> <br>
                    </div>
                    <div class="col-auto">
                        <label><strong>Nome sito <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="nomemod" value = "<?php echo $_SESSION['Modify']['Nome'] ?>">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label><strong>Reazione 1 <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="r1mod" value = "<?php echo $_SESSION['Modify']['R1'] ?>">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label><strong>Reazione 2 <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="r2mod" value = "<?php echo $_SESSION['Modify']['R2'] ?>">
                        </div>
                    </div>
                    <div class="col-auto" align="center">
                        <button type="submit" class="btn btn-primary" id="Submit" name="Modify">
                                Prosegui
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
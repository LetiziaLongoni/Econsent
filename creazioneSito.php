<?php session_start();
	require_once('database.php');

    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }

    if(isset($_POST['Register'])){

        $NomeSito = isset($_POST['NomeSito']) ? $_POST['NomeSito'] : '';
		$IndirizzoSito = isset($_POST['IndirizzoSito']) ? $_POST['IndirizzoSito'] : '';
        $Reazione1 = isset($_POST['Reazione1']) ? $_POST['Reazione1'] : '';
        $Reazione2 = isset($_POST['Reazione2']) ? $_POST['Reazione2'] : '';
        

        if (empty($NomeSito) || empty($IndirizzoSito) || empty($Reazione1) || empty($Reazione2)) {
            $msg = 'Compila tutti i campi';
        }
        else{
            $query = "SELECT Indirizzo_sito FROM sito 
					  WHERE Indirizzo_sito = :indirizzoSito OR Nome_Sito = :nomesito";
            
            $check = $pdo->prepare($query);
            $check->bindParam(':indirizzoSito', $IndirizzoSito, PDO::PARAM_STR);
            $check->bindParam(':nomesito', $NomeSito, PDO::PARAM_STR);
            $check->execute();
            
            $sito = $check->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($sito) > 0) {
                $msg = "Il nome o l'indirizzo inseriti sono già occupati";
            } else {
                $query = "INSERT INTO sito (Nome_sito, Indirizzo_sito, Reazione1, Reazione2) VALUES (:nomeSito, :indirizzoSito, :reazione1, :reazione2)";
            
                $check = $pdo->prepare($query);
                $check->bindParam(':nomeSito', $NomeSito, PDO::PARAM_STR);
                $check->bindParam(':indirizzoSito', $IndirizzoSito, PDO::PARAM_STR);
                $check->bindParam(':reazione1', $Reazione1, PDO::PARAM_STR);
                $check->bindParam(':reazione2', $Reazione2, PDO::PARAM_STR);
                $check->execute();
                
                if ($check->rowCount() > 0) {
                    $messaggio = 'Registrazione eseguita con successo';
                } else {
                    $msg = 'Problemi con l\'inserimento dei dati %s';
                }
        }
    }   
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
          <h1 class ="display-4"> Registra un nuovo sito nel database </h1>
            <form action="" method="post">
                <div class = "form-group">

				<?php if(isset($msg)){?>
				<div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
					<strong><i class="fas fa-exclamation-triangle"></i>&emsp; <?php echo $msg ?></strong>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<?php } $msg=null;?>
                
                <?php if(isset($messaggio)){?>
                    	<div class="alert alert-success" role="alert">
                        	 <strong><?php echo $messaggio ?></strong>
	  						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
						</div>
                <?php } ?>

                <div class="col-auto">
                    <label for="username"><strong>Nome sito <span style="color: red; font-size: 14px;">*</span></strong></label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                        <input type="text" class="form-control" name="NomeSito" placeholder="Inserire il nome del sito">
                    </div>
                </div>
                
                <div class="col-auto">
                    <label for="username"><strong>Indirizzo sito <span style="color: red; font-size: 14px;">*</span></strong></label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                        <input type="text" class="form-control" name="IndirizzoSito" placeholder="Inserire l'indirizzo del sito">
                    </div>
                </div>
                
                <div class="col-auto">
                    <label for="username"><strong>Reazione 1 <span style="color: red; font-size: 14px;">*</span></strong></label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                        <input type="text" class="form-control" name="Reazione1" placeholder="Specificare la reazione 1 del sito">
                    </div>
                </div>
              
              <div class="col-auto">
                    <label for="username"><strong>Reazione 2 <span style="color: red; font-size: 14px;">*</span></strong></label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                        <input type="text" class="form-control" name="Reazione2" placeholder="Specificare la reazione 2 del sito">
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
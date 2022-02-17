<?php session_start();
    require_once('database.php');

    $Email = $_SESSION['UserData']['Email'];
    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }
    
    if(isset($_POST['Delete'])){
		$DeleteSito = isset($_POST['Delete_sito']) ? $_POST['Delete_sito'] : '';
        $ConfirmDeleteSito = isset($_POST['Confirm_delete_sito']) ? $_POST['Confirm_delete_sito'] : '';

        if (empty($DeleteSito) || empty($ConfirmDeleteSito)) {
            $msg = 'Compila tutti i campi';
        }
        else{
        	$trimSito = substr($DeleteSito, strpos($DeleteSito, ': ')+2);
        	$queryTrovaSito = "SELECT Indirizzo_sito FROM sito WHERE Indirizzo_sito = :delete_sito";
            $check = $pdo->prepare($queryTrovaSito);
            $check->bindParam(':delete_sito', $trimSito, PDO::PARAM_STR);
            $check->execute();
            $sito = $check->fetchAll(PDO::FETCH_ASSOC);	
            if (count($sito) == 0) {
              $msg = "Sito non trovato, sceglierne un altro";
            }
            elseif ($DeleteSito !== $ConfirmDeleteSito){
                $msg = 'I siti non corrispondono';
            }
            else {
                //eliminazione sito
                //elimino le associazioni tra il sito e i documenti che vi erano stati caricati
                $queryDeleteDocument = "DELETE FROM documento_attivo WHERE Indirizzo_sito = :delete_sito";
                $check = $pdo->prepare($queryDeleteDocument);
                $check->bindParam(':delete_sito', $trimSito, PDO::PARAM_STR);
                $check->execute();

                //disattivo gli user associati al sito che si vuole eliminare
                $queryUser = "UPDATE user SET Attivo = '0', Sito_appartenenza = '1' WHERE Sito_appartenenza = :delete_sito";
                $check = $pdo->prepare($queryUser);
                $check->bindParam(':delete_sito', $trimSito, PDO::PARAM_STR);
                $check->execute();

                //infine elimino il sito
                $queryDeleteSito = "DELETE FROM sito WHERE Indirizzo_sito = :delete_sito";
                $check = $pdo->prepare($queryDeleteSito);
                $check->bindParam(':delete_sito', $trimSito, PDO::PARAM_STR);
                $check->execute();

                //controllo eliminazione
                $controlQuery = "SELECT Indirizzo_sito FROM sito WHERE Indirizzo_sito = :delete_sito";
                $check = $pdo->prepare($controlQuery);
                $check->bindParam(':delete_sito', $trimSito, PDO::PARAM_STR);
                $check->execute();
                $sito = $check->fetchAll(PDO::FETCH_ASSOC);

                if (count($sito) !== 0) {
                    $msg = "L' eliminazione non è andata a buon fine";
                }
                else{
                    $messaggio = "Eliminazione effettuata correttamente";
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
          <h1 class ="display-4"> Elimina Sito </h1>
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
                  		<label for="username"><strong>Indirizzo sito <span style="color: red; font-size: 14px;">*</span></strong></label>
                    	<div class="input-group mb-2">
                     		<select class="form-control" id="Select1" name="Delete_sito" >
                        	<option value="" disabled selected hidden>Selezionare il sito che si vuole eliminare</option>
                            <?php   
                           		$querysiti = "SELECT Nome_sito, Indirizzo_sito FROM sito WHERE Nome_sito <> 'admin' AND Nome_sito <> 'disattivo'";
                                $checksiti = $pdo->prepare($querysiti);
                                $checksiti->execute();
                                while($sito = $checksiti->fetch(PDO::FETCH_ASSOC)){
                                    echo '<option>' . $sito['Nome_sito'] . ' : ' . $sito['Indirizzo_sito'] . '</option>';
                                }  
                        	?>
                      		</select>
                    	</div>        
                  </div>
                    
                  <div class="col-auto">
                      <label for="username"><strong>Conferma indirizzo sito <span style="color: red; font-size: 14px;">*</span></strong></label>
                    	 <div class="input-group mb-2">
                     		<select class="form-control" id="Select1" name="Confirm_delete_sito" >
                        	<option value="" disabled selected hidden>Confermare il sito che si vuole eliminare</option>
                            <?php     
                                $querysiti = "SELECT Nome_sito, Indirizzo_sito FROM sito WHERE Nome_sito <> 'admin' AND Nome_sito <> 'disattivo'";
                                $checksiti = $pdo->prepare($querysiti);
                                $checksiti->execute();
                                while($sito = $checksiti->fetch(PDO::FETCH_ASSOC)){
                                    echo '<option>' . $sito['Nome_sito'] . ' : ' . $sito['Indirizzo_sito'] . '</option>';
                                }  
                        	?>
                      		</select>
                    	</div>        
                  </div>
                    

                <div class="col-auto" align="center">
	  				<button type="submit" class="btn btn-primary" id="Submit" name="Delete">
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
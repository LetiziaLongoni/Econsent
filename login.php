<?php 
	session_start();
	session_destroy();
	session_start();
	require_once('database.php');

	/* Check Login form submitted */
	if(isset($_POST['Submit'])){

		/* VARIABILI USERNAME E PASSWORD CON VALORI INSERITI DALL UTENTE */
		$Email = isset($_POST['Email']) ? $_POST['Email'] : '';
		$Password = isset($_POST['Password']) ? $_POST['Password'] : '';

		$query = "SELECT  Email_User, Password_User, Admin, Sito_appartenenza, Attivo FROM user WHERE email_user = :email";

		$check = $pdo->prepare($query);
        $check->bindParam(':email', $Email, PDO::PARAM_STR);
        $check->execute();

		/* PRENDO LA PRIMA RIGA RITORNATA DALLA QUERY */
		$user = $check->fetch(PDO::FETCH_ASSOC);

		if(!$user){
            $msg = 'Credenziali utente errate';
		}
		else{
        	if(is_null($user['Password_User'])){
            	if(mb_strlen($Password) < 8){
                	$msg = 'Password inserita troppo corta';
                }elseif(mb_strlen($Password) > 20){
                	$msg = 'Password inserita troppo lunga';
                }else{
					$Hash = password_hash($Password, PASSWORD_BCRYPT);
                 
                	$querypass = "UPDATE user SET Password_user = :pass WHERE email_user = :email";
                    $checkpass = $pdo->prepare($querypass);
                    $checkpass->bindParam(':email', $Email, PDO::PARAM_STR);
                    $checkpass->bindParam(':pass', $Hash, PDO::PARAM_STR);
                    $checkpass->execute();
                    if ($checkpass->rowCount() > 0) {
                    	$messaggio = "La tua password è stata registrata ed è ora utilizzabile!";
                    } else {
                        $msg = 'Problemi con l\'inserimento dei dati ';
                    }
                }              	
            }else{            
                $db_hash = $user['Password_User'];
                $db_admin = $user['Admin'];
                $db_userAttivo = $user['Attivo'];

                if (!password_verify($Password, $db_hash)) {
                    $msg = 'Credenziali utente errate ';
                }elseif ($db_userAttivo == 0) {
                    header('Location: login.php');
                    exit;
                } elseif($db_admin == 1) {
                    $_SESSION['UserData']['Email'] = $Email;
                    $_SESSION['UserData']['Sito'] = 0;
                    $_SESSION['UserData']['Admin'] = 1;
                    $_SESSION['UserData']['UserAttivo'] = 1;
                    header('Location: adminIndex.php');
                    exit;
                }else {
                	$db_sito = $user['Sito_appartenenza'];
                    $queryReazioni = "SELECT Reazione1, Reazione2 FROM sito WHERE Indirizzo_sito = :sito";
                    $check = $pdo->prepare($queryReazioni);
        			$check->bindParam(':sito', $db_sito, PDO::PARAM_STR);
        			$check->execute();
                    while($result = $check->fetch(PDO::FETCH_ASSOC)){
    					$_SESSION['UserData']['Reazione1'] = !empty($result['Reazione1']) ? $result['Reazione1'] : 'Reazione 1';
                        $_SESSION['UserData']['Reazione2'] = !empty($result['Reazione2']) ? $result['Reazione2'] : 'Reazione 2';
                    }
                    
                    $_SESSION['UserData']['Email'] = $Email;
                    $_SESSION['UserData']['Sito'] = $user['Sito_appartenenza'];
                    $_SESSION['UserData']['Admin'] = 0;	
                    $_SESSION['UserData']['UserAttivo'] = 1;
                    header('Location: userIndex.php');
                    exit;
              	}
            }
		}
	}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>LOGIN PAGE</title>
		<?php define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php") ?>

		<style type="text/css">
        	button.link {
            	color:red;
            	background:none;
                border:none;
                font-weight: bold;
            }

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

		<div class="container">
		  <h1 class="display-4">Inserire le credenziali</h1>
			<form action="" method="post">
				<div class="form-group">
				<!-- alert messaggio errore credenziali -->
                  <?php if(isset($msg)){?>
                      <div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
                          <strong><i class="fas fa-exclamation-triangle"></i>&emsp;Credenziali errate!&emsp;</strong>-&emsp;Riprovare
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
                    <label for="email"><strong>Email Utente <span style="color: red; font-size: 14px;">*</span></strong></label>
                    <button type="button" class="link" data-toggle="modal" data-target="#exampleModal" align="right">
                      Primo accesso? Clicca qui
                    </button>
                    <div class="input-group mb-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fas fa-user-md"></i>
                        </div>
                      </div>
                      <!-- lascio scritto l'username nell'input text in caso di errore nelle credenziali-->
                      <input type="text" class="form-control" value="<?php if(isset($Email)) echo $Email;?>"
                             name="Email" placeholder="Inserire email utente">
                    </div>
                  </div>
                  <div class="col-auto">
                    <label for="password"><strong>Password <span style="color: red; font-size: 14px;">*</span></strong></label>
                    <div class="input-group mb-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fas fa-key"></i>
                        </div>
                      </div>
                      <input type="password" class="form-control" name="Password" placeholder="Inserire password">
                    </div>
                  </div>
				</div>
				<div class="col-auto mt-0" style="text-align: left;">
					<span style="color: red; font-size: 14px !important;">* Campi obbligatori</span>
				</div>

                <div class="col-auto" align="center">
                  <button type="submit" class="btn btn-primary" id="Submit" name="Submit">
                    Login <i class="fas fa-sign-in-alt"></i>
                  </button>
                </div>

              <!-- Modal -->
              <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">EConsent</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
						Se stai effettuando l'accesso per la prima volta inserisci la tua mail e successivamente inserisci la password che vorrai usare in futuro (minimo 8 caratteri, massimo 20).<br>
                        La password che inserirai verrà salvata e potrai usarla per i prossimi accessi.<br>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                    </div>
                  </div>
                </div>
              </div>
			</form>
		</div>

		<?php include PREPATH.'page_builder/_footer.php';?>

  </body>
</html>
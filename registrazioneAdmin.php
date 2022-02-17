<?php 
    session_start();
    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }
    
	require_once('database.php');

    if(isset($_POST['Register'])){
		$Email = $_POST['Email'];
        $Nome = $_POST['Nome'];
        $Cognome = $_POST['Cognome'];
        $CF = $_POST['CF'];
        $Note = $_POST['Note'] ? $_POST['Note'] : '';
        
        //controlli
        if (empty($Email) || empty($Nome) || empty($Cognome) || empty($CF)) {
            $msg = 'Compila tutti i campi';
        }elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Inserire una mail valida';
        }else {
            $query = "SELECT email_user FROM user WHERE email_user = :email";
            
            $check = $pdo->prepare($query);
            $check->bindParam(':email', $Email, PDO::PARAM_STR);
            $check->execute();
            
            $user = $check->fetchAll(PDO::FETCH_ASSOC);
            
            //controlla se l'email è già presente del DB
            if (count($user) > 0) {
                $msg = 'Username già in uso';
            } else {
                $query = "INSERT INTO user (email_user, Nome_user, Cognome_user, CF_user, Sito_appartenenza, Attivo, Note, Admin, Reazione1, Reazione2) VALUES (:email, :nome, :cognome, :cf, '0.0.0.0', 1, :note, 1, 0, 0)";
            
                $check = $pdo->prepare($query);
                $check->bindParam(':email', $Email, PDO::PARAM_STR);
                $check->bindParam(':nome', $Nome, PDO::PARAM_STR);
                $check->bindParam(':cognome', $Cognome, PDO::PARAM_STR);
                $check->bindParam(':cf', $CF, PDO::PARAM_STR);
                $check->bindParam(':note', $Note, PDO::PARAM_STR);
                $check->execute();
                
                if ($check->rowCount() > 0) {
                    $messaggio = 'Registrazione eseguita con successo';
                } else {
                    $msg = 'Problemi con l\'inserimento dei dati';
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
          <h1 class ="display-4"> Registra un nuovo Admin </h1>
            <form action="" method="post">
                <div class = "form-group">

				<?php if(isset($msg)){?>
				<div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
					<strong><i class="fas fa-exclamation-triangle"></i>&emsp;<?php echo $msg ?></strong>
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
                        <label for="nome"><strong>Nome <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="Nome" placeholder="Inserire Nome">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label for="nome"><strong>Cognome <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="Cognome" placeholder="Inserire Cognome">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label for="nome"><strong>Codice Fiscale <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="CF" placeholder="Inserire Codice Fiscale">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label for="username"><strong>Email <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="Email" placeholder="Inserire email">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label for="nome"><strong>Note </strong></label>                            
                        <input type="text" class="form-control" name="Note">
                    </div>
                </div>

                <div class="col-auto" align="center">
	  				<button type="submit" class="btn btn-primary" id="Submit" name="Register">
							Registra
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
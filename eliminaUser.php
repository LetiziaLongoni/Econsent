<?php session_start();
    require_once('database.php');

    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }
    
    if(isset($_POST['Delete'])){
		$DeleteUser = isset($_POST['Delete_user']) ? $_POST['Delete_user'] : '';
        $ConfirmDeleteUser = isset($_POST['Confirm_delete_user']) ? $_POST['Confirm_delete_user'] : '';


        if (empty($DeleteUser) || empty($ConfirmDeleteUser)) {
            $msg = 'Compila tutti i campi';
        }
        elseif ($DeleteUser !== $ConfirmDeleteUser){
            $msg = 'Le email non corrispondono';
        }
        else {
            $queryDeleteUser = "DELETE FROM user WHERE email_user = :delete_user";
            $check = $pdo->prepare($queryDeleteUser);
            $check->bindParam(':delete_user', $DeleteUser, PDO::PARAM_STR);
            $check->execute();
            
            $controlQuery = "SELECT email_user FROM user WHERE email_user = :delete_email";
            $check = $pdo->prepare($controlQuery);
            $check->bindParam(':delete_email', $DeleteUser, PDO::PARAM_STR);
            $check->execute();

            $user = $check->fetchAll(PDO::FETCH_ASSOC);
                
            if (count($user) !== 0) {
                $msg = "L' eliminazione non è andata a buon fine";
            }
            else{
                $messaggio = "Eliminazione effettuata correttamente";
            }
        }
    }
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>DELETE USER</title>
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
          <h1 class ="display-4"> Elimina un utente </h1>
            <form action="" method="post">
                <div class = "form-group">

				<?php if(isset($msg)){?>
				<div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
					<strong><i class="fas fa-exclamation-triangle"></i>&emsp; <?php echo $msg ?> </strong>-&emsp;Riprovare
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
                  		<label for="username"><strong>Email <span style="color: red; font-size: 14px;">*</span></strong></label>
                    	<div class="input-group mb-2">
                     		<select class="form-control" id="Select1" name="Delete_user" >
                        	<option value="" disabled selected hidden>Selezionare lo user che si vuole eliminare</option>
                            <?php   
                            $email = $_SESSION['UserData']['Email'];
                            $queryUser = "SELECT email_user FROM user WHERE email_user <> :email";
                            $checkUser = $pdo->prepare($queryUser);
                            $checkUser->bindParam(':email', $email, PDO::PARAM_STR); 
                            $checkUser->execute();
                            while($user = $checkUser->fetch(PDO::FETCH_ASSOC)){
                              echo '<option>' . $user['email_user'] . '</option>';
                            }  
                        	?>
                      		</select>
                    	</div>        
                  </div>
                    
                    <div class="col-auto">
                        <label for="username"><strong>Conferma email <span style="color: red; font-size: 14px;">*</span></strong></label>
                    	<div class="input-group mb-2">
                     		<select class="form-control" id="Select1" name="Confirm_delete_user" >
                        	<option value="" disabled selected hidden>Confermare lo user che si vuole eliminare</option>
                            <?php     
                            $email = $_SESSION['UserData']['Email'];
                            $queryUser = "SELECT email_user FROM user WHERE email_user <> :email";
                            $checkUser = $pdo->prepare($queryUser);
                            $checkUser->bindParam(':email', $email, PDO::PARAM_STR); 
                            $checkUser->execute();
                            while($user = $checkUser->fetch(PDO::FETCH_ASSOC)){
                              echo '<option>' . $user['email_user'] . '</option>';
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
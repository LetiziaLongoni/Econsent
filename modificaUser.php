<?php session_start();
    require_once('database.php');

    if(!isset($_SESSION['UserData']['Email'])){
      header("location:login.php");
      exit;
    }elseif($_SESSION['UserData']['Admin'] == 0){
        header("location:index.php");
        exit;
    }

    if(isset($_POST['Modify'])){
		$ModifyUser = isset($_POST['Email']) ? substr($_POST['Email'],0, strpos($_POST['Email'],' :')) : '';

        if (empty($ModifyUser)) {
            $msg = 'Nessuna email inserita';
        }else {
            $queryCheckUser = "SELECT ID_user FROM user WHERE email_user = :modify_user";
            $check = $pdo->prepare($queryCheckUser);
            $check->bindParam(':modify_user', $ModifyUser, PDO::PARAM_STR);
            $check->execute();
            $user = $check->fetch(PDO::FETCH_ASSOC);
                
            if (!$user) {
                $msg = "Utente non trovato";
            }
            else{
                $message = $user['ID_user'];
				header('location:modificaDati.php?id='.$message);
        	}
        }
    }
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>MODIFY USER</title>
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
            <h1 class ="display-4"> Modifica dati utente </h1>
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
                    <div class="col-auto">
                    	<label><strong>Elenco utenti</strong></label>
                    	<!--<label style="color:#FF8C00">-Arancione : utente disattivo</label><br />            
                    	<label style="color:#00BFFF">-Azzurro : admin</label><br />  
                    	<label>-Nero : utente normale</label>-->             
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <select class="form-control" name="Email" >
                                <option value="" disabled selected hidden></option>
                                <?php            
                                    $queryuser = "SELECT email_user, Nome_user, Cognome_user, Sito_appartenenza FROM user";
                                    $checkuser = $pdo->prepare($queryuser);
                                    $checkuser->execute();
                                    while($user = $checkuser->fetch(PDO::FETCH_ASSOC)){
                                    	if($user['Sito_appartenenza'] == 1){
                                        	echo '<option style="color:#FF8C00">' . $user['email_user'] . ' : ' . $user['Nome_user'] . ' ' . $user['Cognome_user'] . '</option>';
                                        }elseif($user['Sito_appartenenza'] == '0.0.0.0'){
                                        	if($user['email_user'] == $_SESSION['UserData']['Email']){
                                        		echo '<option style="color:#00BFFF">' . $user['email_user'] . ' : ' . $user['Nome_user'] . ' ' . $user['Cognome_user'] . '</option>';                                            
                                    		}
                                        }else{
                                            echo '<option>' . $user['email_user'] . ' : ' . $user['Nome_user'] . ' ' . $user['Cognome_user'] . '</option>';                                                                                    	
                                        }
                                    }  
                                ?>
                            </select>
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
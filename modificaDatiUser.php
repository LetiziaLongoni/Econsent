<?php
	session_start(); 
	define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php");	
    require_once('database.php');

    if(!isset($_SESSION['UserData']['Email'])){
        header("location:login.php");
        exit;
    }elseif($_SESSION['UserData']['Admin'] == 1){
        header("location:login.php");
        exit;
    }
    
    $queryId = "SELECT ID_user FROM user WHERE email_user = :email";
    $checkId = $pdo->prepare($queryId);
    $checkId->bindParam(':email', $_SESSION['UserData']['Email'], PDO::PARAM_STR);
    $checkId->execute();
    $checkId->bindColumn(1, $tmp, PDO::PARAM_LOB);
    while($checkId->fetch(PDO::FETCH_BOUND))
    	$id = $tmp;
    
    //scarico i dati del relativo User

    $queryDati = "SELECT email_user, CF_user, Nome_user, Cognome_user, Reazione1, Reazione2
                  FROM user WHERE ID_user = :id";
    $check = $pdo->prepare($queryDati);
    $check->bindParam(':id', $id, PDO::PARAM_STR);
    $check->execute();
    $user = $check->fetch(PDO::FETCH_ASSOC);
   
    $_SESSION['Modify']['Email'] = $user['email_user'];	
    $_SESSION['Modify']['CF'] = $user['CF_user'];	
    $_SESSION['Modify']['Nome'] = $user['Nome_user'];
    $_SESSION['Modify']['Cognome'] = $user['Cognome_user'];	
    $_SESSION['Modify']['R1'] = $user['Reazione1'];	
    $_SESSION['Modify']['R2'] = $user['Reazione2'];

    //quando premo il pulsante salvo tutti i campi del form
    if(isset($_POST['Modify'])){
      $Email = isset($_POST['emailmod']) ? $_POST['emailmod'] : '';
      $Password = isset($_POST['passwordmod']) ? $_POST['passwordmod'] : '';  
      $Nome = isset($_POST['nomemod']) ? $_POST['nomemod'] : '';
      $Cognome = isset($_POST['cognomemod']) ? $_POST['cognomemod'] : '';
      $Cf = isset($_POST['cfmod']) ? $_POST['cfmod'] : '';
      $R1 = isset($_POST['r1mod']) ? 1 : 0;
      $R2 = isset($_POST['r2mod']) ? 1 : 0;

      //controllo campi obbligatori
      if (empty($Email) || empty($Nome) || empty($Cognome) || empty($Cf)) {
        $msg = 'Compila tutti i campi';
      } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Inserire una mail valida';
      } elseif (!empty($Password) && mb_strlen($Password) < 8){
        $msg = 'Password troppo corta';
      } elseif (!empty($Password) && mb_strlen($Password) > 20){
        $msg = 'Password troppo lunga';
      }else{

        $query = "SELECT email_user FROM user WHERE email_user = :emailmod";
        $check = $pdo->prepare($query);
        $check->bindParam(':emailmod', $Email, PDO::PARAM_STR);
        $check->execute();
        $user = $check->fetchAll(PDO::FETCH_ASSOC);
        if (count($user) > 0 && $user == $emailUser) {
          $msg = 'Email già esistente';
        }else {
          $query = "UPDATE user 
                    SET email_user = :emailmod, Nome_user = :nomemod, Cognome_user = :cognomemod, CF_user = :cfmod, Reazione1 = :r1mod, Reazione2 = :r2mod
                    WHERE ID_user = :id";

          $check = $pdo->prepare($query);
          $check->bindParam(':emailmod', $Email, PDO::PARAM_STR);
          $check->bindParam(':nomemod', $Nome, PDO::PARAM_STR);
          $check->bindParam(':cognomemod', $Cognome, PDO::PARAM_STR);
          $check->bindParam(':cfmod', $Cf, PDO::PARAM_STR);
          $check->bindParam(':r1mod', $R1, PDO::PARAM_STR);
          $check->bindParam(':r2mod', $R2, PDO::PARAM_STR);
          $check->bindParam(':id', $id, PDO::PARAM_STR);
          $check->execute();

          if(!empty($Password)){     
            $Hash = password_hash($Password, PASSWORD_BCRYPT);
            $query = "UPDATE user SET Password_user = :passwordmod
                      WHERE ID_user = :id";            	
            $check2 = $pdo->prepare($query);
            $check2->bindParam(':passwordmod', $Hash, PDO::PARAM_STR);
            $check2->bindParam(':id', $id, PDO::PARAM_STR);
            $check2->execute();
          }
          $messaggio="registrazione eseguita correttamente";

          $queryDati = "SELECT email_user, CF_user, Nome_user, Cognome_user, Reazione1, Reazione2
                        FROM user WHERE ID_user = :id";
          $check = $pdo->prepare($queryDati);
          $check->bindParam(':id', $id, PDO::PARAM_STR);
          $check->execute();
          $user = $check->fetch(PDO::FETCH_ASSOC);

          $_SESSION['Modify']['Email'] = $user['email_user'];	
          $_SESSION['Modify']['CF'] = $user['CF_user'];	
          $_SESSION['Modify']['Nome'] = $user['Nome_user'];
          $_SESSION['Modify']['Cognome'] = $user['Cognome_user'];	
          $_SESSION['Modify']['R1'] = $user['Reazione1'];	
          $_SESSION['Modify']['R2'] = $user['Reazione2'];
          $_SESSION['UserData']['Email'] = $user['email_user'];
        }
    }
$_POST = null;
}
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>MODIFY USER</title>
    	
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
          <h1 class ="display-4"> Modifica dati User </h1>
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
                        <label><strong>Nome <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="nomemod" value = <?php echo $_SESSION['Modify']['Nome'] ?>>
                        </div>
                    </div>
                    <div class="col-auto">
                        <label><strong>Cognome <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="cognomemod" value = <?php echo $_SESSION['Modify']['Cognome'] ?>>
                        </div>
                    </div>
                    <div class="col-auto">
                        <label><strong>Codice fiscale <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="cfmod" value = <?php echo $_SESSION['Modify']['CF'] ?>>
                        </div>
                    </div>
                    <div class="col-auto">
                        <label><strong>Email <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="emailmod" value = <?php echo $_SESSION['Modify']['Email'] ?>>
                        </div>
                    </div>
                    
                    <div class="col-auto">
                        <label><strong>Nuova password (opzionale)</strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="password" class="form-control" name="passwordmod">
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
        <a href="turnBackUser.php" class="btn btn-primary" id="turnBack">
            Indice 
        </a>

        <?php include PREPATH.'page_builder/_footer.php';?>

    </body>
</html>
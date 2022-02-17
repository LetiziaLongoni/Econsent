<?php 
    session_start();
    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }
    require_once('database.php');

    if (isset($_POST['Submit'])) {
    	if (!isset($_POST['selectUser'])){
        	$msg = "selezione non valida, riprovare";
        }else{
			$user = substr($_POST['selectUser'],0, strpos($_POST['selectUser'],' :'));
            $query = "SELECT ID_user, Nome_user, Cognome_user, email_user, Admin FROM user WHERE email_user = :user";
            $check = $pdo->prepare($query);
            $check->bindParam(':user', $user, PDO::PARAM_STR);
            $check->execute();
            $tmp = $check->fetch(PDO::FETCH_ASSOC);
            if(empty($tmp)){
              	$msg = "selezione non valida, riprovare";
            }else{
            	$_SESSION['Show']['idUser'] = $tmp['ID_user'];
            	$_SESSION['Show']['nomeUser'] = $tmp['Nome_user'];
            	$_SESSION['Show']['cognomeUser'] = $tmp['Cognome_user'];
                $_SESSION['Show']['emailUser'] = $user;
                $_SESSION['Show']['Admin'] = $tmp['Admin'];
                
                if($_SESSION['Show']['Admin']==0){
                   $_SESSION['Show']['ruolo'] = 'User';
                }elseif($_SESSION['Show']['Admin']==1){
                  $_SESSION['Show']['ruolo'] = 'Admin';
                }
                
                $querySitoAppartenenza = "SELECT Sito_appartenenza FROM user WHERE ID_user = :user";
                $check = $pdo->prepare($querySitoAppartenenza);
                $check->bindParam(':user', $_SESSION['Show']['idUser'], PDO::PARAM_STR);
                $check->execute();
                $check->bindColumn(1, $sitoAppartenenza, PDO::PARAM_STR);
                while($check->fetch(PDO::FETCH_BOUND))
                  $_SESSION['Show']['sito'] = $sitoAppartenenza;
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
            require_once(PREPATH."page_builder/_header.php");
        ?>
    	
        <style type="text/css">

            .container{
                margin-bottom: 10%;
            }

            label{
                float: left;
            }

            .col{
                margin: 5%;
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
            <h1 class="display-4">Info Utenti</h1>
            <!--<label><strong>Selezionare un sito per visualizzarne più informazioni:</strong></label>     -->         
            <form action="" method="post">
                <div class = 'form-group'>
                    <?php if(!empty($msg)){?>    
                        <div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            <strong><i class="fas fa-exclamation-triangle"></i>&emsp; <?php echo $msg ?> </strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } ?>
                    <div class="col-auto">       
                        <div class="col-auto">
                         	<label><strong>Selezionare un utente per visualizzarne più informazioni:</strong></label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group mb-2">
                                <select class="form-control" id="selectUser" name="selectUser" >
                                  <option value="" disabled selected hidden>Elenco utenti </option>
                                  <?php   
                                  	$query = "SELECT ID_user, Nome_user, Cognome_user, email_user FROM user";
                                    $check = $pdo->prepare($query);
                                    $check->execute();
                                    while($User = $check->fetch(PDO::FETCH_ASSOC)){
                                        echo '<option>' . $User['email_user'] . ' : ' . $User['Nome_user'] . ' ' . $User['Cognome_user'] . '</option>';
                                    }
                                  ?>
                                </select>
                            </div> 
                        </div>
                        <div class="col-auto">
                          <button id="Submit" name="Submit" class="btn btn-primary" >
                            Prosegui
                          </button>
                        </div>
                    </div>
                     <div class="col-auto">
                    	<div class="row">
                        	<div class="col-auto">
                                <?php if(isset($_SESSION['Show']['ruolo'])){ ?> 
                                <label><strong>Ruolo: <?php echo $_SESSION['Show']['ruolo'] ?></strong></label>
                                <?php } ?>
							</div>
                      	</div>
					</div>
                    <div class="col-auto">
                    	<div class="row">
                            <div class="col-auto">
                                <?php if(isset($_SESSION['Show']['nomeUser'])){ ?> 
                                <label>Nome: <strong><?php echo $_SESSION['Show']['nomeUser'] ?></strong></label><br>
							</div>
                            <div class="col-auto">
                                <?php } if(isset($_SESSION['Show']['cognomeUser'])){ ?>
                                <label>Cognome: <strong><?php echo $_SESSION['Show']['cognomeUser'] ?></strong></label><br><br>
                            </div>
                            <div class="col-auto">    
                                <?php } if(isset($_SESSION['Show']['emailUser'])){ ?>
								<label>Email: <strong><?php echo $_SESSION['Show']['emailUser'] ?></strong></label><br><br>
                            </div>
                            <div class="col-auto">
                            	<?php } if($_SESSION['Show']['Admin'] == 0) { ?>
                                    <?php if(isset($_SESSION['Show']['sito'])){
                                    $queryNomeSito = "SELECT Nome_sito FROM sito WHERE Indirizzo_sito = :sito";
                                    $checkNomeSito = $pdo->prepare($queryNomeSito);
                                    $checkNomeSito->bindParam(':sito', $_SESSION['Show']['sito'], PDO::PARAM_STR);
                                    $checkNomeSito->execute();
                                    $nomeSito = $checkNomeSito->fetch(PDO::FETCH_ASSOC); ?>
                                    <label>Sito appartenza: <strong><?php echo $nomeSito['Nome_sito'] ?></strong></label><br><br>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if($_SESSION['Show']['Admin'] == 0) { ?>
                    <div class="col-auto">   
                    	<div class="row">
                            <div class="col">
                                <label><strong>Documenti compilabili dall'utente</strong></label>              
                                <div class="input-group mb-2">
                                    <select class="form-control" id="selectUser" name="doc">
                                        <option value="" disabled selected hidden>Elenco documenti</option>
                                        <?php    
                                        	if(isset($_SESSION['Show']['idUser'])){
                                                $querysito = "SELECT ID_Documento, Nome_documento FROM documento 
                                                              WHERE ID_Documento IN 
                                                              (SELECT ID_Documento FROM documento_attivo WHERE Indirizzo_sito = :sito)";
                                                $checksito = $pdo->prepare($querysito);
                                                $checksito->bindParam(':sito', $_SESSION['Show']['sito'], PDO::PARAM_STR);
                                                $checksito->execute();
                                                while($tmp = $checksito->fetch(PDO::FETCH_ASSOC)){
                                                    echo '<option>' . $tmp['Nome_documento'] . '</option>';
                                                }
                                            } 
                                        ?>
                                    </select>
                                </div>
                            </div>	
                        </div>
                    </div>
                    <?php } ?>
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
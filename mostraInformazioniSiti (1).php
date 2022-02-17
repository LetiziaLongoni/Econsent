<?php 
    session_start();
    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }
    require_once('database.php');

    if (isset($_POST['Submit'])) {
    	if (!isset($_POST['selectDoc'])){
        	$msg = "selezione non valida, riprovare";
        }else{
            $sito = $_POST['selectDoc'];
            $query = "SELECT Indirizzo_sito FROM sito WHERE Nome_sito = :nome";
            $check = $pdo->prepare($query);
            $check->bindParam(':nome', $sito, PDO::PARAM_STR);
            $check->execute();
            $tmp = $check->fetch(PDO::FETCH_ASSOC);
            if(empty($tmp)){
              	$msg = "selezione non valida, riprovare";
            }else{
            	$_SESSION['Show']['indSito'] = $tmp['Indirizzo_sito'];
            	$_SESSION['Show']['nomeSito'] = $sito;
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
            <h1 class="display-4">Info Siti</h1>
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
                         	<label><strong>Selezionare un sito per visualizzarne più informazioni:</strong></label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group mb-2">
                                <select class="form-control" id="selectDoc" name="selectDoc" >
                                  <option value="" disabled selected hidden>Elenco siti </option>
                                  <?php     
                                    $query = "SELECT Nome_sito FROM sito WHERE Nome_sito <> 'admin' AND Nome_sito <> 'disattivo'";
                                    $check = $pdo->prepare($query);
                                    $check->execute();
                                    while($doc = $check->fetch(PDO::FETCH_ASSOC)){
                                        echo '<option>' . $doc['Nome_sito'] . '</option>';
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
                                <?php if(isset($_SESSION['Show']['indSito'])){ ?> 
                                <label>Indirizzo: <strong><?php echo $_SESSION['Show']['indSito'] ?></strong></label><br><br>
							</div>
                            <div class="col-auto">
                                <?php } if(isset($_SESSION['Show']['nomeSito'])){ ?>
                                <label>Nome: <strong><?php echo $_SESSION['Show']['nomeSito'] ?></strong></label><br><br>
                                <?php } ?> 
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">   
                    	<div class="row">
                            <div class="col">
                                <label><strong>Documenti usati dal sito</strong></label>              
                                <div class="input-group mb-2">
                                    <select class="form-control" id="selectDoc" name="doc">
                                        <option value="" disabled selected hidden>Elenco documenti</option>
                                        <?php     
                                            if(isset($_SESSION['Show']['nomeSito'])){
                                                $querysito = "SELECT ID_Documento, Nome_documento FROM documento 
                                                            WHERE ID_Documento IN 
                                                            (SELECT ID_Documento FROM documento_attivo WHERE Indirizzo_sito = :sito)";
                                                $checksito = $pdo->prepare($querysito);
                                                $checksito->bindParam(':sito', $_SESSION['Show']['indSito'], PDO::PARAM_STR);
                                                $checksito->execute();
                                                while($tmp = $checksito->fetch(PDO::FETCH_ASSOC)){
                                                    echo '<option>' . $tmp['Nome_documento'] . '</option>';
                                                }
                                            } 
                                        ?>
                                    </select>
                                </div>
                            </div>	
                            <div class="col">	
                                <div class="">
                                    <label><strong>Utenti collegati al sito</strong></label>              
                                    <div class="input-group mb-2">
                                        <select class="form-control" id="selectUser" name="selectUser" >
                                            <option value="" disabled selected hidden>Elenco utenti</option>
                                            <?php     
                                                if(isset($_SESSION['Show']['nomeSito'])){
                                                    $queryuser = "SELECT email_user, Nome_user, Cognome_user FROM user WHERE Sito_Appartenenza = :sito";
                                                    $checkuser = $pdo->prepare($queryuser);
                                                    $checkuser->bindParam(':sito', $_SESSION['Show']['indSito'], PDO::PARAM_STR);
                                                    $checkuser->execute();
                                                    while($user = $checkuser->fetch(PDO::FETCH_ASSOC)){
                                                        echo '<option>' . $user['email_user'] . ' : ' . $user['Nome_user'] . ' ' . $user['Cognome_user'] . '</option>';
                                                    }  
                                                }
                                            ?>
                                        </select>
                                    </div>  
                                </div>
                            </div>
                        </div>
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
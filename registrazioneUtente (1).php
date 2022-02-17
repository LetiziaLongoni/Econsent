<?php session_start();
	require_once('database.php');

    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }

    if(isset($_POST['Register'])){
		$Email = isset($_POST['Email']) ? $_POST['Email'] : '';
        $Nome = isset($_POST['Nome']) ? $_POST['Nome'] : '';
        $Cognome = isset($_POST['Cognome']) ? $_POST['Cognome'] : '';
        $Cf = isset($_POST['CodiceFiscale']) ? $_POST['CodiceFiscale'] : '';
        $Sito = isset($_POST['SelectSito']) ? $_POST['SelectSito'] : '';
        $Note = $_POST['Note'] ? $_POST['Note'] : '';
        $R1 = isset($_POST['cbreazione1']) ? 1 : 0;
        $R2 = isset($_POST['cbreazione2']) ? 1 : 0;

        if (empty($Email) || empty($Nome) || empty($Cognome) || empty($Cf) || empty($Sito)) {
            $msg = 'Compila tutti i campi';
        } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Inserire una mail valida';
        } else {
            $trimSito = substr($Sito, strpos($Sito, ': ')+2);

            $query = "SELECT email_user FROM user WHERE email_user = :email";
            
            $check = $pdo->prepare($query);
            $check->bindParam(':email', $Email, PDO::PARAM_STR);
            $check->execute();
            
            $user = $check->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($user) > 0) {
                $msg = 'Email già esistente %s';
            } else {
                $query = "INSERT INTO user (email_user, CF_user, Nome_user, Cognome_user, Sito_appartenenza, Attivo, Note, Reazione1, Reazione2, Admin) VALUES (:email, :cf, :nome, :cognome, :sito, 1, :note, :r1, :r2,  0)";
            
                $check = $pdo->prepare($query);
                $check->bindParam(':email', $Email, PDO::PARAM_STR);
                $check->bindParam(':sito', $trimSito, PDO::PARAM_STR);
                $check->bindParam(':nome', $Nome, PDO::PARAM_STR);
                $check->bindParam(':cognome', $Cognome, PDO::PARAM_STR);
                $check->bindParam(':cf', $Cf, PDO::PARAM_STR);
                $check->bindParam(':note', $Note, PDO::PARAM_STR);
                $check->bindParam(':r1', $R1, PDO::PARAM_STR);
                $check->bindParam(':r2', $R2, PDO::PARAM_STR);
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
            <h1 class ="display-4"> Registra un nuovo User</h1>
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
                            <input type="text" class="form-control" name="CodiceFiscale" placeholder="Inserire Codice Fiscale">
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
                    <!--<div class="col-auto">
                        <label for="password"><strong>Password <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </div>
                            </div>
                            <input type="password" class="form-control" name="Password" placeholder="Inserire password">
                        </div>
                    </div>-->                 
                    <div class="col-auto">
                        <label for="nome"><strong>Sito <span style="color: red; font-size: 14px;">*</span></strong></label>              
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <select class="form-control" id="SelectSito" name="SelectSito" onchange="showReactions()">
                                <option disabled selected>Selezionare sito di appartenenza</option>
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
                        <label for="nome"><strong>Note (opzionale)</strong></label>                            
                        <input type="text" class="form-control" name="Note">
                    </div>                    
                    <div class="col-auto" id="hide">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="cbreazione1" value="value1">
                            <label id="cb1" class="form-check-label" >Reazione 1 &nbsp&nbsp&nbsp</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="cbreazione2" value="value2">
                            <label id="cb2" class="form-check-label" >Reazione 2</label>
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

		<a href="turnBack.php" class="btn btn-primary" id="turnBack" margin="50px">
            Indice
        </a>

        <?php include PREPATH.'page_builder/_footer.php';?>
		
        <script type="text/javascript">
            var cb1 = document.getElementById("cb1");
            var cb2 = document.getElementById("cb2");  
        	var divv = document.getElementById("hide");
        	var sel = document.getElementById("SelectSito");
        	divv.style.display="none";       	          
        	function showReactions() {
                divv.style.display = "block";
                var value = sel.options[sel.selectedIndex].text;
                var sito = value.substring(value.indexOf(':')+2);
                if(sito){
                  var request = new XMLHttpRequest();
                  //open(...) = preparazione della richiesta al server
                  request.open("GET", "getReactions.php?sito="+sito, false); 
                  request.overrideMimeType('text/xml; charset=iso-8859-1');
                  var lines;
                  request.onreadystatechange = function() {
                    if (request.readyState === 4) {  // Makes sure the document is ready to parse.
                      if (request.status === 200) {  // Makes sure the file was found.
                        lines = request.responseText; // Will separate each line into an array
                      }
                    }
                  }
                  request.send();  //send() = invio richiesta al server
                  var sep = lines.indexOf('::')
                  if(sep != -1){
                  	cb1.innerHTML = lines.substring(0,sep);
                    cb2.innerHTML = lines.substring(sep+2);
                  }
                  if(cb1.innerHTML=="")
                  	cb1.innerHTML = 'Reazione 1';
                  if(cb2.innerHTML=="")
                  	cb2.innerHTML = 'Reazione 2';
                }
            }
        </script>
        
    </body>

</html>

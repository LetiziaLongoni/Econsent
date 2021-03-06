<?php
	session_start(); 
	define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php");	
    require_once('database.php');

    if(!isset($_SESSION['UserData']['Email'])){
        header("location:login.php");
        exit;
    }elseif($_SESSION['UserData']['Admin'] == 0){
        header("location:index.php");
        exit;
    }elseif(!isset($_GET['id']) || !is_numeric($_GET['id'])){ //se non viene passato un ID valido, rimando alla pagina prima
        header("location:modificaUser.php");
    }
    
    //se è valido, prendo l'ID scarico i dati del relativo utente
    $id=$_GET['id'];
    
    $queryAdmin = "SELECT Admin FROM user WHERE ID_user = :id";
    $checkAdmin = $pdo->prepare($queryAdmin);
    $checkAdmin->bindParam(':id', $id, PDO::PARAM_STR);
    $checkAdmin->execute();
    $checkAdmin->bindColumn(1, $tmp, PDO::PARAM_LOB);
    while($checkAdmin->fetch(PDO::FETCH_BOUND))
    	$Admin = $tmp;
        
    //scarico i dati del relativo Admin o User
    if($Admin == 0){
        $queryDati = "SELECT email_user, CF_user, Nome_user, Cognome_user, Sito_appartenenza, Reazione1, Reazione2, Admin
                      FROM user WHERE ID_user = :id";
        $check = $pdo->prepare($queryDati);
        $check->bindParam(':id', htmlspecialchars($id), PDO::PARAM_STR);
        $check->execute();
        $user = $check->fetch(PDO::FETCH_ASSOC);
        if(!$user || ($user['Admin'] == 1 && $user['email_user'] != $_SESSION['UserData']['Email'])){
            //se l'utente non viene trovato o se si prova a modificare altri admin, rimando alla pagina prima
            header("location:modificaUser.php");
        }
        $_SESSION['Modify']['Email'] = $user['email_user'];	
        $_SESSION['Modify']['CF'] = $user['CF_user'];	
        $_SESSION['Modify']['Nome'] = $user['Nome_user'];
        $_SESSION['Modify']['Cognome'] = $user['Cognome_user'];	
        $_SESSION['Modify']['Sito'] = $user['Sito_appartenenza'];
        $_SESSION['Modify']['R1'] = $user['Reazione1'];	// ['Modify'][R1] e [R2] memorizzano se le reazioni sono checkate o no
        $_SESSION['Modify']['R2'] = $user['Reazione2'];

        //prendo anche il nome del sito e delle reazioni per mostrare a display
        $queryNomeSito = "SELECT Nome_sito, Indirizzo_sito, Reazione1, Reazione2
                          FROM sito WHERE Indirizzo_sito = :nome";
        $check = $pdo->prepare($queryNomeSito);
        $check->bindParam(':nome', $_SESSION['Modify']['Sito'], PDO::PARAM_STR);
        $check->execute();
        $user = $check->fetch(PDO::FETCH_ASSOC);
        $_SESSION['Modify']['Nomesito'] = $user['Nome_sito'];
        $_SESSION['Modify']['Reazione1'] = $user['Reazione1']; //['Modify'][Reazione1] e [Reazione2] memorizzano i nomi delle reazioni in base al sito iniziale
        $_SESSION['Modify']['Reazione2'] = $user['Reazione2'];

        //quando premo il pulsante salvo tutti i campi del form
        if(isset($_POST['Modify'])){
            $Email = isset($_POST['emailmod']) ? $_POST['emailmod'] : '';
            $Password = isset($_POST['passwordmod']) ? $_POST['passwordmod'] : '';  
            $Nome = isset($_POST['nomemod']) ? $_POST['nomemod'] : '';
            $Cognome = isset($_POST['cognomemod']) ? $_POST['cognomemod'] : '';
            $Cf = isset($_POST['cfmod']) ? $_POST['cfmod'] : '';
            $Sito = isset($_POST['sitomod']) ? substr($_POST['sitomod'], strpos($_POST['sitomod'], ': ')+2) : $_SESSION['Modify']['Sito'];
            $R1 = isset($_POST['r1mod']) ? 1 : 0;
            $R2 = isset($_POST['r2mod']) ? 1 : 0;

            //controllo campi obbligatori
            if (empty($Email) || empty($Nome) || empty($Cognome) || empty($Cf) || empty($Sito)) {
                $msg = 'Compila tutti i campi';
            } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                $msg = 'Inserire una mail valida';
            } elseif (!empty($Password) && mb_strlen($Password) < 8){
                $msg = 'Password troppo corta';
            } elseif (!empty($Password) && mb_strlen($Password) > 20){
                $msg = 'Password troppo lunga';
            } else{
                if($trimSito == '1'){
                    $Attivo = 0;
                }else{
                    $Attivo = 1;
                }

                $query = "SELECT email_user FROM user WHERE email_user = :emailmod AND ID_user <> :id";
                $check = $pdo->prepare($query);
                $check->bindParam(':emailmod', $Email, PDO::PARAM_STR);
                $check->bindParam(':id', $id, PDO::PARAM_STR);
                $check->execute();
                $user = $check->fetchAll(PDO::FETCH_ASSOC);
                if (count($user) > 0) {
                    $msg = 'Email già esistente';
                }else {
                    $query = "UPDATE user 
                              SET email_user = :emailmod, Nome_user = :nomemod, Cognome_user = :cognomemod, Sito_appartenenza = :sitomod, CF_user = :cfmod, Reazione1 = :r1mod, Reazione2 = :r2mod, Attivo = :attivo
                              WHERE ID_user = :id";

                    $check = $pdo->prepare($query);
                    $check->bindParam(':emailmod', $Email, PDO::PARAM_STR);
                    $check->bindParam(':sitomod', $Sito, PDO::PARAM_STR);
                    $check->bindParam(':nomemod', $Nome, PDO::PARAM_STR);
                    $check->bindParam(':cognomemod', $Cognome, PDO::PARAM_STR);
                    $check->bindParam(':cfmod', $Cf, PDO::PARAM_STR);
                    $check->bindParam(':r1mod', $R1, PDO::PARAM_STR);
                    $check->bindParam(':r2mod', $R2, PDO::PARAM_STR);
                    $check->bindParam(':attivo', $Attivo, PDO::PARAM_STR);
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
                    
                    $queryDati = "SELECT email_user, CF_user, Nome_user, Cognome_user, Sito_appartenenza, Reazione1, Reazione2, Admin
                              FROM user WHERE ID_user = :id";
                    $check = $pdo->prepare($queryDati);
                    $check->bindParam(':id', htmlspecialchars($id), PDO::PARAM_STR);
                    $check->execute();
                    $user = $check->fetch(PDO::FETCH_ASSOC);
                    if(!$user || ($user['Admin'] == 1 && $user['email_user'] != $_SESSION['UserData']['Email'])){
                      //se l'utente non viene trovato o se si prova a modificare altri admin, rimando alla pagina prima
                      header("location:modificaUser.php");
                    }
                    $_SESSION['Modify']['Email'] = $user['email_user'];	
                    $_SESSION['Modify']['CF'] = $user['CF_user'];	
                    $_SESSION['Modify']['Nome'] = $user['Nome_user'];
                    $_SESSION['Modify']['Cognome'] = $user['Cognome_user'];	
                    $_SESSION['Modify']['Sito'] = $user['Sito_appartenenza'];
                    $_SESSION['Modify']['R1'] = $user['Reazione1'];	
                    $_SESSION['Modify']['R2'] = $user['Reazione2'];
                }
            }
            $_POST = null;
        }
     }elseif($Admin == 1){
     	$queryDati = "SELECT email_user, CF_user, Nome_user, Cognome_user
                      FROM user WHERE ID_user = :id";
        $check = $pdo->prepare($queryDati);
        $check->bindParam(':id', htmlspecialchars($id), PDO::PARAM_STR);
        $check->execute();
        $user = $check->fetch(PDO::FETCH_ASSOC);
        if(!$user || ($Admin == 1 && $user['email_user'] != $_SESSION['UserData']['Email'])){
            //se l'utente non viene trovato o se si prova a modificare altri admin, rimando alla pagina prima
            header("location:modificaUser.php");
        }
        $_SESSION['Modify']['Email'] = $user['email_user'];	
        $_SESSION['Modify']['CF'] = $user['CF_user'];	
        $_SESSION['Modify']['Nome'] = $user['Nome_user'];
        $_SESSION['Modify']['Cognome'] = $user['Cognome_user'];	


        //quando premo il pulsante salvo tutti i campi del form
        if(isset($_POST['Modify'])){
            $Email = isset($_POST['emailmod']) ? $_POST['emailmod'] : '';
            $Password = isset($_POST['passwordmod']) ? $_POST['passwordmod'] : '';  
            $Nome = isset($_POST['nomemod']) ? $_POST['nomemod'] : '';
            $Cognome = isset($_POST['cognomemod']) ? $_POST['cognomemod'] : '';
            $Cf = isset($_POST['cfmod']) ? $_POST['cfmod'] : '';

            //controllo campi obbligatori
            if (empty($Email) || empty($Nome) || empty($Cognome) || empty($Cf)) {
                $msg = 'Compila tutti i campi';
            } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
                $msg = 'Inserire una mail valida';
            } elseif (!empty($Password) && mb_strlen($Password) < 8){
                $msg = 'Password troppo corta';
            } elseif (!empty($Password) && mb_strlen($Password) > 20){
                $msg = 'Password troppo lunga';
            
                $query = "SELECT email_user FROM user WHERE email_user = :emailmod AND ID_user <> :id";
                $check = $pdo->prepare($query);
                $check->bindParam(':emailmod', $Email, PDO::PARAM_STR);
                $check->bindParam(':id', $id, PDO::PARAM_STR);
                $check->execute();
                $user = $check->fetchAll(PDO::FETCH_ASSOC);
                if (count($user) > 0) {
                    $msg = 'Email già esistente';
                }else {
                    $query = "UPDATE user 
                              SET email_user = :emailmod, Nome_user = :nomemod, Cognome_user = :cognomemod, CF_user = :cfmod
                              WHERE ID_user = :id";

                    $check = $pdo->prepare($query);
                    $check->bindParam(':emailmod', $Email, PDO::PARAM_STR);
                    $check->bindParam(':nomemod', $Nome, PDO::PARAM_STR);
                    $check->bindParam(':cognomemod', $Cognome, PDO::PARAM_STR);
                    $check->bindParam(':cfmod', $Cf, PDO::PARAM_STR);
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
                    
                    $queryDati = "SELECT email_user, CF_user, Nome_user, Cognome_user, Sito_appartenenza, Reazione1, Reazione2, Admin
                    FROM user WHERE ID_user = :id";
                    $check = $pdo->prepare($queryDati);
                    $check->bindParam(':id', htmlspecialchars($id), PDO::PARAM_STR);
                    $check->execute();
                    $user = $check->fetch(PDO::FETCH_ASSOC);
                    if(!$user || ($user['Admin'] == 1 && $user['email_user'] != $_SESSION['UserData']['Email'])){
                        //se l'utente non viene trovato o se si prova a modificare altri admin, rimando alla pagina prima
                        header("location:modificaUser.php");
                    }
                    $_SESSION['Modify']['Email'] = $user['email_user'];	
                    $_SESSION['Modify']['CF'] = $user['CF_user'];	
                    $_SESSION['Modify']['Nome'] = $user['Nome_user'];
                    $_SESSION['Modify']['Cognome'] = $user['Cognome_user'];	
                    $_SESSION['Modify']['Sito'] = $user['Sito_appartenenza'];
                    $_SESSION['Modify']['R1'] = $user['Reazione1'];	
                    $_SESSION['Modify']['R2'] = $user['Reazione2'];
                }
            $_POST = null;
        }}}
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
                    
                    <?php if(isset($messaggio)){?>
                    	<div class="alert alert-success" role="alert">
                        	 <strong><?php echo $messaggio ?></strong>
	  						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
						</div>
                    <?php } ?>

					<?php if($Admin == 0){?>
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
                    	<label><strong>Sito <span style="color: red; font-size: 14px;">*</span></strong></label>              
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <select class="form-control" id="sitomod" name="sitomod" onchange="showReactions()">
                                <option value="" disabled selected hidden><?php echo $_SESSION['Modify']['Nomesito'] . ' : ' . $_SESSION['Modify']['Sito'] ?></option>
                                <?php            
                                    $querysiti = "SELECT Nome_sito, Indirizzo_sito FROM sito WHERE Nome_sito <> 'admin' AND Nome_sito <> 'disattivo' ";
                                    $checksiti = $pdo->prepare($querysiti);
                                    $checksiti->execute();
                                    while($sito = $checksiti->fetch(PDO::FETCH_ASSOC)){
                                        echo '<option>' . $sito['Nome_sito'] . ' : ' . $sito['Indirizzo_sito'] . '</option>';
                                    }  
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-auto" >
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="r1mod" value="value1" <?php if($_SESSION['Modify']['R1'] == 1) {?> checked <?php } ?> > 
                            <label id="cb1" class="form-check-label" > <?php echo $_SESSION['Modify']['Reazione1']?> &nbsp&nbsp&nbsp</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="r2mod" value="value2" <?php if($_SESSION['Modify']['R2'] == 1) {?> checked <?php } ?> >
                            <label id="cb2" class="form-check-label" > <?php echo $_SESSION['Modify']['Reazione2']?> </label>
                        </div>     
                    </div> 
 					<?php } ?>
                    
                    <?php if($Admin == 1){?>
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
                    <div class="col-auto">
                        <label><strong>Codice fiscale <span style="color: red; font-size: 14px;">*</span></strong></label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" name="cfmod" value = <?php echo $_SESSION['Modify']['CF'] ?> >
                        </div>
                    </div>
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
                    <?php } ?>
                    
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

        <script type="text/javascript">
            var cb1 = document.getElementById("cb1");
            var cb2 = document.getElementById("cb2");  
        	var sel = document.getElementById("sitomod");
            
        	function showReactions() {
            	var value="";
                if(sel)
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
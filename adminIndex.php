<?php 
    session_start();
    define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php"); 
    
    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }
    
    if(isset($_SESSION['UserData']['Nome_Documento']))
        $_SESSION['UserData']['Nome_Documento']=null; 
    if(isset($_SESSION['UserData']['ID_Documento']))
        $_SESSION['UserData']['ID_Documento']=null;    
    if(isset($_SESSION['UserData']['Nome_Dizionario']))
        $_SESSION['UserData']['Nome_Dizionario']=null; 
    if(isset($_SESSION['UserData']['ID_Dizionario']))
        $_SESSION['UserData']['ID_Dizionario']=null;
    if(isset($_SESSION['Modify'])) //(array) usato per modifiche sito e utente
        $_SESSION['Modify']=null;
	if(isset($_SESSION['Show'])) //(array) usato in mostra info utenti/doc/siti
        $_SESSION['Show']=null;
    if(isset($_SESSION['reazione1'])) //usato in registrazioneUtente
        $_SESSION['reazione1']=null;
    if(isset($_SESSION['reazione2'])) //usato in registrazioneUtente
        $_SESSION['reazione2']=null;
    
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>ADMIN INDEX PAGE</title>
        <?php 
            require_once(PREPATH."page_builder/_header.php") 
        ?>
    </head>

    <body>
        <div class = "container">
            <label class="text-primary" style="font-size: 35px;"> Selezionare una delle seguenti azioni: </label>
            
            <ul class = "list-group" style="font-size: 25px;"><br>
                <a class="list-group-item list-group-item-info" href="mostraInformazioniUtenti.php" style="color:black">Visualizza le informazioni legate agli utenti </a>
                <a class="list-group-item list-group-item-info" href="mostraInformazioniSiti.php" style="color:black">Visualizza le informazioni legate ai Siti </a>
                <a class="list-group-item list-group-item-info" href="mostraInformazioniDocumenti.php" style="color:black">Visualizza le informazioni legate ai documenti </a>
                <a class="list-group-item list-group-item-info" href="registrazioneUtente.php" style="color:black">Registra un nuovo User </a>
                <a class="list-group-item list-group-item-info" href="registrazioneAdmin.php"style="color:black">Registra un nuovo Admin </a>
              	<a class="list-group-item list-group-item-info" href="creazioneSito.php" style="color:black">Registra un nuovo Sito </a>
              	<a class="list-group-item list-group-item-info" href="uploadDocument.php" style="color:black">Aggiungi un documento nel Database</a>
                <a class="list-group-item list-group-item-info" href="uploadDizionario.php" style="color:black">Aggiungi un dizionario nel Database</a>
           		<a class="list-group-item list-group-item-info" href="modificaUser.php" style="color:black">Modifica i dati di un utente </a>
           		<a class="list-group-item list-group-item-info" href="modificaSito.php" style="color:black">Modifica i dati di un Sito </a>
                <a class="list-group-item list-group-item-info" href="selezionaDocumento.php" style="color:black">Attiva un documento in un Sito</a>
                <a class="list-group-item list-group-item-info" href="selezionaDizionario.php" style="color:black">Attiva un dizionario in un documento</a>
                <a class="list-group-item list-group-item-info" href="disattivaDocumento.php" style="color:black">Disattiva un documento da un Sito</a>
                <a class="list-group-item list-group-item-info" href="disattivaDizionario.php" style="color:black">Disattiva un dizionario da un documento</a>
           		<a class="list-group-item list-group-item-info" href="eliminaUser.php" style="color:black">Elimina un utente </a>   
                <a class="list-group-item list-group-item-info" href="eliminaSito.php" style="color:black">Elimina un Sito </a>
            </ul>
        </div>
        <div class="col-auto">
          <a href="logout.php" class="btn btn-primary" id="logout">
            Logout <i class="fas fa-sign-out-alt"></i>
          </a>
        </div> 
    	 <?php include PREPATH.'page_builder/_footer.php';?>
    </body>
</html>

<?php
	session_start();
    //STABILISCE UNA CONNESSIONE CON IL DATABASE E 
    //RESTITUISCE IL CONTENUTO RICHIESTO
    require_once('database.php');
    $query = "SELECT Reazione1, Reazione2 FROM sito WHERE Indirizzo_sito = :tmp";
    $check = $pdo->prepare($query);
    $check->bindParam(':tmp', $_GET['sito'], PDO::PARAM_STR);
    $check->execute();
    while($result = $check->fetch(PDO::FETCH_ASSOC)){
    	echo $result['Reazione1'] . '::' . $result['Reazione2'];
	}
?>

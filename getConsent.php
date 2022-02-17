<?php
    //STABILISCE UNA CONNESSIONE CON IL DATABASE E 
    //RESTITUISCE IL CONTENUTO DEL FILE RICHIESTO
    require_once('database.php');
    $query = "SELECT Contenuto FROM documento WHERE Nome_documento = :doc";
    $check = $pdo->prepare($query);
    $check->bindParam(':doc', $_GET['docn'], PDO::PARAM_STR);
    $check->execute();
    $check->bindColumn(1, $blob, PDO::PARAM_LOB);
    while($check->fetch(PDO::FETCH_BOUND))
    echo $blob;
?>

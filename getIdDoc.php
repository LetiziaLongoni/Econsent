<?php
    session_start();
    //STABILISCE UNA CONNESSIONE CON IL DATABASE E 
    //SALVA IN UNA VARIABILE DI SESSIONE L'ID DEL DOCUMENTO RICHIESTO
    require_once('database.php');
    $queryIdDoc = "SELECT ID_documento FROM documento WHERE Nome_documento = :doc";
    $check = $pdo->prepare($queryIdDoc);
    $check->bindParam(':doc', $_GET['docn'], PDO::PARAM_STR);
    $check->execute();
    $check->bindColumn(1, $id, PDO::PARAM_LOB);
    while($check->fetch(PDO::FETCH_BOUND)){
    	$_SESSION['UserData']['ID_Documento'] = $id;
    }   
    
    $queryDiz = "SELECT id_dizionario FROM dizionario_attivo WHERE id_documento = :doc AND attivo = 1";
    $checkd = $pdo->prepare($queryDiz);
    $checkd->bindParam(':doc', $id, PDO::PARAM_STR);
    $checkd->execute();
    $checkd->bindColumn(1, $diz, PDO::PARAM_LOB);
    while($checkd->fetch(PDO::FETCH_BOUND)){
    	$_SESSION['UserData']['ID_Dizionario'] = $diz;
    }
?>

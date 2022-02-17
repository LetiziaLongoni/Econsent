<?php
	session_start();
    //STABILISCE UNA CONNESSIONE CON IL DATABASE E 
    //RESTITUISCE IL CONTENUTO DEL FILE RICHIESTO
    require_once('database.php');
    if(isset($_GET['diz'])){
      $query = "SELECT Contenuto FROM dizionario WHERE ID_dizionario = :diz";
      $check = $pdo->prepare($query);
      $check->bindParam(':diz', $_GET['diz'], PDO::PARAM_STR);
      $check->execute();
      $check->bindColumn(1, $blob, PDO::PARAM_LOB);
      while($check->fetch(PDO::FETCH_BOUND))
      echo $blob;
    }else{
      $query = "SELECT Contenuto FROM dizionario WHERE Nome_dizionario = 'dizionario.json'";
      $check = $pdo->prepare($query);
      $check->bindParam(':diz', $_GET['diz'], PDO::PARAM_STR);
      $check->execute();
      $check->bindColumn(1, $blob, PDO::PARAM_LOB);
      while($check->fetch(PDO::FETCH_BOUND))
      echo $blob;
    }
?>
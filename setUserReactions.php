<?php
    session_start();
    //STABILISCE UNA CONNESSIONE CON IL DATABASE E 
    //SALVA LE EMAIL DEGLI USER ADDETTI ALLE REAZIONI
    
    require_once('database.php');
    
    if($_GET['r1'] == '0'){
    	$_SESSION['UserData']['UserReazione1'] = 0;
    }
    else{
      $queryR1 = "SELECT email_user FROM user WHERE ID_user = :id1";
      $check = $pdo->prepare($queryR1);
      $check->bindParam(':id1', $_GET['r1'], PDO::PARAM_STR);
      $check->execute();
      $check->bindColumn(1, $email1, PDO::PARAM_STR);
      while($check->fetch(PDO::FETCH_BOUND)){
          $_SESSION['UserData']['UserReazione1'] = $email1;
      }
    }
    
    if($_GET['r2'] == '0'){
    	$_SESSION['UserData']['UserReazione2'] = 0;
    }
    else{
      $queryR2 = "SELECT email_user FROM user WHERE ID_user = :id2";
      $check2 = $pdo->prepare($queryR2);
      $check2->bindParam(':id2', $_GET['r2'], PDO::PARAM_STR);
      $check2->execute();
      $check2->bindColumn(1, $email2, PDO::PARAM_STR);
      while($check2->fetch(PDO::FETCH_BOUND)){
          $_SESSION['UserData']['UserReazione2'] = $email2;
      }
    }  
    
    $_SESSION['UserData']['Memory'] = $_GET['mem'];

?>

    

<?php 
    session_start();
    define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php"); 
    
    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 1)){
        header("location:login.php");
        exit;
    }
    
    if(isset($_SESSION['UserData']['Nome_Documento']))
        $_SESSION['UserData']['Nome_Documento']=null; 
    if(isset($_SESSION['UserData']['ID_Documento']))
        $_SESSION['UserData']['ID_Documento']=null;
    if(isset($_SESSION['Modify']))
        $_SESSION['Modify']=null;

    
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
                <a class="list-group-item list-group-item-info" href="modificaDatiUser.php" style="color:black">Modifica i dati personali </a>
                <a class="list-group-item list-group-item-info" href="index.php"style="color:black">Compila un documento </a>
                <a class="list-group-item list-group-item-info" href="mostraCompilazioni.php"style="color:black">Visualizza le compilazioni effettuate </a>
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

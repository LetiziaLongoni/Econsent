<?php 
    session_start();
    if(!isset($_SESSION['UserData']['Email'])){
        header("location:login.php");
        exit;
    }elseif($_SESSION['UserData']['Admin'] == 1){
        header("location:login.php");
        exit;
    }
    require_once('database.php'); 
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
            
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
            
            tr:nth-child(even) {
              	background-color: #c6c6c6;
            }
            
            tr:nth-child(odd) {
              	background-color: #ddeeee;
            }

        </style>
    </head>

    <body>         
        <div class = "container">
            <h1 class="display-4">Elenco compilazioni </h1>
            <table>
            	<tr>
                	<th>Documento</th>
                    <th>Data</th>
                </tr>
          		<?php
                	$queryUserId = "SELECT ID_user FROM user WHERE email_user = :email";
                    $check = $pdo->prepare($queryUserId);
                    $check->bindParam(':email', $_SESSION['UserData']['Email'], PDO::PARAM_STR);        
                    $check->execute();
                    $id = $check->fetch(PDO::FETCH_ASSOC);
                    
                    $queryCompilazioni = "SELECT documento.Nome_documento, compilazioni.Data_compilazione FROM compilazioni JOIN documento on compilazioni.id_documento = documento.ID_documento
                    					  WHERE compilazioni.id_user = :idUser";
                    $checkCompilazioni = $pdo->prepare($queryCompilazioni);
                    $checkCompilazioni->bindParam(':idUser', $id['ID_user'], PDO::PARAM_STR);
                    $checkCompilazioni->execute();
                    while($comp = $checkCompilazioni->fetch(PDO::FETCH_ASSOC)){
                        echo "<tr>";
                        echo "<td>" . $comp["Nome_documento"] . "</td>";
                        echo "<td>" . $comp["Data_compilazione"] . "</td>";
                        echo "</tr>";
                    }
                 ?>   
             </table>
        	
        </div>  
        <a href="logout.php" class="btn btn-primary" id="logout">
            Logout <i class="fas fa-sign-out-alt"></i>
        </a>
        <a href="turnBackUser.php" class="btn btn-primary" id="turnBack">
            Indice 
        </a>

        <?php include PREPATH.'page_builder/_footer.php';?>

    </body>
</html>
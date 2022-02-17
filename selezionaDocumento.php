<?php 
    session_start();
    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }
    require_once('database.php');

    if (isset($_POST['Submit'])) {

        $docName = isset($_POST['Doc']) ? $_POST['Doc'] : '';
        if(empty($docName)){
            $msg = "Nessun file selezionato";
        }else{
            $_SESSION['UserData']['Nome_Documento'] = $docName;
            $controlQuery = "SELECT ID_documento FROM documento WHERE Nome_documento = :nome";
            $check = $pdo->prepare($controlQuery);
            $check->bindParam(':nome', $docName, PDO::PARAM_STR);   
            $check->execute();
            $check->bindColumn(1, $id, PDO::PARAM_STR);
            while($check->fetch(PDO::FETCH_BOUND))
                $_SESSION['UserData']['ID_Documento'] = $id;
            header('Location:assegnazioneSitoDocumento.php');
            exit;
            
        }  
        $_POST = null;
    }
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>MANAGE DOCS</title>
		<?php define("PREPATH", "");
            require_once(PREPATH."page_builder/_header.php");
        ?>
    	
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
            <h1 class="display-3">Seleziona il documento che vuoi attivare</h1>
            <form action="" method="post">
                <div class = 'form-group'>
                    <?php if(!empty($msg)){?>    
                        <div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            <strong><i class="fas fa-exclamation-triangle"></i>&emsp; <?php echo $msg ?> </strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php } ?>          
                    <div class="col-auto">
                        <label><strong>Documenti presenti nel database:</strong></label>              
                        <div class="input-group mb-2">

                            <select class="form-control" id="Select1" name="Doc" >
                                <option value="" disabled selected hidden>Selezionare il documento che si vuole attivare</option>
                                <?php     
                                    $querydoc = "SELECT Nome_documento FROM documento";
                                    $checkdoc = $pdo->prepare($querydoc);
                                    $checkdoc->execute();
                                    while($doc = $checkdoc->fetch(PDO::FETCH_ASSOC)){
                                        echo '<option>' . $doc['Nome_documento'] . '</option>';
                                    }  
                                ?>
                            </select>
                        </div>        
                    </div>
                </div> 
                <div class="col-auto" align="center">
                    <button type="submit" class="btn btn-primary" id="Submit" name="Submit">
                            Prosegui
                    </button>
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

    </body>
</html>
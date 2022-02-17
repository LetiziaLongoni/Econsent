<?php 
    session_start();
    if((!isset($_SESSION['UserData']['Email'])) || ($_SESSION['UserData']['Admin'] == 0)){
        header("location:login.php");
        exit;
    }
    require_once('database.php');

    if (isset($_POST['Submit'])) {
        $txtName = isset($_FILES['dictionaryToUpload']['name']) ? $_FILES['dictionaryToUpload']['name'] : '';
        if(empty($txtName)){
            $msg = "Selezionare il dizionario che si vuole caricare";
        }else{
        	$ext = pathinfo($txtName, PATHINFO_EXTENSION);
            if($ext !== 'txt' && $ext !== 'json'){
            	$msg = "selezionare un file .txt oppure .json";
			}else{
                $controlQuery = "SELECT Nome_dizionario FROM dizionario WHERE Nome_dizionario = :nome";
                $check = $pdo->prepare($controlQuery);
                $check->bindParam(':nome', $txtName, PDO::PARAM_STR);   
                $check->execute();
                $doc = $check->fetchAll(PDO::FETCH_ASSOC);
                if(count($doc)>0){
                    //DAFINIRE
                    $msg = "Dizionario già presente!";
                }elseif($_FILES['dictionaryToUpload']['size'] > 16000000){
                    $msg = "Dimensione massima disponibile nel database per ogni dizionario = 16MB";
                }else{
                    $dataUpload = date("Y-m-d H:i:s");
                    $txtContent = file_get_contents($_FILES['dictionaryToUpload']['tmp_name']);

                    $insertQuery = "INSERT into dizionario(Nome_dizionario, Data_inserimento, Contenuto) VALUES (:nome, :dataUpload, :contenuto)";
                    $check = $pdo->prepare($insertQuery);
                    $check->bindParam(':dataUpload', $dataUpload, pdo::PARAM_STR);  
                    $check->bindParam(':contenuto', $txtContent, PDO::PARAM_STR);
                    $check->bindParam(':nome', $txtName, PDO::PARAM_STR);        
                    $check->execute();

                    if ($check->rowCount() > 0) {
                        $msg = 'Dizionario caricato con successo';
                        $queryIdDic = "SELECT ID_dizionario FROM dizionario WHERE Nome_dizionario = :nome";
                        $check = $pdo->prepare($queryIdDic);
                        $check->bindParam(':nome', $txtName, PDO::PARAM_STR);        
                        $check->execute();
                        $check->bindColumn(1, $id, PDO::PARAM_STR);
                        while($check->fetch(PDO::FETCH_BOUND))
                            $_SESSION['UserData']['ID_Dizionario'] = $id;
                        header('Location:assegnazioneDocumentoDizionario.php');
                        exit;
                    } else {
                        $msg = 'Problemi con l\'inserimento del dizionario %s';
                    }
                }
            }
        }
        
    }
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>MANAGE DICTIONARY</title>
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
            <h1 class="display-4">Selezionare un dizionario da caricare nel database</h1>
            <?php if(!empty($msg)){?>    
                <div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i>&emsp; <?php echo $msg ?> </strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php } ?> 
            <div class="form-group col-auto ml-5 mr-5 mt-5 mb-0">
                <form action="" method="post" enctype="multipart/form-data">
                    <label class="mb-3 mylabel" for="dictionaryToUpload"><strong>Selezionare un file di estensione TXT o JSON</strong></label>
                    <div class="input-group mb-4">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                        <input type="file" class="file" name="dictionaryToUpload" id="dictionaryToUpload" accept="text/plain,application/JSON">
                        <input type="text" class="form-control input-lg path" disabled>

                        <div class="input-group-btn">
                            <button id="search" type="button" class="browse btn btn-info form-control input-lg">
                                <i class="fas fa-folder-open"></i> Sfoglia
                            </button>
                        </div>
                    </div>
                    <div class="col-auto" align="center">
                        <button type="submit" class="btn btn-primary" id="Submit" name="Submit">
                                Carica
                        </button>
                    </div>
                </form>
            </div>           
        </div>

        <a href="logout.php" class="btn btn-primary" id="logout">
            Logout <i class="fas fa-sign-out-alt"></i>
        </a>
        <a href="turnBack.php" class="btn btn-primary" id="turnBack">
            Indice 
        </a>

        <?php include PREPATH.'page_builder/_footer.php';?>
        
        <script type="text/javascript">
            //FUNZIONE CHE, AL CLICK SUL BOTTONE SFOGLIA, ATTIVA L'INPUT FILE
            $(document).on('click', '.browse', function(){
                var file = $(this).parent().parent().parent().find('.file');
                file.trigger('click');
            });   
            //FUNZIONE CHE OGNI VOLTA CHE SELEZIONI UN FILE, NE MOSTRA IL TITOLO A SCHERMO
            $(document).on('change', '.file', function(){
                $(this).parent().find('.path').val($(this).val().replace(/C:\\fakepath\\/i, ''));
                $('#submit').attr('disabled', false);
            });
        </script>
    </body>
</html>

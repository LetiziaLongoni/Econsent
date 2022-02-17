<?php session_start();
    require_once('database.php');

    if(!isset($_SESSION['UserData']['Email'])){
      header("location:login.php");
      exit;
    }elseif($_SESSION['UserData']['Admin'] == 0){
        header("location:index.php");
        exit;
    }

    if(isset($_POST['Modify'])){
		$sitoMod = isset($_POST['Sito']) ? substr($_POST['Sito'], strpos($_POST['Sito'],': ')+2) : '';

        if (empty($sitoMod)) {
            $msg = 'Nessuna sito selezionato';
        }else {
            $queryCheckSito = "SELECT * FROM sito WHERE Indirizzo_sito = :modify_sito";
            $check = $pdo->prepare($queryCheckSito);
            $check->bindParam(':modify_sito', $sitoMod, PDO::PARAM_STR);
            $check->execute();
            $sito = $check->fetch(PDO::FETCH_ASSOC);
                
            if (!$sito) {
                $msg = "Sito non trovato";
            }
            else{
                $message = $sito['Indirizzo_sito'];
				header('location:modificaDatiSito.php?id='.$message);
        	}
        }
    }
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ADMIN PAGE</title>
		<?php define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php") ?>
    	
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
            <h1 class ="display-4"> Modifica dati sito </h1>
            <form action="" method="post">
                <div class = "form-group">

                    <?php if(isset($msg)){?>
                      <div class="col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert">
                          <strong><i class="fas fa-exclamation-triangle"></i>&emsp; <?php echo $msg ?> </strong>-&emsp;Riprovare
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
					<?php } $msg = null; ?>
                    <div class="col-auto">
                    	<label><strong>Elenco siti</strong></label>        
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-user-md"></i>
                                </div>
                            </div>
                            <select class="form-control" name="Sito" >
                                <option value="" disabled selected hidden>Selezionare il sito da modificare</option>
                                <?php   
                                    $querysiti = "SELECT Nome_sito, Indirizzo_sito FROM sito WHERE Nome_sito <> 'admin' AND Nome_sito <> 'disattivo'";
                                    $checksiti = $pdo->prepare($querysiti);
                                    $checksiti->execute();
                                    while($sito = $checksiti->fetch(PDO::FETCH_ASSOC)){
                                        echo '<option>' . $sito['Nome_sito'] . ' : ' . $sito['Indirizzo_sito'] . '</option>';
                                    }  
                                ?>
                            </select>
                        </div>
                    </div>

                <div class="col-auto" align="center">
	  				<button type="submit" class="btn btn-primary" id="Submit" name="Modify">
							Prosegui
					</button>
				</div>
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
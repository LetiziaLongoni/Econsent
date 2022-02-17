<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>INOLTRO CONSENSO INFORMATO</title>
    <?php define("PREPATH", "");
    require_once(PREPATH."page_builder/_header.php");
    session_start();
    require_once('database.php');
    if(!isset($_SESSION['UserData']['Email'])){
      header("location:login.php");
      exit;
    }    
    $Email = $_SESSION['UserData']['Email'];
    $UserReazione1 = $_SESSION['UserData']['UserReazione1'];
    $UserReazione2 = $_SESSION['UserData']['UserReazione2'];
    $idDocumento = $_SESSION['UserData']['ID_Documento'];
    ?>


  </head>
  <body>

    <?php 
      //libreria fpdf
      /*require('fpdf.php');
		
      //creazione pdf
      $pdf = new FPDF();
      $pdf->AddPage();
      $pdf->SetFont('Arial','B',16);
      $pdf->Cell(40,10,'Hello World!',1);
      $pdf->Cell(60,10,'Powered by FPDF.',1,1,'C');
      $pdf->Output();
    
      $r1 = $_POST['reazione1'];
      $r2 = $_POST['reazione2'];*/

      //da salvare nel db 
      //(se l'opzione apposita è stata selezionata, altrimenti salvo una stringa vuota)
      if($_SESSION['UserData']['Memory'] != 1)
          $stringa = "";
      else{
          $jsonString = $_POST['prova'];
          $array = json_decode($jsonString);
          $stringa = implode("\n", $array);
      }
      //da inviare per mail
      $s = $_POST['jsonReport'];

      //preparo html del report da inviare per mail
      $s = str_replace("{\"readabilityIndexes\":{","{<br><h5>Redability Indexes</h5>{",$s);
      $s = str_replace("\"title\":[","<br><h5>Title</h5>[",$s);
      $s = str_replace("\"footer\":","<br><h5>Footer</h5>",$s);
      $s = str_replace("\"sections\":[","<br><h5>Sections</h5>[",$s);
      $s = str_replace("\"accepted\":","<h5>Accepted</h5>",$s);
      $s = str_replace("\"title\":","title : ",$s);
      $s = str_replace("\"gulpease\":","gulpease : ",$s);
      $s = str_replace("\"costaCabitza\":","costaCabitza : ",$s);
      $s = str_replace("\"education\":","<br></div></div></div><br><h5>Livello di Istruzione</h5>",$s);
      $s = str_replace("\"fearful\":","<br><h5>Quanto é preoccupato?</h5>",$s);
      $s = str_replace("\"confused\":","<br><h5>Quanto ha capito?</h5>",$s);
      $s = str_replace("[","<div style='padding: 10px; background-color: #dbfcfc;'>",$s);
      $s = str_replace("]","</div>",$s);
      $s = str_replace("\"paragraphs\":","<div style='padding: 20px; background-color: #b6dbdb;'><b><u>paragraphs</u></b> : ",$s);
      $s = str_replace("}},","</div>",$s);
      $s = str_replace("\"id\":","<br><i>id</i> : ",$s);
      $s = str_replace("\"text\":","<br><i>text</i> : ",$s);
      $s = str_replace("\"readabilityIndexes\":","<br><i>readabilityIndexes</i> : ",$s);
      $s = str_replace(",\"",",<br>\"",$s);
      $s = str_replace("},","<hr>",$s);
      $s = str_replace("{<br>","<br>",$s);
      $s = str_replace("<hr><br>\"reactions\":","<br><i>reactions</i> : ",$s);
      $s = str_replace("{","",$s);
      $s = str_replace("}","",$s);
      $s = str_replace(">,<","><",$s);

      $s = "<html><body><div style='padding: 10px; background-color: #eff2f2'><h3>Report finale Consenso Informato v2.0</h3>". $s ."</div></body></html>";

      //data e ora compilazione
      $dataResponse = date("Y-m-d H:i:s");
      //id del medico in servizio
      $querySelectId = "SELECT ID_user FROM user WHERE email_user = :email";    
      $check = $pdo->prepare($querySelectId);
      $check->bindParam(':email', $Email, PDO::PARAM_STR);
      $check->execute();
      $prova = $check->fetch(PDO::FETCH_ASSOC); 
      $User = $prova['ID_user'];

      //inserisco i dati nel DB
      $queryInsertBody = "INSERT INTO compilazioni (id_documento, id_user, Data_compilazione, Campi) VALUES (:id_doc, :id_user, :data_compilazione, :body)";
      $check = $pdo->prepare($queryInsertBody);
      $check->bindParam(':id_doc', $idDocumento, PDO::PARAM_STR);
      $check->bindParam(':id_user', $User, PDO::PARAM_STR);
      $check->bindParam(':data_compilazione', $dataResponse, PDO::PARAM_STR);
      $check->bindParam(':body', $stringa, PDO::PARAM_LOB);
      $check->execute();

      if($r1 == false && $r2 == false){
          //invio email allo User che ha fatto compilare il documento
          $to = $Email;
          $subject = "Stage - Consenso Informato v3.0";
          $headers = "From: stage.econsent@gmail.com \r\n";
          $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

          //$to ricevente
          //$subject = soggetto email
          //$s = contenuto email
          mail($to,$subject,$s,$headers);
       }elseif($r1 == true || $r2 == true){
          if($r1 == true){
              //invio email allo User selezionato per occuparsi di Reazione1
              $to = $UserReazione1;
              $subject = "Stage - Consenso Informato v3.0";
              $headers = "From: stage.econsent@gmail.com \r\n";
              $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

              mail($to,$subject,$s,$headers);
           }
           if($r2 == true){
              //invio email allo User selezionato per occuparsi di Reazione2
              $to = $UserReazione2;
              $subject = "Stage - Consenso Informato v3.0";
              $headers = "From: stage.econsent@gmail.com \r\n";
              $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

              mail($to,$subject,$s,$headers);
           }
        }
     
     

    ?>


    <!-- pulsante logout -->
    <a href="logout.php" class="btn btn-primary" id="logout">
        Logout <i class="fas fa-sign-out-alt"></i>
    </a>
    <a href="turnBackUser.php" class="btn btn-primary" id="turnBack">
        Indice 
    </a>


    <div class="container">

      <div class="row">
        <div class="col-12 text-center" >
          <h1 class="display-4">DOCUMENTO INOLTRATO</h1>
          <script type="text/javascript">
           console.log(JSON.parse(localStorage.getItem("json")));
          </script>
        </div>
      </div>

      <div id="progressBar" class="col-12 mt-4">
        <div class="progress" style="height: 30px;">
          <div id="slider-progress" class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
          <div class="progress-bar-title">100% Completato</div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-12 text-center" >
          <span class="font-weight-bold">Grazie per la disponibilità!<br>Ora è possibile riconsegnare il dispositivo allo staff medico.</span>
        </div>
      </div>

    </div>

    <?php include PREPATH.'page_builder/_footer.php';?>

  </body>
</html
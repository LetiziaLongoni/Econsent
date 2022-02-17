<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>UPLOAD FILE</title>
        <?php 
            session_start();
            if(!isset($_SESSION['UserData']['Email'])|| ($_SESSION['UserData']['Admin'] == 1)){
                header("location:login.php");
                exit;
            }
            define("PREPATH", "");
            require_once(PREPATH."page_builder/_header.php");

            require_once('database.php');
            
            $Email =  $_SESSION['UserData']['Email'];
            $sitoAppartenenza = $_SESSION['UserData']['Sito'];
        ?>

        <style type="text/css">

            .container{
                padding: 2%;
                margin-bottom: 10%;
            }

            .myLabel{
                float: left;
            }

            #recentFile{
                width: 100%;
            }

            .popover{
                max-width: 500px;
            }

        </style>
    </head>

    <body>

        <div class="container">
            <h1 class="display-4">Compilazione documento</h1>

            <div class="col-auto">
                <label class="myLabel"><strong>Selezionare lo User che si occuperà della Reazione: "<?php echo $_SESSION['UserData']['Reazione1'] ?>"</strong></label>              
                <div class="input-group mb-2">
                    <select class="form-control" id="Select1" name="User1" >
                        <?php
                            $queryUser1 = "SELECT Nome_user, Cognome_user, email_user, ID_user FROM user
                                            WHERE Sito_appartenenza = :sito AND Reazione1 = 1";
                            $checkUser1 = $pdo->prepare($queryUser1);
                            $checkUser1->bindParam(':sito', $sitoAppartenenza, PDO::PARAM_STR);
                            $checkUser1->execute();
                            while($user1 = $checkUser1->fetch(PDO::FETCH_ASSOC)){
                                echo '<option value = "'. $user1['ID_user'] .'">' . $user1['Nome_user'] . ' ' . $user1['Cognome_user'] . ' - ' . $user1['email_user'] . '</option>';
                            }  
                        ?>
                    </select>
                </div>        
            </div>
            <div class="col-auto">
                <label class="myLabel"><strong>Selezionare lo User che si occuperà della Reazione: "<?php echo $_SESSION['UserData']['Reazione2'] ?>"</strong></label>              
                <div class="input-group mb-2">
                    <select class="form-control" id="Select2" name="User2" >
                        <?php
                            $queryUser2 = "SELECT Nome_user, Cognome_user, email_user, ID_user FROM user
                                                      WHERE Sito_appartenenza = :sito AND Reazione2 = 1";
                            $checkUser2 = $pdo->prepare($queryUser2);
                            $checkUser2->bindParam(':sito', $sitoAppartenenza, PDO::PARAM_STR);
                            $checkUser2->execute();
                            while($user2 = $checkUser2->fetch(PDO::FETCH_ASSOC)){
                              echo '<option value = "'. $user2['ID_user'] .'">' . $user2['Nome_user'] . ' ' . $user2['Cognome_user'] . ' - ' . $user2['email_user'] . '</option>';
                            }  
                        ?>
                    </select>
                </div>        
            </div>
            <div class="col-auto">
                <label class="myLabel"><strong>Si desidera salvare il contenuto del consenso? </strong></label>              
                <div class="input-group mb-2">
                	<div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="save" name="memory" value="1">
                      <label class="form-check-label" for="memory">
                        Si
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="nosave" name="memory" value="0" checked>
                      <label class="form-check-label" for="memory">
                        No
                      </label>
                    </div>
                </div>        
            </div>
            <div class="col-auto">            
                <!-- DOCUMENTI PRESENTI NEL DATABASE -->
                <label class="myLabel"><strong>Selezionare il documento che si vuole compilare: </strong></label>              
                <div class="btn-group-vertical text-center" id="recentFile">
                    <input type="text" id="mySearch" onkeyup="fileSearch()" placeholder="&#xF002; Ricerca ...">
                    <?php   //SCRIPT PER CARICARE TUTTI I DOCUMENTI DAL DB                  
                        $querydocs = "SELECT d.Nome_Documento FROM documento d
                                        JOIN documento_attivo da ON da.ID_Documento = d.ID_Documento
                                        JOIN sito s ON s.Indirizzo_Sito = da.Indirizzo_Sito
                                        WHERE da.Attivo = 1 and da.Indirizzo_Sito = :sito";
                        $check = $pdo->prepare($querydocs);
                        $check->bindParam(':sito', $_SESSION['UserData']['Sito'], PDO::PARAM_STR);
                        $check->execute();
                        
                        //PER OGNI DOCUMENTO VIENE CREATO UN BUTTON
                        //CLICCANDONE UNO SI APRE IL MODAL CHE RICHIEDE IL LIVELLO D'ISTRUZIONE
                        while($doc = $check->fetch(PDO::FETCH_ASSOC)){
                            echo ("<button class='fileCI btn btn-block btn-info' style='float:left;' name='". $doc['Nome_Documento'] ."' onclick='patientEducation(this.name, false)'><i class='fas fa-file-alt'></i>&nbsp;&nbsp;&nbsp;". $doc['Nome_Documento'] . "</button>");
                        }
                    ?>
                </div>
                <div id="notFound" class="text-muted text-center">
                    0 risultati di ricerca
                </div>   
            </div>       
        </div>

        <!-- pulsante logout  -->
        <a href="logout.php" class="btn btn-primary" id="logout">
        Logout <i class="fas fa-sign-out-alt"></i>
        </a>
        
         <!-- pulsante per tornare all'indice  -->
        <a href="turnBackUser.php" class="btn btn-primary" id="turnBack">
            Indice 
        </a>

        <!-- Modal per selezionare livello istruzione
            compare dopo aver selezionato un file     -->
        <div class="modal fade" id="education" role="dialog">
            <div class="modal-dialog" style="max-width: 600px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Apertura Consenso Informato</h4>
                    </div>
                    <div class="modal-body text-justify pl-5 pr-5" style="font-weight: normal;">
                        <div id="errorMessage">
                        </div>
                        <h4 align="center">- Livello di istruzione -</h4>
                        <hr>
                        <span style="font-weight: 600;">Qual è il livello di Istruzione di chi legge e compila il Consenso Informato? <span style="color: red;">*</span></span>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" id="scuola" name="education" value="0">
                            <label class="form-check-label" for="scuola">
                                Scuola dell'obbligo
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="diploma" name="education" value="0.5">
                            <label class="form-check-label" for="diploma">
                                Diploma
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="laurea" name="education" value="1">
                            <label class="form-check-label" for="laurea">
                                Laurea
                            </label>
                        </div>
                        <div class="modal-footer">
                            <!-- spiegazione indice Costa-Cabitza-->
                            <span class="text-muted text-justify p-2" style="font-size: 12px;">(Informazione necessaria per indicare la facilità di lettura del testo del Consenso Informato secondo l'indice di leggibilità Costa-Cabitza)
                                <a tabindex='0' class="popoverMedico" data-toggle='popover' data-trigger='focus' data-placement='right' data-html="true" title="Cos'è l'Indice Costa-Cabitza?" data-content="Si tratta di una funzionalità che analizza il testo del Consenso e mostra la facilità di lettura/comprensione di ciascun paragrafo.<hr> L'indice Costa-Cabitza addiziona:
                                <br>- il Livello di Istruzione, nell’ottica che ad un minor livello di istruzione corrisponde una maggiore difficoltà nella comprensione;
                                <br>- variabili legate alla semantica del testo, vale a dire il numero di parole difficili e il numero di parole specialistiche (parole mediche) secondo l’assunto che maggiore è il numero di queste parole, più il processo di comprensione viene ostacolato;
                                <br>- una variabile più sintattica, secondo la quale maggiore è il numero di periodi rispetto al numero di parole totali, più le frasi sono brevi e quindi la comprensione è velocizzata.">
                                    <i class="far fa-question-circle" aria-hidden="true"></i>
                                </a>
                            </span>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                            <button type="button" class="btn btn-success" onclick="checkEducationSelection()" style="width: 100px">Conferma</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include PREPATH.'page_builder/_footer.php';?>

        <script type="text/javascript">

            $('[data-toggle="popover"]').popover();
            $('#notFound').hide();

            // ***
            // INUTILI ORA CHE ABBIAMO TOLTO L'UPLOAD DEI FILE
            // ***
            /*FUNZIONE CHE, AL CLICK SUL BOTTONE "Sfoglia", ATTIVA L'INPUT FILE
            $(document).on('click', '.browse', function(){
                var file = $(this).parent().parent().parent().find('.file');
                file.trigger('click');
            });   
            //FUNZIONE CHE OGNI VOLTA CHE SELEZIONI UN FILE, NE MOSTRA IL TITOLO  A SCHERMO
            $(document).on('change', '.file', function(){
                $(this).parent().find('.path').val($(this).val().replace(/C:\\fakepath\\/i, ''));
                $('#submit').attr('disabled', false);
            });*/

            //funzione di supporto per la ricerca tra file
            //mostra solo i documenti il cui nome contiene la stringa inserita dall'utente
            //case INsensitive
            function fileSearch() {
                var filter, group, files;
                filter = $('#mySearch').val().toUpperCase();
                files = $('.fileCI');
                var countDisplay = 0;
                for (var i = 0; i < files.length; i++) {
                    if (files[i].innerText.toUpperCase().indexOf(filter) > -1) {
                        files[i].style.display = "";
                        countDisplay++;
                    } else
                        files[i].style.display = "none";
                }
                if (countDisplay == 0)
                    $('#notFound').show();
                else
                    $('#notFound').hide();
            }

            var fileName = "";  //NOME FILE SCELTO
            var fileId = 0;
            var upload;     //false se il file è scelto dal DB (al momento risulta sempre FALSE)
                            //true se il file viene caricato sul momento
            var education;  //0 - 0.5 - 1 in base al livello di studio scelto
            var memory;     //1 se si desidera salvare il file sul db, 0 altrimenti
            
            //Funzione chiamata dopo aver selezionato un file
            //Mostra il modal che chiede il livello di istruzione
            function patientEducation(file, bool){            	
                fileName = file; 
                var idrequest = new XMLHttpRequest(); 

                //questa chiamata salva gli ID del documento e del dizionario ad esso associato
                //in due variabili di sessione che vengono usate nelle pagine successive
                idrequest.open("GET", "getIdDoc.php?docn="+fileName, false);  //FALSE = CHIAMATA SINCRONA (obsoleta)
                                                                            //TRUE = ASINCRONA 
                idrequest.send();  //send() = invio richiesta al server
                
                $('.modal-title').html("Apertura Consenso Informato: <i>" + file + "</i>");
                $('#education').modal('show');
                upload = bool;
            }

            //Funzione chiamata dopo aver scelto il livello 
            //di istruzione e aver premuto conferma
            function checkEducationSelection(){
              education = $('input[name=education]:checked').val();
              memory = $('input[name=memory]:checked').val();
              if(education == undefined){ 
                $('#errorMessage').html(
                  '<div class=" text-center col-auto alert alert-danger alert-dismissible fade show mt-3" role="alert"><strong align="center"><i class="fas fa-exclamation-triangle"></i>&emsp;Attenzione!&emsp;</strong>E\' obbligatorio selezionare una tra le tre opzioni proposte. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
                );
              }
              else {
                $('#education').modal('hide'); //chiusura modal
                $('#edu').val(fileName);     
                if(!upload){
                  var user1,user2;
                  
                  if($('#Select1').val()==null)
                  	user1 = 0;
                  else 
                  	user1 = $('#Select1').val();
                    
                  if($('#Select2').val()==null)
                  	user2 = 0;
                  else 
                  	user2 = $('#Select2').val();
				  
                  var postRequest = new XMLHttpRequest();  
                  postRequest.open("GET", "setUserReactions.php?r1="+user1+"&r2="+user2+"&mem="+memory, false); 
                  postRequest.send();
                  //APERTURA PAGINA BUILDER E EFFETTIVA COSTRUZIONE DEI PARAGRAFI DEL CONSENSO
                  window.location.href = "<?=PREPATH?>builder.php?redirectTo=" + fileName +"&education=" + education;
                }
              }
            }

        </script>

    </body>
</html>

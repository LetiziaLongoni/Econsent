//FUNZIONE CHE PRENDE IN INPUT IL NOME DI UN FILE TXT E
//RITORNA IL CONTENUTO DEL FILE DIVISO IN PARAGRAFI
txtToText = function(docname){
  var txtFile = new XMLHttpRequest(); 

  //open(...) = preparazione della richiesta al server
  txtFile.open("GET", "getConsent.php?docn="+docname, false);  //FALSE = CHIAMATA SINCRONA (obsoleta)
                                                               //TRUE = ASINCRONA (NON FUNZIONA!!!)
  txtFile.overrideMimeType('text/xml; charset=iso-8859-1');
  var lines;
  txtFile.onreadystatechange = function() {
    if (txtFile.readyState === 4) {  // Makes sure the document is ready to parse.
      if (txtFile.status === 200) {  // Makes sure the file was found.
        lines = txtFile.responseText.split("\n"); // Will separate each line into an array
      }
    }
  }
  txtFile.send();  //send() = invio richiesta al server
  return lines;
}

//FUNZIONE CHE PRENDE IN INPUT UN ARRAY DI PARAGRAFI
textToParagraph = function(text){
  var paragraphs = [];      //array di paragrafi 
  var title = [];           //array di titoli da mettere in alto
  var footer = "";          
  var index = 0;
  var s = "";

  //CICLO TUTTI I PARAGRAFI
  for (var i = 0; i < text.length; i++) {
    if(text[i]){ //CONTROLLO DA TESTARE!!!!!!
      if (text[i].length <=1) {
        var pIndex = paragraphs[index];
        pIndex = pIndex[pIndex.length-1];
        if (!pIndex.match(/[A-Z\_\.\:\s\@\]\)]/))
          paragraphs[index] += ".";
        index++;
      }
      else{
        if(text[i].charAt(0) == '$'){
          title.push(text[i].substring(1, text[i].length - 2));
        }
        else if(text[i].charAt(0) == '#'){
          footer = text[i].substring(1, text[i].length - 2);
          }
        else {
          if(paragraphs[index] === undefined){
            paragraphs[index] = text[i];
          } else{
            paragraphs[index] += (" " + text[i]);
          }
        }
      }
    }
  }
  return new InformedConsent(title, footer, paragraphs);
}

htmlToText = function(docname){
  let paragraphs = [];
  let footer = "";
  let title = [];
  $.ajax({
    async: false,
    url: "getConsent.php?docn="+docname,
    success: function(result){
      let txt = '';
      $('#pdf-text').html(result);
      $('.WordSection1').children().each(function(){
        if(this.tagName == 'H1')
          title.push(this.innerText);
        else if(this.tagName == 'P' && this.innerText.charCodeAt(0) != 160)
          paragraphs.push(this.innerText);
        else if (this.tagName == 'H2')
          footer = this.innerText;
        else if(this.tagName == 'H3')
          txt += ('@'+this.innerText+'@ ');
        else if (this.innerText.charCodeAt(0) == 160 && txt != '') {
          paragraphs.push(txt);
          txt = '';
        }
      });
    },
    error: function(error){
      console.log(error);
    }
  });
  $('#pdf-text').empty();

  return new InformedConsent(title, footer, paragraphs);
}
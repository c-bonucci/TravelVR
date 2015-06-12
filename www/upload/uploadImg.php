<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <link type="text/css" rel="stylesheet" href="../css/materialize.min.css"  media="screen,projection"/>
    <script type="text/javascript" src="../js/materialize.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>


    <title>TravelVR</title>
    <?php 
$title = $_GET['title'];
$type = $_GET['type'];
$desc = $_GET['desc'];
$lat = $_GET['lat'];
$lng = $_GET['lng'];
    ?>
    <script>
      function _(el) {
        return document.getElementById(el);
      }

      var validExt = [".jpg", ".jpeg", ".bmp", ".png"];    
      function validate() {
        var img = _("file1");
        var fileName = img.value;
        if (fileName.length > 0)
        {
          for (var j = 0; j < validExt.length; j++) {
            var ext = validExt[j];
            if (fileName.substr(fileName.length - ext.length, ext.length).toLowerCase() == ext.toLowerCase()) {
              return true;
            }
          }
        }
        alert("Inserire un file immagine valido!");
        return false;
      }

      function uploadFile() {
        if(!validate())
          return false;
        _("PB").style.visibility = "visible"
        var file = _("file1").files[0];
        var formData = new FormData();
        formData.append("file1", file);
        var ajax = new XMLHttpRequest();

        ajax.upload.addEventListener("progress", progressHandler, false);
        ajax.addEventListener("load", completeHandler, false);
        ajax.addEventListener("error", errorHandler, false);
        ajax.addEventListener("abort", abortHandler, false);

        ajax.open("POST","upload.php?title=<?php echo $title; ?>&type=<?php echo $type; ?>&desc=<?php echo $desc; ?>&lat=<?php echo $lat; ?>&lng=<?php echo $lng; ?>");
        ajax.send(formData);
      }

      function progressHandler(event) {
        var percent = (event.loaded / event.total) * 100;
        _("progressBar").style.width = Math.round(percent) + "%";
        _("status").innerHTML = Math.round(percent) + " % ";
        if(percent === 100)
          document.getElementById("btns").style.visibility = "visible";
      }
      function completeHandler(event) {
        _("status").innerHTML = event.target.responseText;
        _("progressBar").value = 0;
      }
      function errorHandler(event) {
        _("status").innerHTML = "Upload failed";
      }
      function abortHandler(event) {
        _("status").innerHTML = "Upload aborted";
      }

    </script>

  </head>
  <body>
    <img src="../logos/photosphere.png" style=" float:right; width:125px; height:125px; margin-right:10px;">

    <center><h4 style="font-family: 'Lobster', cursive;">Adesso scegli l'immagine da caricare!</h4></center>

    <br><br>


    <form id="prog" enctype="multipart/form-data" method="post" action="">
      <div class="file-field input-field">
        <input class="file-path validate" type="text"/>
        <div style="margin-left:10px;" class="btn">
          <span>File</span>
          <input type="file" name="file1" id="file1" />
        </div>
      </div>

      <center><!--uploadFile();-->
        <a class="waves-effect waves-light btn" onclick='uploadFile();'><i class="mdi-file-file-upload right"></i>Carica!</a>
        <h4 id="status" style="margin-left:10px;"></h4>
      </center>
      <div id="PB" style="visibility: hidden;">
        <div class="progress" style="height:20px; ">
          <div class="determinate" id="progressBar"  style="width: 0%"></div>
        </div>
        </form>
      </div>
    <div id="btns" style="visibility: hidden;">
      <a class="waves-effect waves-light btn left" href="../index.php"><i class="mdi-hardware-keyboard-arrow-left left"></i>Torna alla Mappa</a>
      <a class="waves-effect waves-light btn right" href="uploadForm.html"><i class="mdi-navigation-refresh right"></i>Carica un'altra immagine</a>
    </div>
  </body>

</html>
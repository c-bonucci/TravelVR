<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
  <head>
    <title> PHP upload test page </title>
    <link rel="stylesheet" href="style.css">
    <?php
require_once('php_image_magician.php');
    ?>
  </head>
  <body>
    <?php

error_reporting(2047);
if (isset($_POST["invio"])) {
  $percorso = "../VR/img/";
  if (is_uploaded_file($_FILES['file1']['tmp_name'])) {
    if (move_uploaded_file($_FILES['file1']['tmp_name'], $percorso.$_FILES['file1']['name'])) {
      echo 'Nome file: <b>'.$_FILES['file1']['name'].'</b><br>';
      echo 'MIME Type: <b>'.$_FILES['file1']['type'].'</b><br>';
      echo 'Dimensione: <b>'.$_FILES['file1']['size'].'</b> byte<br>';
      echo '======================<br>';
      echo 'File caricato correttamente<br><br>';
      echo '<a href="upload.php">carica un altro file</a>';

      $magicianObj = new imageLib($percorso.$_FILES['file1']['name']);
      $magicianObj -> resizeImage(1894, 947, 'crop');
      $magicianObj -> saveImage($percorso.$_FILES['file1']['name']);
    } else {
      echo "si è verificato un errore durante l'upload: ".$_FILES["file1"]["error"];
    }
  } else {
    echo "si è verificato un errore durante l'upload: ".$_FILES["file1"]["error"];
  }
} else {
    ?>
    <form enctype="multipart/form-data" method="post" action="" name="uploadform">
      seleziona il file da caricare sul server: 
      <br>
      <input type="file" name="file1" size="50">
      <br>
      <input type="submit" value="invia" name="invio">
      <br>
      <img id="img">
    </form>

    <?php
}
    ?>
  </body>
  </html>
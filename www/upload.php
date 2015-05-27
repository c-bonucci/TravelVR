<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
  <head>
    <meta name="viewport" content="initial-scale=1,  minimal-ui">


    <title> PHP upload test page </title>
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>


    <?php
require_once('php_image_magician.php');
    ?>
  </head>
  <style>
    a {
      color:white;
    }
  </style>
  <body>
    <?php

error_reporting(2047);
if (isset($_POST["invio"])) {
  $percorso = "VR/img/";
  if (is_uploaded_file($_FILES['file1']['tmp_name'])) 
  {
    if (move_uploaded_file($_FILES['file1']['tmp_name'], $percorso.$_FILES['file1']['name'])) {
      echo '<i class="large center mdi-action-done"></i><br>';
      echo '<h5 class="center">Immagine caricata con successo!</h5><br><br>';
      echo '<h5>Nome: <b>'.$_FILES['file1']['name'].'</b></h5><br>';
      echo '<h5>Tipo: <b>'.$_FILES['file1']['type'].'</b></h5><br>';
      echo '<h5>Dimensione: <b>'.($_FILES['file1']['size'])/100000 .' MB</b></h5><br>';
      echo '<b>===============================================</b><br>';
      echo '<a href="index.php"><button class="btn waves-effect waves-light">Torna alla mappa<i class="mdi-navigation-arrow-back left"></i></button></a>';
      echo '<a href="upload.php"><button class="btn waves-effect waves-light">Carica un\' altra immagine<i class="mdi-navigation-refresh right"></i></button></a>';

      $magicianObj = new imageLib($percorso.$_FILES['file1']['name']);
      $magicianObj -> resizeImage(1894, 947, 'crop');
      $magicianObj -> saveImage($percorso.$_FILES['file1']['name']);
      
      $con= new mysqli("localhost","root","oculus","TravelVR");
      // Check connection
      if ($con->connect_error)
      {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
      }
      else
      {
        $res = $con->prepare("INSERT INTO `Immagini`(`id`, `titolo`, `tipologia`, `latitude`, `longitude`, `img`) 
        VALUES ('null','" . $_POST['title'] . "','" . $_POST['type'] . "','" . $_POST['lat'] . "', '" . $_POST['long'] . "', '" . $_FILES['file1']['name'] ."')");
        $res->execute();
      } 
    } else {
      echo "si è verificato un errore durante l'upload: ".$_FILES["file1"]["error"];
    }
  } else {
    echo "si è verificato un errore durante l'upload: ".$_FILES["file1"]["error"];
  }
} else {
    ?>
    <form enctype="multipart/form-data" method="post" name="uploadform">
      <a href="index.php" style="margin-top:5px; margin-left:5px;" class="btn waves-effect waves-light"><i class="mdi-navigation-arrow-back left"></i></a> <br><br>
      <div class="row">
        <form class="col s12">
          <div class="row">
            <div class="input-field col s12">
              <input name="title" type="text" class="validate" length="10"maxlength="10">
              <label for="first_name">Titolo</label>
            </div>
            <div class="input-field col s12">
              <input name="type" type="text" class="validate">
              <label for="type">Tipologia</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <textarea id="textarea" class="materialize-textarea" length="120" maxlength="120"></textarea>
              <label for="textarea1">Descrizione</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <textarea name="lat" class="materialize-textarea" length="120" maxlength="120"></textarea>
              <label for="lat">lat</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <textarea name="long" class="materialize-textarea" length="120" maxlength="120"></textarea>
              <label for="long">long</label>
            </div>
          </div>
          <div class="row">
            <div class="file-field input-field ">
              <input class="file-path validate s12" type="text"/>
              <div class="btn">
                Scegli
                <input type="file" name="file1"/>
              </div>
            </div>
          </div>
          <br><br>
          <button class="btn waves-effect waves-light" type="submit" name="invio">Carica<i class="mdi-file-file-upload right"></i></button>
        </form>
      </div>

    </form>

    <?php
}
    ?>
  </body>
  </html>

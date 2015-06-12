<?php

$fileName =$_FILES["file1"]["name"];
$fileTmpLoc =$_FILES["file1"]["tmp_name"];
$fileType =$_FILES["file1"]["type"];
$fileSize =$_FILES["file1"]["size"];
$fileError =$_FILES["file1"]["error"];

$title = $_GET['title'];
$type = $_GET['type'];
$desc = $_GET['desc'];
$lat = $_GET['lat'];
$lng = $_GET['lng'];

if(!$fileTmpLoc) {
  echo "ERRORE <br> Scegliere prima un'immagine";
  exit();
}

if(move_uploaded_file($fileTmpLoc, "../VR/img/" . $fileName)){
  echo "Inserimento di $fileName completato";
  /* **** RESIZE IMAGE **** */
  // File and new size
  $filename = $fileName;
  // Get new sizes
  list($width, $height) = getimagesize($filename);
  //figure out height ratios
  $newwidth = 1894;
  $newheight = ($newwidth/$width) * $height;
  // Load
  $thumb = imagecreatetruecolor($newwidth, $newheight);
  $source = imagecreatefromjpeg($filename);
  // Resize
  imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
  // Save the image as 'mynewpic.jpg'
  imagejpeg($thumb, $filename,85);
  // Free up memory
  imagedestroy($thumb);

  /* **** DATABASE CONNECTION AND RECORD INSERT **** */
  $con= new mysqli("localhost","root","oculus","TravelVR");
  // Check connection
  if ($con->connect_error)
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  else
  {
    $res = $con->prepare("INSERT INTO Immagini(titolo, tipologia, descrizione, latitude, longitude, img)
                          VALUES (?, ?, ?, ?, ?, ?)");
    $res->bind_param("sssdds", $title, $type, $desc, $lat, $lng, $filename);
    $res->execute();
  }

}
else
  echo "Errore nell'inserimento";
?>
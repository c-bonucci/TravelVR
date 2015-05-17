<?php
$con=mysqli_connect("localhost","root","oculus","TravelVR");
// Check connection
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$res = mysqli_query($con,"SELECT img FROM Immagini WHERE id = 1");

while ($row = mysqli_fetch_assoc($res)) {
  print_r ('<img src="http://www.travelvr.me/VR/img/' . $row['img'] . '">');
}

mysqli_close($con);
?> 
<!DOCTYPE html>
<html>
  <head>
    <!-- Render the page for a better view on mobile phones -->
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <link rel="stylesheet" href="style.css">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

    <!-- Google Maps API & Places -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>

  </head>


  <script type="text/javascript">
    var map;
    //Main function on load
    function initialize() {
      //Google Maps API with ROADMAP type and default UI disabled
      map=new google.maps.Map(document.getElementById("map-canvas"), {mapTypeId:google.maps.MapTypeId.ROADMAP,                                                                                     disableDefaultUI: true,
                                                                     });

      //Marker icon 
      var image = {
        url: 'cardboard-marker.png',
        size: new google.maps.Size(50, 50),
        origin: new google.maps.Point(0,0),
        anchor: new google.maps.Point(25, 25)
      };
      //parte da modificare con php
      var marker = [];

      var m;

      function placeMarker(location) {
        if (m) {
          m.setPosition(location);
        } else {
          m = new google.maps.Marker({
            position: location,
            map: map,
            icon : image,
          });
        }
      }
      <?php

$con= new mysqli("localhost","root","oculus","TravelVR");
// Check connection
if ($con->connect_error)
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{

  $res = $con->prepare("SELECT id, titolo,tipologia, latitude, longitude, img 
                        FROM Immagini ");
  $res->execute();
  $res->bind_result($id,$titolo,$tipologia, $latitude, $longitude, $img);

  while ($res->fetch()){
    echo "marker.push(new google.maps.Marker({
        position:new google.maps.LatLng(" . $latitude . "," . $longitude . "),
        map:map,
        icon : image,
        id : ". $id .",
        titolo :'".$titolo."',
        img : '" . $img . "',
        tipologia: '" .$tipologia ."',
      }));";
  }
}
$con->close();
      ?>

      // Map bound fixer
      var defaultBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(43.909, 11.122),
        new google.maps.LatLng(43.481, 11.994));
      map.fitBounds(defaultBounds);

      // Places SearchBox positioning
      var input = /** @type {HTMLInputElement} */(
        document.getElementById('pac-input'));
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      // Geolocation button positioning
      var loc = document.getElementById('geoloc');
      map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(loc);

      // SearchBox variable declaration
      var searchBox = new google.maps.places.SearchBox(input);

      // Event places changed typing in SearchBox
      google.maps.event.addListener(searchBox, 'places_changed', function() {
        var places = searchBox.getPlaces();
        if (places.length == 0) {
          return;
        }

        // For each place, get the icon, place name, and location.
        var bounds = new google.maps.LatLngBounds();
        for (var i = 0, place; place = places[i]; i++) {
          var image = {
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(25, 25)
          };
          bounds.extend(place.geometry.location);
        }
        map.fitBounds(bounds);
      });

      //Click event on the map. Put a new marker (da mettere in pagina upload)
      google.maps.event.addListener(map, 'click', function(event) {
        //placeMarker(event.latLng);
        $("#footer").removeClass("visibile");

      });

      //Click event on markers
      marker.forEach(function(m){
        google.maps.event.addListener(m, 'click', function(event) {
          $("#footer").addClass("visibile");
          document.getElementById("titolo").innerHTML = m.titolo;
          document.getElementById("tipologia").innerHTML = m.tipologia;
          document.getElementById("img-vr").src="../VR/img/" + m.img;
          document.getElementById("link").href="../VR/vr2.php?id=" + m.id;
          /*map.setZoom(8);
          map.setCenter(m.getPosition());*/
        });
      })


      google.maps.event.addListener(map, 'bounds_changed', function() {
        var bounds = map.getBounds();
        searchBox.setBounds(bounds);
      });
    }


    //Geolocation function
    function UserPosition() {
      if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
          map.setCenter(initialLocation);
          map.setZoom(15);
        }, function() {
          alert("Questa app desidera utilizzare il GPS per rilevare la posizione. Abilitare la posizione fucking man");
        });
      }
    }
    google.maps.event.addDomListener(window, 'load', initialize);
  </script>

  <body>
    <!-- Places SearchBox  -->
    <input id="pac-input" class="controls" type="text" placeholder="Search Box">
    <!-- Map -->
    <div id="map-canvas"></div>
    <!-- Geolocalization button -->
    <a id="geoloc" onclick="UserPosition()" class="btn-floating btn-large waves-effect waves-light indigo accent-3 z-depth-5"><i class="mdi-device-gps-fixed"></i></a>
    <!-- Info footer that appears clicking on map's markers -->
    <footer id="footer">
      <div class="subfooter">
        <img src="normal-marker.png">
        <div class="info">
          <h4 id="titolo"></h4>
          <p id="tipologia"></p>
        </div>    
      </div>
      <a id="link"><img id="img-vr" class="img-vr"></a>
    </footer>

  </body>
</html>
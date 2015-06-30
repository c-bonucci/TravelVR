<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">

    <!-- Render the page for a better view on mobile phones -->
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    <link rel="shortcut icon" href="favicon.ico" />

    <link rel="stylesheet" href="css/style.css">
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

    <!-- Google Maps API & Places -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&&libraries=places"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="js/materialize.min.js"></script>

    <title>TravelVR</title>
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
        url: 'logos/cardboard-marker.png',
        size: new google.maps.Size(50, 50),
        origin: new google.maps.Point(0,0),
        anchor: new google.maps.Point(25, 25)
      };
      // Marker's array
      var marker = [];

      var m;

      //This function puts a marker on the given location with icon properties
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
      //php part that loads all markerson the map from the db
      <?php
// DB connection
$con= new mysqli("localhost","root","oculus","TravelVR");
// Check connection
if ($con->connect_error)
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else
{
  //Query
  $res = $con->prepare("SELECT id, titolo,tipologia,descrizione, latitude, longitude, img 
                        FROM Immagini ");
  $res->execute();
  //Binds all the query results with this variables (caution with the order!)
  $res->bind_result($id,$titolo,$tipologia,$descrizione, $latitude, $longitude, $img);

  while ($res->fetch()){
    echo "marker.push(new google.maps.Marker({
        position:new google.maps.LatLng(" . $latitude . "," . $longitude . "),
        map:map,
        icon : image,
        id : ". $id .",
        titolo :'".$titolo."',
        tipologia: '" .$tipologia ."',
        descrizione: '" . $descrizione ."',
        img : '" . $img . "',
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
      var input = document.getElementById('pac-input');
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      // Geolocation button positioning
      var loc = document.getElementById('geoloc');
      map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(loc);

      //Insert a new image
      var add = document.getElementById('addImg');
      map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(add);

      var auth = document.getElementById('author');
      map.controls[google.maps.ControlPosition.TOP_RIGHT].push(auth);


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

      //Click event on the map. Closes the info footer and remove SearchBox focus
      google.maps.event.addListener(map, 'click', function(event) {
        $("#footer").removeClass("visibile");
        $("#pac-input").blur();
      });

      //Click event on markers
      marker.forEach(function(m){
        google.maps.event.addListener(m, 'click', function(event) {
          //Open the footer to display features
          $("#footer").addClass("visibile");
          //Building the footer with marker's options
          document.getElementById("titolo").innerHTML = m.titolo;
          document.getElementById("tipologia").innerHTML = m.tipologia;
          document.getElementById("img-vr").src="VR/img/" + m.img;
          document.getElementById("link").href="VR/vr.php?id=" + m.id;
          document.getElementById("desc").innerHTML = m.descrizione;

        });
      })

      //Bounds Changed event... when navigating the map
      google.maps.event.addListener(map, 'bounds_changed', function() {
        var bounds = map.getBounds();
        searchBox.setBounds(bounds);
      });
      
      
    }


    //Geolocation function
    function UserPosition() {
      if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var userLocation = new google.maps.LatLng(
              position.coords.latitude,position.coords.longitude);
          map.setCenter(userLocation);
          var m = new google.maps.Marker({
            position: userLocation,
            map: map,
          });
          map.setZoom(11);
        }, function() {
          alert("TravelVR desidera utilizzare il GPS per rilevare la posizione. " + 
                "Abilitare il servizio di localizzazione e ricaricare la pagina!");
        });
      }
    }

    //on window's load calling inizialize()
    google.maps.event.addDomListener(window, 'load', initialize);
  </script>

  <body>
    <!-- Places SearchBox  -->
    <input id="pac-input" class="controls" type="text" placeholder="Ricerca">
    <!-- Author link -->
    <!--<a href="#openModal"><img class="z-depth-5" id="author" src="logos/bonu_small.jpg"></a>-->
    <a id="author" class="btn-floating btn-medium waves-effect waves-light modal-trigger z-depth-5" href="#modal1"><img class="z-depth-5" src="logos/bonu_small.jpg" onclick="$('#modal1').openModal();"></a>
    <div id="modal1" class="modal">
      <div class="modal-content">
        <center>
          <div style="margin-left:auto; margin-right:auto;">
            <h4 style="color:black;">Claudio Bonucci</h4>
            <img src="logos/bonu.jpg" style="width:200px; height:200px; border-radius: 100px;">
            <h6 style="color:black;">Quest webapp è stata realizzata come progetto finale per il mio esame di Stato <br><br> claudio.bonucci96@gmail.com</h6>
          </div>
        </center>
      </div>
    </div>
    <!-- Map -->
    <div id="map-canvas"></div>
    <!-- Geolocalization button -->
    <a id="geoloc" onclick="UserPosition()" class="btn-floating btn-large waves-effect waves-light indigo accent-3 z-depth-5"><i class="mdi-device-gps-fixed"></i></a>
    <!-- Insert button -->
    <a id="addImg" class="btn-floating btn-large waves-effect waves-light red z-depth-5" href="upload/uploadForm.html"><i class="mdi-content-add"></i></a>
    <!-- Info footer that appears clicking on map's markers -->
    <footer id="footer">
      <div class="subfooter">
        <img src="logos/normal-marker.png">
        <div class="info">
          <h4 id="titolo"></h4>
          <p id="tipologia"></p>
        </div>    
      </div>
      <div id="palo">
        <a id="link"><img id="img-vr" class="img-vr"></a>
        <p id="desc" style="color:black;"></p>
      </div>
    </footer>

  </body>
</html>
<html>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no "/>

  <script src="js/three.min.js"></script>
  <script src="js/OrbitControls.js"></script>	
  <script src="js/Detector.js"></script>
  <script src="js/DeviceOrientationControls.js"></script>
  <link rel="stylesheet" href="css/visione.css">
  <head>
    <title>TravelVR</title>
  </head>

  <?php
if(isset($_GET['id']))
{
  $con= new mysqli("localhost","root","oculus","TravelVR");
  // Check connection
  if ($con->connect_error)
  {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  else
  {
    
    $res = $con->prepare("SELECT img FROM Immagini WHERE id = ?");
    $res->bind_param("i", intval($_GET['id']));    
    $res->execute();
    $res->bind_result($img);
    
    while ($res->fetch()){
      echo "<script> var load = function() {" .
      "document.getElementById(\"spheresx\"); ".
      "document.getElementById(\"spheredx\"); ".
      "createRenderer(\"spheresx\", \"img/" . $img . "\");" .
      "createRenderer(\"spheredx\", \"img/" . $img . "\");" .
      "} </script>";
    }
  }
  $con->close();
}
//else non esiste l'id --> pagina di errore
  ?> 
  <body onclick="load()">
    <div class = "container" id ="container">
      <div class="meta" id="spheresx" style="background: blue;"></div>
      <div class="meta" id="spheredx" style="background: yellow;"></div>
      <img src="img/mask.png" style="width:100%; height:100%; position:absolute; top:0; left:0">
      <img src="img/fullscreen-512.png" id="bottone">
    </div>
  </body>

  <script>
    var Fullscreen = {
      launch: function(element) {
        if(element.requestFullscreen) {
          element.requestFullscreen();
        } else if(element.mozRequestFullScreen) {
          element.mozRequestFullScreen();
        } else if(element.webkitRequestFullscreen) {
          element.webkitRequestFullscreen();
        } else if(element.msRequestFullscreen) {
          element.msRequestFullscreen();
        }
      },
      exit: function() {
        if(document.exitFullscreen) {
          document.exitFullscreen();
        } else if(document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if(document.webkitExitFullscreen) {
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
          document.msExitFullscreen();
        }
      }
    };

    document.getElementById('bottone').addEventListener('click', function (event) {
      event.preventDefault();
      Fullscreen.launch(document.documentElement);
      screen.orientation.lock("landscape");
    });



    var createRenderer = function(divID, texture) {
      var webglEl = document.getElementById(divID);

      var width  = window.innerWidth,
          height = window.innerHeight;

      var scene = new THREE.Scene();

      var camera = new THREE.PerspectiveCamera(75, width / height, 1, 1000);
      camera.position.x = 0.1;

      var renderer = Detector.webgl ? new THREE.WebGLRenderer() : new THREE.CanvasRenderer();
      renderer.setSize(width, height);

      var sphere = new THREE.Mesh(
        new THREE.SphereGeometry(100, 20, 20),
        new THREE.MeshBasicMaterial({
          map: THREE.ImageUtils.loadTexture(texture)
        })
      );
      sphere.scale.x = -1;
      scene.add(sphere);

      var controls = new THREE.DeviceOrientationControls(camera);
      controls.noPan = true;
      controls.noZoom = true; 
      controls.autoRotate = false;
      //controls.autoRotateSpeed = 0.5;

      webglEl.appendChild(renderer.domElement);

      render();


      function render() {
        controls.update();
        requestAnimationFrame(render);
        renderer.render(scene, camera);
      }

      function onMouseWheel(event) {
        event.preventDefault();

        if (event.wheelDeltaY) { // WebKit
          camera.fov -= event.wheelDeltaY * 0.05;
        } else if (event.wheelDelta) { 	// Opera / IE9
          camera.fov -= event.wheelDelta * 0.05;
        } else if (event.detail) { // Firefox
          camera.fov += event.detail * 1.0;
        }

        camera.fov = Math.max(40, Math.min(100, camera.fov));
        camera.updateProjectionMatrix();
      }

      document.addEventListener('mousewheel', onMouseWheel, false);
      document.addEventListener('DOMMouseScroll', onMouseWheel, false);

    }
  </script>
</html>
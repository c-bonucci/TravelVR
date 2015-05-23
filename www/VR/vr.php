<html>
  <!-- Render the page for a better view on mobile phones -->
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
  //Connection to the DB
  $con= new mysqli("localhost","root","oculus","TravelVR");
  // Check connection
  if ($con->connect_error)
  {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  else
  {
    // Query --> I want the image with the id i pass it
    $res = $con->prepare("SELECT img FROM Immagini WHERE id = ?");
    // Check if the id value is an integer
    $res->bind_param("i", intval($_GET['id']));
    // Execute the query
    $res->execute();
    // Save the query result in a variable
    $res->bind_result($img);
    // While i have tuples
    while ($res->fetch()){
      //Declaration of the load function that puts the image in the VR way
      echo "<script> var load = function() {" .
        "document.getElementById(\"spheresx\"); ".
        "document.getElementById(\"spheredx\"); ".
        "createRenderer(\"spheresx\", \"img/" . $img . "\");" .
        "createRenderer(\"spheredx\", \"img/" . $img . "\");" .
        "} </script>";
    }
  }
  //Close the connection
  $con->close();
}
//else non esiste l'id --> pagina di errore
  ?> 
  <body onclick="load()">
    <div class = "container" id ="container">
     <!-- Left div where i'll load the image -->
      <div class="meta" id="spheresx" style="background: blue;"></div>
      <!-- Right div where i'll load the image -->
      <div class="meta" id="spheredx" style="background: yellow;"></div>
      <!-- VR mask image -->
      <img src="img/mask.png" style="width:100%; height:100%; position:absolute; top:0; left:0">
      <!-- Fullscreen button -->
      <img src="img/fullscreen-512.png" id="bottone">
    </div>
  </body>

  
  <script>
    //Function fullscreen for various environment
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

    //Event click on Fullscreen button --> Launches the fullscreen mode and lock the device on landscape mode
    document.getElementById('bottone').addEventListener('click', function (event) {
      event.preventDefault();
      Fullscreen.launch(document.documentElement);
      screen.orientation.lock("landscape");
    });


    //Function createRenderer 
    var createRenderer = function(divID, texture) {
      var webglEl = document.getElementById(divID);

      var width  = window.innerWidth,
          height = window.innerHeight;
      //To display anything with Three.js we need three things: A scene, a camera, and a renderer so we can render the scene with the camera.
      var scene = new THREE.Scene();
      //There are a few types of cameras. For this use we need the PerspectiveCamera(field of view, aspect ratio, near and far clipping plane)
      var camera = new THREE.PerspectiveCamera(75, width / height, 1, 1000);
      //The position of the camera. (Zoom)
      camera.position.x = 0.1;
      //The renderer is what we need to render the images in our divs. The main render is WebGLRenderer but for users who have browsers that doesn't support WebGL, we use CanvasRenderer
      var renderer = Detector.webgl ? new THREE.WebGLRenderer() : new THREE.CanvasRenderer();
      renderer.setSize(width, height);
      //Next we need to mesh the images
      var sphere = new THREE.Mesh(
        // the first argument of THREE.SphereGeometry is the radius, the second argument is
        // the segmentsWidth and the third argument is the segmentsHeight.  Increasing the 
        // segmentsWidth and segmentsHeight will yield a more perfect circle, but will degrade
        // rendering performance
        new THREE.SphereGeometry(100, 20, 20),
        new THREE.MeshBasicMaterial({
          //loadTexture --> Loads the image in the scene
          map: THREE.ImageUtils.loadTexture(texture)
        })
      );
      sphere.scale.x = -1;
      //Add the sphere to the scene
      scene.add(sphere);

      //The 
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
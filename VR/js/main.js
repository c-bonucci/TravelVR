var fullscreen = function() {
    document.body.webkitRequestFullScreen();
    createRenderer("spheresx", "../img/photo.jpg");
    createRenderer("spheredx", "../img/photo.jpg");
}

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

    document.addEventListener('mousewheel', onMouseWheel, true);
    document.addEventListener('DOMMouseScroll', onMouseWheel, false);

}
import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { FBXLoader } from 'three/addons/loaders/FBXLoader.js';

let genWidth, genHeight;

if (window.innerWidth <= 750) {
  genWidth = 200;
  genHeight = 200;
} else {
  genWidth = 140;
  genHeight = 280;
}

const canvas = document.getElementById('canvas');
const renderizador = new THREE.WebGLRenderer({
  canvas: canvas,
  antialias: true,
  alpha: true
});
renderizador.setSize(genWidth, genHeight);
renderizador.shadowMap.enabled = true;
renderizador.shadowMap.type = THREE.PCFSoftShadowMap;

const escena = new THREE.Scene();

// Cámara
const camara = new THREE.PerspectiveCamera(60, genWidth / genHeight, 0.1, 1000);
camara.position.set(0, 50, 150);

// Luces (más claras)
const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.2);
hemiLight.position.set(0, 200, 0);
escena.add(hemiLight);

const dirLight = new THREE.DirectionalLight(0xffffff, 1);
dirLight.position.set(50, 100, 100);
dirLight.castShadow = true;
dirLight.shadow.mapSize.width = 2048;
dirLight.shadow.mapSize.height = 2048;
dirLight.shadow.camera.top = 200;
dirLight.shadow.camera.bottom = -200;
dirLight.shadow.camera.left = -200;
dirLight.shadow.camera.right = 200;
escena.add(dirLight);

// Controles
const controles = new OrbitControls(camara, canvas);
controles.enablePan = false;
controles.enableZoom = false;

// Loader FBX
const loader = new FBXLoader();
loader.load('./res/3Dmodels/PizzaSteve.fbx', (object) => {
  object.traverse((child) => {
    if (child.isMesh) {
      child.castShadow = true;
      child.receiveShadow = true;
      // Evitar transparencias raras
      if (child.material) {
        child.material.transparent = false;
        child.material.opacity = 1;
      }
    }
  });

  // Centrar el modelo en el canvas
  const box = new THREE.Box3().setFromObject(object);
  const center = box.getCenter(new THREE.Vector3());
  object.position.sub(center); // mueve el centro del objeto al (0,0,0)

  escena.add(object);

  // Animación del modelo
  function animateObject() {
    requestAnimationFrame(animateObject);
    object.rotation.y += 0.002;
  }
  animateObject();
});

// Render loop
function animar() {
  requestAnimationFrame(animar);
  controles.update();
  renderizador.render(escena, camara);
}
animar();

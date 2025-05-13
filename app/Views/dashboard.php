<div id="map" style="width: 100%; height: 80vh;"></div>

<!-- Modal 3D Viewer -->
<div id="modal3D" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    z-index: 9999;">

    <button onclick="close3D()" style="
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: white;
        border: none;
        font-size: 20px;
        padding: 10px;
        cursor: pointer;
        z-index: 10000;">❌</button>

    <div id="viewer3D" style="width: 80%; height: 80%; background: #000;"></div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>


<script>
    const map = L.map('map').setView([-8.294014625833483, 114.30673598813148], 17);

    // Layer satelit
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri'
    }).addTo(map);

    // Contoh data, seolah berasal dari database
    const markers = [{
            name: "Gedung Prabu Tawangalun",
            coords: [-8.294371437514862, 114.30567584508485],
            desain: "gedungA.glb"
        },
        {
            name: "Hotel Poliwangi",
            coords: [-8.293274882825061, 114.30682170808706],
            desain: "gedungB.glb"
        },
        {
            name: "Gedung Kuliah Terpadu",
            coords: [-8.292446616676521, 114.3057493479127],
            desain: "gedungC.glb"
        },
        {
            name: "Perpustakaan Poliwangi",
            coords: [-8.295537691873253, 114.30674387841817],
            desain: "gedungD.glb"
        },
        {
            name: "Aula Poliwangi",
            coords: [-8.29546070879696, 114.30727612044645],
            desain: "aula_poliwangi.glb"
        }
    ];

    markers.forEach(marker => {
        L.marker(marker.coords).addTo(map)
            .bindPopup(`<b>${marker.name}</b><br><button onclick="show3D('desain3d/${marker.desain}')">Lihat Gedung</button>`);
    });

    let renderer, scene, camera, loader, model, controls;

    function show3D(url) {
        document.getElementById('modal3D').style.display = 'flex';

        const container = document.getElementById('viewer3D');
        container.innerHTML = ''; // clear previous model

        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 1, 5);

        renderer = new THREE.WebGLRenderer({
            antialias: true
        });
        renderer.setSize(container.clientWidth, container.clientHeight);
        container.appendChild(renderer.domElement);

        // Tambahkan kontrol orbit
        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;

        const light = new THREE.DirectionalLight(0xffffff, 1);
        light.position.set(1, 1, 1).normalize();
        scene.add(light);

        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);

        loader = new THREE.GLTFLoader();
        loader.load(url, function(gltf) {
            model = gltf.scene;
            scene.add(model);
        }, undefined, function(error) {
            console.error(error);
        });

        animate();
    }

    function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    }


    function close3D() {
        document.getElementById("modal3D").style.display = "none";
    }
</script>
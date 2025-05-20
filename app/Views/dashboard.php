<?= $this->extend('template') ?>
<?= $this->section('content') ?>
<!-- PETA -->
<div id="map" style="width: 100%; height: 80vh;"></div>

<!-- MODAL 3D -->
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

<!-- LEGENDA PETA -->
<div id="legend" style="
    position: absolute;
    bottom: 40px;
    left: 40px;
    background: rgba(255, 255, 255, 0.9);
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    z-index: 1000;">
    <strong>Legenda:</strong>
    <!-- <div style="display: flex; align-items: center; margin-top: 5px;">
        <div style="width: 15px; height: 15px; background: green; margin-right: 8px;"></div> Perangkat Aktif
    </div> -->
    <div style="display: flex; align-items: center; margin-top: 5px;">
        <img src="<?= base_url('aset/img/marker_biru.png'); ?>" width="15" height="15" style="margin-right: 8px;"> Lokasi Gedung
    </div>
    <div style="display: flex; align-items: center; margin-top: 5px;">
        <img src="<?= base_url('aset/img/ic_tower.png'); ?>" width="15" height="15" style="margin-right: 8px;"> Lokasi Menara
    </div>
</div>



<!-- LIBRARY -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

<style>
    .label-marker {
        position: absolute;
        color: white;
        font-size: 14px;
        font-weight: bold;
        pointer-events: none;
        text-shadow: 1px 1px 3px black;
        transform: translate(-50%, -100%);
        z-index: 10001;
    }
</style>

<script>
    const map = L.map('map').setView([-8.294014625833483, 114.30673598813148], 17);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri'
    }).addTo(map);

    const markers = [{
            name: "Gedung Prabu Tawangalun",
            coords: [-8.294371437514862, 114.30567584508485],
            desain: "gedungA.glb"
        },
        {
            name: "Hotel Poliwangi",
            coords: [-8.293315250171206, 114.30665188041469],
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
            desain: "perpus_poliwangi.glb"
        },
        {
            name: "Aula Poliwangi",
            coords: [-8.29546070879696, 114.30727612044645],
            desain: "aula_poliwangi8.glb"
        },
        {
            name: "Menara Peringatan",
            coords: [-8.293822242473611, 114.30650971355313],
            // desain: "aula_poliwangi7.glb"
        }
    ];

    markers.forEach(marker => {
        let icon = null;

        if (marker.name === "Menara Peringatan") {
            // Buat ikon khusus untuk Menara Peringatan
            icon = L.icon({
                iconUrl: '<?= base_url('aset/img/ic_tower.png'); ?>  ', // ganti dengan ikon khusus kamu
                iconSize: [32, 40],
                iconAnchor: [16, 40],
                popupAnchor: [0, -40]
            });
        }

        L.marker(marker.coords, icon ? {
                icon: icon
            } : {})
            .addTo(map)
            .setZIndexOffset(1000)
            .bindPopup(`<b>${marker.name}</b><br>${
            marker.desain ? `<button onclick="show3D('desain3d/${marker.desain}')">Lihat Gedung</button>` : ''
        }`);
    });

    let renderer, scene, camera, loader, model, controls;
    const animateLabelUpdates = [];

    function show3D(url) {
        document.getElementById('modal3D').style.display = 'flex';

        const container = document.getElementById('viewer3D');
        container.innerHTML = '';

        // Hapus label lama
        document.querySelectorAll('.label-marker').forEach(el => el.remove());
        animateLabelUpdates.length = 0;

        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 2, 8);

        renderer = new THREE.WebGLRenderer({
            antialias: true
        });
        renderer.setSize(container.clientWidth, container.clientHeight);
        container.appendChild(renderer.domElement);

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
            ubahKeWireframe(model);
            scene.add(model);
            tambahMarkerKeModel(model);
        }, undefined, function(error) {
            console.error("GLB Error:", error);
        });

        animate();
    }

    function ubahKeWireframe(object) {
        object.traverse(function(child) {
            if (child.isMesh) {
                child.material = new THREE.MeshBasicMaterial({
                    color: 0xffffff,
                    wireframe: true
                });
            }
        });
    }

    function tambahMarkerKeModel(model) {
        const dataMarker = [{
                indeks_3d: "r_aula2",
                status: "Hidup"
            },
            {
                indeks_3d: "r_perpus2",
                status: "Hidup"
            },
            {
                indeks_3d: "r_aula1",
                status: "Bahaya"
            }
        ];

        dataMarker.forEach(item => {
            const target = model.getObjectByName(item.indeks_3d);
            if (target) {
                const worldPos = new THREE.Vector3();
                target.getWorldPosition(worldPos);

                const warna = item.status === "Bahaya" ? 0xff0000 : item.status === "Mati" ? 0x555555 : 0x00ff00;

                const marker = new THREE.Mesh(
                    new THREE.SphereGeometry(0.2, 16, 16),
                    new THREE.MeshStandardMaterial({
                        color: warna
                    })
                );
                marker.position.copy(worldPos);
                marker.position.y += 0.5;
                scene.add(marker);

                tambahLabelTeks(item.indeks_3d, marker.position);
            } else {
                console.warn(`Ruangan "${item.indeks_3d}" tidak ditemukan.`);
            }
        });
    }

    function tambahLabelTeks(namaRuangan, posisi3D) {
        const label = document.createElement('div');
        label.className = 'label-marker';
        label.innerText = namaRuangan;
        document.body.appendChild(label);

        function updateLabelPosition() {
            const vector = posisi3D.clone().project(camera);
            const x = (vector.x * 0.5 + 0.5) * window.innerWidth;
            const y = (-vector.y * 0.5 + 0.5) * window.innerHeight;
            label.style.left = `${x}px`;
            label.style.top = `${y}px`;
        }

        animateLabelUpdates.push(updateLabelPosition);
    }

    function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
        animateLabelUpdates.forEach(fn => fn());
    }

    function close3D() {
        document.getElementById("modal3D").style.display = "none";
        if (renderer) {
            renderer.dispose();
            renderer.forceContextLoss();
            renderer.domElement = null;
            renderer = null;
        }
        // Hapus semua label dari DOM
        document.querySelectorAll('.label-marker').forEach(el => el.remove());
        animateLabelUpdates.length = 0;
    }
</script>
<?= $this->endSection() ?>
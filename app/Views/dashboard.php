<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<!-- PETA UTAMA -->
<div id="map" style="width: 100%; height: 80vh;"></div>

<!-- MODAL DENAH 2D -->
<div id="modal2D" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    z-index: 9999;
    ">
    <button onclick="close2D()" style="
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: white;
        border: none;
        font-size: 20px;
        padding: 10px;
        cursor: pointer;
        z-index: 10000;">❌</button>
    <div id="floorplanContainer" style="width: 80%; height: 80%; background: white; position: relative;">
        <!-- Denah 2D akan dimuat di sini -->
        <img id="floorplanImage" src="" alt="Denah Gedung" style="width: 100%; height: 100%; object-fit: contain;" />
        <div id="markersOnFloorplan" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
    </div>
</div>

<!-- LEGENDA PETA -->
<div id="legend" style="
        position: absolute;
        bottom: 80px;
        left: 30px; 
        background: rgba(255, 255, 255, 0.9);
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 14px;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        z-index: 1000;">
    <strong>Legenda:</strong>
    <div style="display: flex; align-items: center; margin-top: 5px;">
        <img src="<?= base_url('aset/img/marker_biru.png'); ?>" width="15" height="15" style="margin-right: 8px;"> Lokasi Gedung
    </div>
    <div style="display: flex; align-items: center; margin-top: 5px;">
        <img src="<?= base_url('aset/img/ic_tower.png'); ?>" width="15" height="15" style="margin-right: 8px;"> Lokasi Menara
    </div>
</div>

<!-- LIBRARY -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


<script>
    const map = L.map('map').setView([-8.294014625833483, 114.30673598813148], 17);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri'
    }).addTo(map);

    const markers = [{
            name: "Gedung Prabu Tawangalun",
            coords: [-8.294371437514862, 114.30567584508485],
            floorplan: "aula_poliwangi.png",
            devices: [{
                    name: "Sensor Aula 1",
                    xPercent: 10,
                    yPercent: 55,
                    status: "Hidup"
                },
                {
                    name: "Sensor Aula 2",
                    xPercent: 50,
                    yPercent: 60,
                    status: "Bahaya"
                },
            ]
        },
        {
            name: "Hotel Poliwangi",
            coords: [-8.293315250171206, 114.30665188041469],
            floorplan: "denah_gedungB.png",
            devices: [{
                name: "Sensor Lobby",
                xPercent: 30,
                yPercent: 30,
                status: "Mati"
            }, ]
        },
        {
            name: "Gedung Kuliah Terpadu",
            coords: [-8.292446616676521, 114.3057493479127],
            floorplan: "denah_gedungC.png",
            devices: []
        },
        {
            name: "Perpustakaan Poliwangi",
            coords: [-8.295537691873253, 114.30674387841817],
            floorplan: "denah_perpus_poliwangi.png",
            devices: []
        },
        {
            name: "Aula Poliwangi",
            coords: [-8.29546070879696, 114.30727612044645],
            floorplan: "aula_poliwangi.png",
            devices: []
        },
        {
            name: "Menara Peringatan",
            coords: [-8.293822242473611, 114.30650971355313],
            floorplan: null // tidak punya denah
        }
    ];

    markers.forEach(marker => {
        let icon = null;
        if (marker.name === "Menara Peringatan") {
            icon = L.icon({
                iconUrl: '<?= base_url('aset/img/ic_tower.png'); ?>',
                iconSize: [32, 40],
                iconAnchor: [16, 40],
                popupAnchor: [0, -40]
            });
        } else {
            icon = L.icon({
                iconUrl: '<?= base_url('aset/img/marker_biru.png'); ?>',
                iconSize: [25, 30],
                iconAnchor: [12, 30],
                popupAnchor: [0, -30]
            });
        }

        L.marker(marker.coords, {
                icon: icon
            })
            .addTo(map)
            .setZIndexOffset(1000)
            .bindPopup(`<b>${marker.name}</b><br>` + (marker.floorplan ? `<button onclick="show2D('${marker.floorplan}', ${markers.indexOf(marker)})">Lihat Denah</button>` : 'Tidak ada denah'));
    });

    function show2D(floorplanUrl, index) {
        const modal = document.getElementById('modal2D');
        const img = document.getElementById('floorplanImage');
        const container = document.getElementById('markersOnFloorplan');

        img.src = '<?= base_url('aset/denah/'); ?>' + floorplanUrl;

        // Kosongkan marker lama
        container.innerHTML = '';

        // Tampilkan marker perangkat di denah 2D dengan posisi relatif (%)
        const devices = markers[index].devices || [];

        devices.forEach(device => {
            const markerDiv = document.createElement('div');
            markerDiv.style.position = 'absolute';
            markerDiv.style.left = device.xPercent + '%';
            markerDiv.style.top = device.yPercent + '%';
            markerDiv.style.transform = 'translate(-50%, -50%)';
            markerDiv.style.width = '16px';
            markerDiv.style.height = '16px';
            markerDiv.style.borderRadius = '50%';
            markerDiv.style.border = '2px solid white';
            markerDiv.style.cursor = 'pointer';
            markerDiv.title = device.name + ' (' + device.status + ')';

            // Warna sesuai status
            if (device.status === 'Bahaya') {
                markerDiv.style.backgroundColor = 'red';
            } else if (device.status === 'Mati') {
                markerDiv.style.backgroundColor = 'gray';
            } else {
                markerDiv.style.backgroundColor = 'green';
            }

            container.appendChild(markerDiv);
        });

        modal.style.display = 'flex';
    }

    function close2D() {
        document.getElementById('modal2D').style.display = 'none';
    }
</script>

<?= $this->endSection() ?>
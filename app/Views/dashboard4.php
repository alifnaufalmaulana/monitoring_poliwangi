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
    <div style="width: 80%; height: 80%; background: white; position: relative; padding: 10px;">
        <h3 id="modalTitle">Denah Gedung</h3>

        <!-- Dropdown Lantai -->
        <select id="selectLantai" style="margin-bottom: 10px; width: 200px;">
            <option value="">Pilih Lantai</option>
        </select>

        <!-- Container Denah -->
        <div id="floorplanContainer" style="width: 100%; height: calc(100% - 80px); position: relative; border: 1px solid #ccc;">
            <img id="floorplanImage" src="" alt="Denah Gedung" style="width: 100%; height: 100%; object-fit: contain;" />
            <div id="markersOnFloorplan" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
        </div>
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
</div>

<!-- LIBRARY -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Pastikan baseURL sudah didefinisikan di template induk atau disini:
    const baseURL = '<?= base_url(); ?>/';

    // Data gedung dari controller ke view
    const gedungMarkers = <?= json_encode($gedung) ?>;

    const map = L.map('map').setView([-8.294, 114.3067], 17);

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri'
    }).addTo(map);

    gedungMarkers.forEach(g => {
        const marker = L.marker([parseFloat(g.latitude), parseFloat(g.longitude)], {
            icon: L.icon({
                iconUrl: '<?= base_url('aset/img/marker_biru.png'); ?>',
                iconSize: [25, 30],
                iconAnchor: [12, 30],
                popupAnchor: [0, -30]
            })
        }).addTo(map);

        marker.bindPopup(
            `<b>${g.nama_gedung}</b><br><button onclick="showDenah(${g.id_gedung}, '${g.nama_gedung}')">Lihat Denah</button>`
        );
    });

    // Modal dan denah
    const modal = document.getElementById('modal2D');
    const floorplanImage = document.getElementById('floorplanImage');
    const markersOnFloorplan = document.getElementById('markersOnFloorplan');
    const selectLantai = document.getElementById('selectLantai');
    const modalTitle = document.getElementById('modalTitle');

    let currentGedungId = null;
    let lantaiData = [];

    function showDenah(gedungId, gedungName) {
        currentGedungId = gedungId;
        modalTitle.textContent = `Denah Gedung: ${gedungName}`;
        floorplanImage.src = '';
        markersOnFloorplan.innerHTML = '';
        selectLantai.innerHTML = '<option value="">Pilih Lantai</option>';

        modal.style.display = 'flex';

        fetch(`${baseURL}api/lantai/${gedungId}`)
            .then(response => response.json())
            .then(data => {
                lantaiData = data;
                if (data.length === 0) {
                    alert('Data lantai tidak ditemukan untuk gedung ini.');
                    return;
                }
                data.forEach(l => {
                    const option = document.createElement('option');
                    option.value = l.id_lantai;
                    option.textContent = l.nama_lantai;
                    selectLantai.appendChild(option);
                });

                // Pilih lantai pertama otomatis
                selectLantai.value = data[0].id_lantai;
                selectLantai.dispatchEvent(new Event('change'));
            })
            .catch(() => {
                alert('Gagal mengambil data lantai.');
            });
    }


    // Saat lantai dipilih, tampilkan gambar denahnya
    selectLantai.addEventListener('change', function() {
        const lantaiId = this.value;
        if (!lantaiId) {
            floorplanImage.src = '';
            markersOnFloorplan.innerHTML = '';
            return;
        }
        const lantai = lantaiData.find(l => l.id_lantai == lantaiId); // pakai id_lantai
        if (!lantai) return;

        // Set gambar denah lantai
        floorplanImage.src = '<?= base_url('aset/denah/'); ?>' + lantai.denah; // pakai denah sesuai model
        markersOnFloorplan.innerHTML = '';

        // TODO: Bisa tambahkan marker perangkat untuk lantai ini jika ada
    });

    function close2D() {
        modal.style.display = 'none';
        floorplanImage.src = '';
        markersOnFloorplan.innerHTML = '';
        selectLantai.innerHTML = '<option value="">Pilih Lantai</option>';
    }
</script>

<?= $this->endSection() ?>
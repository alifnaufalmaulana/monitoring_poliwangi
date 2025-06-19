<?= $this->extend('template') ?>
<?= $this->section('content') ?>

<!-- PETA UTAMA -->
<div id="map" style="width: 100%; height: 88vh;"></div>

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

<!-- MODAL TOWER -->
<div id="modalTower" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.8);
    justify-content: center;
    align-items: center;
    z-index: 9999;
">
    <button onclick="closeTower()" style="
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: white;
        border: none;
        font-size: 20px;
        padding: 10px;
        cursor: pointer;
        z-index: 10000;">❌</button>
    <div style="width: 70%; background: white; padding: 20px; text-align: center; border-radius: 10px;">
        <h3 id="towerTitle">Menara</h3>
        <img id="towerImage" src="" alt="Foto Menara" style="max-width: 100%; max-height: 70vh; object-fit: contain; border: 1px solid #ccc; border-radius: 10px;">
    </div>
</div>


<!-- LEGENDA PETA -->
<div id="legend" style="
        position: absolute;
        bottom: 473px;
        left: 80px; 
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

<!-- Audio notifikasi -->
<audio id="notif-audio" src="<?= base_url('aset/alarm/alarm1.mp3') ?>" loop></audio>

<!-- LIBRARY -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Pastikan baseURL sudah didefinisikan di template induk atau disini:
    const baseURL = '<?= base_url(); ?>/';

    // Data gedung dari controller ke view
    const gedungMarkers = <?= json_encode($gedung) ?>;

    const map = L.map('map').setView([-8.294, 114.3067], 17);

    L.tileLayer('http://{s}.google.com/vt?lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Google',
    }).addTo(map);

    gedungMarkers.forEach(g => {
        let iconUrl = '<?= base_url('aset/img/marker_biru.png'); ?>';
        if (g.tipe === 'menara') {
            iconUrl = '<?= base_url('aset/img/ic_tower.png'); ?>';
        }

        const marker = L.marker([parseFloat(g.latitude), parseFloat(g.longitude)], {
            icon: L.icon({
                iconUrl: iconUrl,
                iconSize: [35, 35],
                iconAnchor: [20, 30],
                popupAnchor: [0, -30]
            })
        }).addTo(map);

        // Pastikan g.denah berisi nama file gambar, misalnya "2.jpg"
        if (g.tipe === 'menara') {
            marker.bindPopup(
                `<b>${g.nama_gedung}</b><br>
             <button onclick="lihatTower(${g.id_gedung}, '${g.nama_gedung}', '${g.denah}')">Lihat Menara</button>`
            );
        } else {
            marker.bindPopup(
                `<b>${g.nama_gedung}</b><br>
             <button onclick="showDenah(${g.id_gedung}, '${g.nama_gedung}')">Lihat Denah</button>`
            );
        }
    });


    // Modal dan denah
    const modal = document.getElementById('modal2D');
    const floorplanImage = document.getElementById('floorplanImage');
    const markersOnFloorplan = document.getElementById('markersOnFloorplan');
    const selectLantai = document.getElementById('selectLantai');
    const modalTitle = document.getElementById('modalTitle');

    let currentGedungId = null;
    let lantaiData = [];
    let dangerAudio = null; // Variabel untuk menyimpan referensi audio agar bisa diakses secara global

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

        // Ambil data perangkat untuk lantai ini
        fetch(`${baseURL}api/perangkat/lantai/${lantaiId}`)
            .then(res => res.json())
            .then(perangkat => {
                perangkat.forEach(p => {
                    const marker = document.createElement('div');
                    marker.style.position = 'absolute';
                    marker.style.width = '12px';
                    marker.style.height = '12px';
                    marker.style.borderRadius = '50%';
                    marker.style.left = `${p.pos_x}%`;
                    marker.style.top = `${p.pos_y}%`;
                    marker.style.transform = 'translate(-50%, -50%)';
                    marker.title = p.nama_perangkat;

                    // Tambahkan warna dinamis
                    if (p.status_perangkat === 'aktif') {
                        marker.style.backgroundColor = 'green';
                    } else if (p.status_perangkat === 'mati') {
                        marker.style.backgroundColor = 'gray';
                    } else if (p.status_perangkat === 'bahaya') {
                        marker.style.backgroundColor = 'red';
                    } else {
                        marker.style.backgroundColor = 'green';
                    }

                    markersOnFloorplan.appendChild(marker);
                });


            })
            .catch(err => {
                console.error("Gagal ambil data perangkat:", err);
            });
    });

    function close2D() {
        modal.style.display = 'none';
        floorplanImage.src = '';
        markersOnFloorplan.innerHTML = '';
        selectLantai.innerHTML = '<option value="">Pilih Lantai</option>';
    }

    function lihatTower(id, nama, denah) {
        document.getElementById('towerTitle').textContent = `Menara: ${nama}`;

        // Gunakan nama file gambar dari parameter `denah`
        const imagePath = '<?= base_url('aset/denah/'); ?>' + denah;

        const towerImage = document.getElementById('towerImage');
        towerImage.src = imagePath;

        // Tampilkan modal
        document.getElementById('modalTower').style.display = 'flex';
    }


    function closeTower() {
        document.getElementById('modalTower').style.display = 'none';
        document.getElementById('towerImage').src = '';
    }

    function enableAudio() {
        const audio = document.getElementById('notif-audio');
        audio.play().then(() => {
            audio.pause();
            console.log('Audio siap diputar saat status bahaya!');
        }).catch(err => {
            console.warn('Gagal mengaktifkan audio:', err);
        });
    }

    // Koneksi WebSocket ke server Node.js kamu
    const socket = new WebSocket("ws://localhost:8080"); // Ganti jika dihosting

    socket.onopen = function() {
        console.log("WebSocket tersambung.");
    };

    socket.onmessage = function(event) {
        const data = JSON.parse(event.data);
        console.log("Data dari WebSocket:", data);

        if (data.status_perangkat === 'bahaya') {
            // Inisialisasi audio jika belum ada
            if (!dangerAudio) {
                dangerAudio = document.getElementById('notif-audio');
            }

            // Putar audio notifikasi
            dangerAudio.play().catch(err => {
                console.warn('Audio gagal diputar:', err);
            });

            // Tampilkan notifikasi visual dengan SweetAlert
            Swal.fire({
                icon: 'warning',
                title: '🚨 PERINGATAN BAHAYA! 🚨',
                html: `<strong>Jenis Bencana:</strong> ${data.jenis_bencana}<br>
                       <strong>Perangkat:</strong> ${data.nama_perangkat}<br>
                       <strong>Lokasi:</strong><br>
                        <strong>Gedung:</strong>  ${data.gedung}<br>
                        <strong>Lantai:</strong> ${data.lantai}<br> 
                        <strong>Ruangan:</strong> ${data.ruangan}<br>
                       <strong>Tanggal & Waktu:</strong> ${data.waktu}`,
                backdrop: true,
                allowOutsideClick: false,
                confirmButtonText: 'Tutup',
                showConfirmButton: true
            }).then((result) => {
                // Hentikan audio saat tombol "Tutup" diklik atau SweetAlert ditutup
                if (dangerAudio) {
                    dangerAudio.pause();
                    dangerAudio.currentTime = 0; // Mengatur ulang waktu audio ke awal
                    console.log('Audio dihentikan.');
                }
            });
        }

        // Kamu bisa update tampilan status perangkat juga di sini...
    };

    socket.onerror = function(err) {
        console.error("WebSocket error:", err);
    };
</script>

<?= $this->endSection() ?>
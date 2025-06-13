<?= $this->extend('template') ?>
<?= $this->section('content') ?>
<div id="map" style="width: 100%; height: 80vh;"></div>

<div id="legend" style="
        position: absolute;
        bottom: 90px;
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

<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD-asvrnczu5xDwfpYrxb4MozRaJ59qku0&callback=initMap"></script>

<script>
    // Pastikan initMap sudah didefinisikan di template.php.
    // Jika Anda ingin menambahkan marker spesifik untuk halaman ini,
    // Anda bisa memodifikasi initMap di template untuk memanggil fungsi di sini
    // ATAU menggunakan event listener pada peta yang sudah diinisialisasi.

    // Contoh menggunakan event listener untuk menambahkan marker setelah peta siap
    // Ini memastikan peta 'map' sudah ada dan siap digunakan.
    window.addEventListener('load', function() {
        if (typeof google !== 'undefined' && typeof map !== 'undefined') {
            // Tambahkan marker khusus untuk halaman ini
            const markerData = [{
                    lat: -8.295480850822189,
                    lng: 114.30721663330499,
                    title: "Aula Poliwangi",
                    iconUrl: '<?= base_url('aset/img/marker_biru.png'); ?>'
                },
                {
                    //-8.295525092731191, 114.30676610522671
                    lat: -8.295525092731191, // Contoh koordinat lain
                    lng: 114.30676610522671, // Contoh koordinat lain
                    title: "Perpustakaan Poliwangi",
                    iconUrl: '<?= base_url('aset/img/marker_biru.png'); ?>' // Bisa ikon berbeda
                },
                {
                    //-8.294315378085596, 114.30572112713598
                    lat: -8.2943153780855962,
                    lng: 114.30572112713598,
                    title: "Gedung Prabu Tawangalun",
                    iconUrl: '<?= base_url('aset/img/marker_biru.png'); ?>'
                },
                {
                    //-8.293291608320539, 114.30682609580788
                    lat: -8.293291608320539,
                    lng: 114.30682609580788,
                    title: "Hotel Poliwangi",
                    iconUrl: '<?= base_url('aset/img/marker_biru.png'); ?>'
                },
                {
                    //-8.292446616676521, 114.3057493479127
                    lat: -8.292446616676521,
                    lng: 114.3057493479127,
                    title: "Gedung Kuliah Terpadu",
                    iconUrl: '<?= base_url('aset/img/marker_biru.png'); ?>'
                },
                // Tambahkan lebih banyak data marker di sini
            ];

            markerData.forEach(data => {
                new google.maps.Marker({
                    position: {
                        lat: data.lat,
                        lng: data.lng
                    },
                    map: map,
                    title: data.title,
                    icon: {
                        url: data.iconUrl,
                        scaledSize: new google.maps.Size(30, 30)
                    }
                });
            });

            const markerMenara = new google.maps.Marker({
                position: {
                    //-8.293764768763173, 114.30649834537775
                    lat: -8.293764768763173,
                    lng: 114.30649834537775
                }, // Contoh lokasi menara
                map: map, // Gunakan objek 'map' yang sudah didefinisikan di template
                title: "Menara Peringatan",
                icon: {
                    url: '<?= base_url('aset/img/ic_tower.png'); ?>',
                    scaledSize: new google.maps.Size(30, 30) // Sesuaikan ukuran ikon
                }
            });

            // Anda juga bisa menambahkan event listener ke objek peta jika diperlukan
            // map.addListener("click", (mapsMouseEvent) => {
            //     console.log("Peta diklik di: " + mapsMouseEvent.latLng.toString());
            // });
        }
    });
</script>
<?= $this->endSection() ?>
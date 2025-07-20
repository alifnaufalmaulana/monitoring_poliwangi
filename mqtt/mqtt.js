const mqtt = require('mqtt');
const mysql = require('mysql');
const WebSocket = require('ws');
const util = require('util');

// Koneksi ke database MySQL
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
  database: 'monitor_bencana',
});

db.connect((err) => {
  if (err) {
    console.error('Gagal terhubung ke database:', err);
    return;
  }
  console.log('Terhubung ke database MySQL');
});

// Ubah query callback jadi async/await
const query = util.promisify(db.query).bind(db);

// Buat WebSocket server di port 8080
const wss = new WebSocket.Server({ port: 8080 });

// Koneksi ke broker MQTT publik (mosquitto)
const client = mqtt.connect('ws://test.mosquitto.org:8080');

client.on('connect', () => {
  console.log('Terhubung ke broker MQTT Mosquitto');

  // Subscribe ke topik untuk menerima data dari perangkat
  client.subscribe('menara_poliwangi', (err) => {
    if (err) {
      console.error('Gagal subscribe:', err);
    } else {
      console.log("Berhasil subscribe ke topik 'menara_poliwangi'");
    }
  });
});

// Tangani pesan yang diterima dari MQTT
client.on('message', async (topic, message) => {
  const payload = message.toString();
  console.log(`Pesan diterima dari [${topic}]: ${payload}`);

  try {
    const parsed = JSON.parse(payload);
    const dataArray = Array.isArray(parsed) ? parsed : [parsed];

    for (const data of dataArray) {
      // Update posisi perangkat jika ada koordinat GPS
      if (data.latitude && data.longitude) {
        await query('UPDATE perangkat SET lat = ?, `long` = ? WHERE id_perangkat = ?', [data.latitude, data.longitude, data.id_perangkat]);
        console.log(`Update posisi perangkat ID ${data.id_perangkat}`);
      }

      // Cek riwayat terbaru perangkat
      const results = await query('SELECT status_perangkat, aksi, waktu FROM riwayat_perangkat WHERE id_perangkat = ? ORDER BY waktu DESC LIMIT 1', [data.id_perangkat]);

      if (results.length === 0) {
        // Jika belum ada riwayat, insert baru
        await query('INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)', [data.id_perangkat, data.status_perangkat, data.aksi, data.waktu]);
        console.log(`Insert riwayat pertama perangkat ID ${data.id_perangkat}`);
      } else {
        const last = results[0];

        // Update waktu jika status dan aksi sama tapi waktunya beda
        if (last.status_perangkat === data.status_perangkat && last.aksi === data.aksi && last.waktu !== data.waktu) {
          await query('UPDATE riwayat_perangkat SET waktu = ? WHERE id_perangkat = ? AND waktu = ?', [data.waktu, data.id_perangkat, last.waktu]);
        }
        // Update aksi jika aksi berubah, tapi status sama
        else if (last.status_perangkat === data.status_perangkat && last.aksi !== data.aksi) {
          await query('UPDATE riwayat_perangkat SET aksi = ?, waktu = ? WHERE id_perangkat = ? AND waktu = ?', [data.aksi, data.waktu, data.id_perangkat, last.waktu]);
        }
        // Jika semua berbeda, masukkan riwayat baru
        else if (last.status_perangkat !== data.status_perangkat || last.aksi !== data.aksi || last.waktu !== data.waktu) {
          await query('INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)', [data.id_perangkat, data.status_perangkat, data.aksi, data.waktu]);
        }
      }

      // Jika status bahaya, simpan ke kebencanaan
      if (data.status_perangkat === 'bahaya' && data.jenis_bencana) {
        await query('INSERT INTO kebencanaan (id_perangkat, jenis_bencana, waktu) VALUES (?, ?, ?)', [data.id_perangkat, data.jenis_bencana, data.waktu]);
        console.log(`Bahaya! Disimpan ke kebencanaan dari perangkat ID ${data.id_perangkat}`);
      }

      // Ambil info lokasi perangkat
      const lokasiResult = await query(
        `SELECT p.nama_perangkat, g.id_gedung, g.nama_gedung, l.nama_lantai, r.nama_ruangan
        FROM perangkat p
        JOIN ruangan r ON p.id_ruangan = r.id_ruangan
        JOIN lantai l ON r.id_lantai = l.id_lantai
        JOIN gedung g ON l.id_gedung = g.id_gedung
        WHERE p.id_perangkat = ?`,
        [data.id_perangkat]
      );

      // Jika lokasi tidak ditemukan, beri nilai default
      const lokasi = lokasiResult[0] || {
        nama_perangkat: 'Tidak diketahui',
        id_gedung: null,
        nama_gedung: 'Tidak diketahui',
        nama_lantai: 'Tidak diketahui',
        nama_ruangan: 'Tidak diketahui',
      };

      // Gabungkan data asli dengan informasi lokasi
      const dataLengkap = {
        ...data,
        nama_perangkat: lokasi.nama_perangkat,
        id_gedung: lokasi.id_gedung,
        gedung: lokasi.nama_gedung,
        lantai: lokasi.nama_lantai,
        ruangan: lokasi.nama_ruangan,
      };

      // Kirim data ke semua klien WebSocket yang terhubung
      wss.clients.forEach((wsClient) => {
        if (wsClient.readyState === WebSocket.OPEN) {
          wsClient.send(JSON.stringify(dataLengkap));
        }
      });
    }
  } catch (e) {
    console.error('Gagal memproses pesan MQTT:', e);
  }
});


// const mqtt = require('mqtt');
// const mysql = require('mysql');
// const WebSocket = require('ws');

// // === Koneksi ke database MySQL ===
// const db = mysql.createConnection({
//   host: 'localhost',
//   user: 'root',
//   password: '',
//   database: 'monitor_bencana',
// });

// db.connect((err) => {
//   if (err) {
//     console.error('Gagal terhubung ke database:', err);
//     return;
//   }
//   console.log('Terhubung ke database MySQL');
// });

// // === Membuat WebSocket server ===
// const wss = new WebSocket.Server({ port: 8080 });

// // === Konfigurasi koneksi HiveMQ Cloud ===
// const options = {
//   host: '42d07283d5a84c3793786ab6bc58871c.s1.eu.hivemq.cloud', // Ganti dengan host dari HiveMQ Cloud kamu
//   port: 8883, // Port TLS HiveMQ
//   protocol: 'mqtts', // Menggunakan MQTT over TLS (secure)
//   username: 'hivemqalif', // Ganti dengan username HiveMQ kamu
//   password: 'Hivemqalif8', // Ganti dengan password HiveMQ kamu
//   reconnectPeriod: 1000, // Reconnect setiap 1 detik jika disconnect
// };

// // === Koneksi ke broker MQTT HiveMQ ===
// const client = mqtt.connect(options);

// client.on('connect', () => {
//   console.log('Terhubung ke HiveMQ Cloud');

//   // Subscribe ke topik sensor
//   client.subscribe('monitoring/perangkat/+', (err) => {
//     if (err) {
//       console.error('Gagal subscribe:', err);
//     } else {
//       console.log("Berhasil subscribe ke topik 'monitoring/perangkat/+'");
//     }
//   });
// });

// client.on('error', (err) => {
//   console.error('Kesalahan koneksi HiveMQ:', err);
// });

// // === Saat menerima pesan MQTT ===
// client.on('message', (topic, message) => {
//   const payload = message.toString();
//   console.log(`Pesan diterima dari [${topic}]: ${payload}`);

//   try {
//     const data = JSON.parse(payload);

//     // Update posisi perangkat ke tabel `perangkat`
//     if (data.latitude && data.longitude) {
//       const updatePosisi = `UPDATE perangkat SET latitude = ?, longitude = ? WHERE id_perangkat = ?`;
//       db.query(updatePosisi, [data.latitude, data.longitude, data.id_perangkat], (err) => {
//         if (err) {
//           console.error('Gagal update posisi perangkat:', err);
//         } else {
//           console.log(`Berhasil update posisi perangkat ID ${data.id_perangkat}`);
//         }
//       });
//     }

//     // Cek riwayat sebelumnya
//     const cekSql = 'SELECT status_perangkat, aksi, waktu FROM riwayat_perangkat WHERE id_perangkat = ? ORDER BY waktu DESC LIMIT 1';
//     db.query(cekSql, [data.id_perangkat], (err, results) => {
//       if (err) {
//         console.error('Gagal mengambil data terakhir:', err);
//         return;
//       }

//       if (results.length === 0) {
//         // Insert pertama kali
//         const insertSql = `INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)`;
//         db.query(insertSql, [data.id_perangkat, data.status_perangkat, data.aksi, data.waktu], (err) => {
//           if (err) {
//             console.error('Gagal insert riwayat pertama:', err);
//           } else {
//             console.log(`Berhasil insert riwayat pertama perangkat ID ${data.id_perangkat}`);
//           }
//         });
//       } else {
//         const last = results[0];

//         if (last.status_perangkat === data.status_perangkat && last.aksi === data.aksi && last.waktu !== data.waktu) {
//           // Update waktu saja
//           const updateSql = `UPDATE riwayat_perangkat SET waktu = ? WHERE id_perangkat = ? AND waktu = ?`;
//           db.query(updateSql, [data.waktu, data.id_perangkat, last.waktu], (err) => {
//             if (err) {
//               console.error('Gagal update waktu riwayat:', err);
//             } else {
//               console.log(`Berhasil update waktu riwayat perangkat ID ${data.id_perangkat}`);
//             }
//           });
//         } else if (last.status_perangkat === data.status_perangkat && last.aksi !== data.aksi) {
//           // Update aksi dan waktu
//           const updateSql = `UPDATE riwayat_perangkat SET aksi = ?, waktu = ? WHERE id_perangkat = ? AND waktu = ?`;
//           db.query(updateSql, [data.aksi, data.waktu, data.id_perangkat, last.waktu], (err) => {
//             if (err) {
//               console.error('Gagal update aksi & waktu riwayat:', err);
//             } else {
//               console.log(`Berhasil update aksi & waktu perangkat ID ${data.id_perangkat}`);
//             }
//           });
//         } else if (last.status_perangkat !== data.status_perangkat || last.aksi !== data.aksi || last.waktu !== data.waktu) {
//           // Tambahkan riwayat baru
//           const insertSql = `INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)`;
//           db.query(insertSql, [data.id_perangkat, data.status_perangkat, data.aksi, data.waktu], (err) => {
//             if (err) {
//               console.error('Gagal insert riwayat baru:', err);
//             } else {
//               console.log(`Berhasil insert riwayat baru perangkat ID ${data.id_perangkat}`);
//             }
//           });
//         }
//       }

//       // Simpan ke tabel kebencanaan jika status bahaya
//       if (data.status_perangkat === 'bahaya' && data.jenis_bencana) {
//         const insertBencana = `INSERT INTO kebencanaan (id_perangkat, jenis_bencana, waktu) VALUES (?, ?, ?)`;
//         db.query(insertBencana, [data.id_perangkat, data.jenis_bencana, data.waktu], (err) => {
//           if (err) {
//             console.error('Gagal menyimpan ke kebencanaan:', err);
//           } else {
//             console.log(`Berhasil simpan kebencanaan dari perangkat ID ${data.id_perangkat}`);
//           }
//         });
//       }

//       // Kirim data real-time ke semua client yang terhubung melalui WebSocket
//       wss.clients.forEach((wsClient) => {
//         if (wsClient.readyState === WebSocket.OPEN) {
//           wsClient.send(payload);
//         }
//       });
//     });
//   } catch (e) {
//     console.error('Gagal parse payload JSON:', e);
//   }
// });

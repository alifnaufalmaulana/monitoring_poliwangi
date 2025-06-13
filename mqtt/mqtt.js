const mqtt = require('mqtt');
const mysql = require('mysql');
const WebSocket = require('ws');

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

// Membuat WebSocket server
const wss = new WebSocket.Server({ port: 8080 });

// Koneksi ke broker MQTT
const client = mqtt.connect('ws://test.mosquitto.org:8080');

client.on('connect', () => {
  console.log('Terhubung ke broker MQTT Lokal');

  client.subscribe('monitoring/perangkat/+', (err) => {
    if (err) {
      console.error('Gagal subscribe:', err);
    } else {
      console.log("Berhasil subscribe ke topik 'monitoring/perangkat/+'");
    }
  });
});

// Saat menerima pesan MQTT
client.on('message', (topic, message) => {
  const payload = message.toString();
  console.log(`Pesan diterima dari [${topic}]: ${payload}`);

  try {
    const data = JSON.parse(payload);

    // Update lat dan long perangkat ke tabel `perangkat`
    if (data.latitude && data.longitude) {
      const updatePosisi = `UPDATE perangkat SET latitude = ?, longitude = ? WHERE id_perangkat = ?`;
      db.query(updatePosisi, [data.latitude, data.longitude, data.id_perangkat], (err) => {
        if (err) {
          console.error('Gagal update posisi perangkat:', err);
        } else {
          console.log(`Berhasil update posisi perangkat ID ${data.id_perangkat}`);
        }
      });
    }

    // Proses penyimpanan ke `riwayat_perangkat`
    const cekSql = 'SELECT status_perangkat, aksi, waktu FROM riwayat_perangkat WHERE id_perangkat = ? ORDER BY waktu DESC LIMIT 1';
    db.query(cekSql, [data.id_perangkat], (err, results) => {
      if (err) {
        console.error('Gagal mengambil data terakhir:', err);
        return;
      }

      if (results.length === 0) {
        // Insert pertama kali
        const insertSql = `INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)`;
        db.query(insertSql, [data.id_perangkat, data.status_perangkat, data.aksi, data.waktu], (err) => {
          if (err) {
            console.error('Gagal insert riwayat perangkat pertama:', err);
          } else {
            console.log(`Berhasil insert riwayat pertama perangkat ID ${data.id_perangkat}`);
          }
        });
      } else {
        const last = results[0];

        if (last.status_perangkat === data.status_perangkat && last.aksi === data.aksi && last.waktu !== data.waktu) {
          // Waktu beda, status & aksi sama → update waktu
          const updateSql = `UPDATE riwayat_perangkat SET waktu = ? WHERE id_perangkat = ? AND waktu = ?`;
          db.query(updateSql, [data.waktu, data.id_perangkat, last.waktu], (err) => {
            if (err) {
              console.error('Gagal update waktu riwayat:', err);
            } else {
              console.log(`Berhasil update waktu riwayat perangkat ID ${data.id_perangkat}`);
            }
          });
        } else if (last.status_perangkat === data.status_perangkat && last.aksi !== data.aksi) {
          // Aksi beda → update aksi & waktu
          const updateSql = `UPDATE riwayat_perangkat SET aksi = ?, waktu = ? WHERE id_perangkat = ? AND waktu = ?`;
          db.query(updateSql, [data.aksi, data.waktu, data.id_perangkat, last.waktu], (err) => {
            if (err) {
              console.error('Gagal update aksi & waktu riwayat:', err);
            } else {
              console.log(`Berhasil update aksi & waktu perangkat ID ${data.id_perangkat}`);
            }
          });
        } else if (last.status_perangkat !== data.status_perangkat || last.aksi !== data.aksi || last.waktu !== data.waktu) {
          // Ada perubahan → insert baru
          const insertSql = `INSERT INTO riwayat_perangkat (id_perangkat, status_perangkat, aksi, waktu) VALUES (?, ?, ?, ?)`;
          db.query(insertSql, [data.id_perangkat, data.status_perangkat, data.aksi, data.waktu], (err) => {
            if (err) {
              console.error('Gagal insert riwayat baru:', err);
            } else {
              console.log(`Berhasil insert riwayat baru perangkat ID ${data.id_perangkat}`);
            }
          });
        }
      }

      // Simpan ke tabel `kebencanaan` jika status bahaya
      if (data.status_perangkat === 'bahaya' && data.jenis_bencana) {
        const insertBencana = `INSERT INTO kebencanaan (id_perangkat, jenis_bencana, waktu) VALUES (?, ?, ?)`;
        db.query(insertBencana, [data.id_perangkat, data.jenis_bencana, data.waktu], (err) => {
          if (err) {
            console.error('Gagal menyimpan ke kebencanaan:', err);
          } else {
            console.log(`Berhasil simpan kebencanaan dari perangkat ID ${data.id_perangkat}`);
          }
        });
      }

      // Kirim ke client WebSocket
      wss.clients.forEach((wsClient) => {
        if (wsClient.readyState === WebSocket.OPEN) {
          wsClient.send(payload);
        }
      });
    });
  } catch (e) {
    console.error('Gagal parse payload JSON:', e);
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

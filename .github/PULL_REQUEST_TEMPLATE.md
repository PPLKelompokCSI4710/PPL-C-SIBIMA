## Deskripsi PBI

**Nomor PBI:** PBI-17 & PBI-18
**Judul PBI:** Dosen Memonitor Progres Studi & Mahasiswa Mengelola Progres Studi
**Tautan Trello/Jira:** -

## Deskripsi Singkat Pekerjaan

Pada PBI ini, fitur manajemen dan _monitoring_ progres akademik pada sistem SIBIMA telah diimplementasikan:

1. **Implementasi UI Filament & Tema SIBIMA:** Menyesuaikan warna _dashboard_ Filament dan aplikasi utama dengan standar palet warna SIBIMA.
2. **Autorisasi Progres Studi:** Mengonfigurasi akses agar **Dosen** dan **Admin** dapat melihat seluruh _progress_ mahasiswa (PBI#17), sementara **Mahasiswa** difilter otomatis hanya dapat melihat dan me-_update_ datanya sendiri (PBI#18).
3. **Penyempurnaan Tabel Monitoring:** Menambahkan relasi sehingga tabel menampilkan Nama Mahasiswa, NIM, Progress Studi (SKS & IPK), serta Status Akademik (dengan indikator warna).
4. **Role pada Registrasi & Otomatisasi Profil:** Menambahkan opsi _dropdown_ (Mahasiswa, Dosen, Admin) pada halaman registrasi. Registrasi kini otomatis memberikan _role_ melalui Spatie dan langsung membuat _dummy profile_ kosong untuk mencegah error tabel _monitoring_. Pendaftaran juga diarahkan otomatis ke `/admin`.
5. **Perbaikan Model & Relasi:** Membuat model `Mahasiswa` dan `Dosen` yang sebelumnya hilang, lalu menghubungkan keduanya di dalam model `User`.

## Perubahan Konfigurasi / Dependensi?

- [ ] Ada penambahan package Composer baru (`composer install` diperlukan)
- [ ] Ada penambahan package NPM baru (`npm install` diperlukan)
- [ ] Ada penambahan file konfigurasi `.env` baru (sebutkan di bawah jika ada)
- [x] Ada perubahan migrasi atau seeder baru (`php artisan migrate:fresh --seed` diperlukan) -> _File `MahasiswaSeeder.php` ditambahkan._

## Checklist Developer

_Pastikan Anda menceklis semua poin di bawah sebelum meminta Code Review!_

- [x] Kode lulus pengecekan lokal (`npm run format` & `php artisan pint`)
- [x] File route diletakkan pada folder aktor yang tepat (`routes/web/actor.php`) -> _Akses via Filament `/admin`_
- [x] UI berjalan lancar pada Vue/Inertia tanpa error console
- [x] PBI sudah ditesting secara mandiri di lokal dan sesuai kriteria (Acceptance Criteria)

## Catatan Khusus untuk Reviewer

Sangat disarankan untuk melakukan `php artisan migrate:fresh --seed` dan `npm run build`. Registrasikan akun baru dengan _role_ "Mahasiswa" agar dapat melihat otomatisasi injeksi tabel _profile_ dan menguji fitur _update_ progres (PBI#18).

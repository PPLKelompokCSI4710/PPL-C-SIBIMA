# SIBIMA - Sistem Bimbingan Mahasiswa

Repositori ini ditujukan untuk tim 8 Full-Stack Developer yang mengembangkan fitur-fitur SIBIMA berdasarkan Product Backlog Items (PBI).

## 🚀 Prasyarat Pengembangan (Environment Requirements)
Pastikan Anda memiliki:
- **PHP** ^8.3
- **Composer** v2+
- **Node.js** v20+ & **NPM** v10+
- **MySQL** / MariaDB

## 🛠 Instalasi Proyek (Lokal)
1. **Clone repository ini**
   ```bash
   git clone <repo-url> sibima
   cd sibima
   ```
2. **Install Dependensi Backend & Frontend**
   ```bash
   composer install
   npm install
   ```
3. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Sesuaikan koneksi database (`DB_DATABASE`, `DB_USERNAME`, dll) di file `.env`.*
4. **Migrasi & Seed Database Base**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Akan menggenerate role Spatie dan akun testing default: admin@sibima.test, dosen@sibima.test, mahasiswa@sibima.test dengan password `password`)*
5. **Jalankan Development Server**
   ```bash
   npm run dev
   # Buka tab terminal baru
   php artisan serve
   ```

## 📜 Standardisasi & Konvensi Git
Untuk menghindari konflik antar 8 developer, patuhi standar berikut:

### 1. Struktur Branching
Selalu buat branch baru dari `main` atau `develop` untuk setiap PBI menggunakan format:
`feature/PBI-<Nomor>-<Nama-Fitur>`
*Contoh:* `feature/PBI-04-dashboard-mahasiswa`

### 2. Format Commit (Husky)
Linting berjalan otomatis sebelum commit. Jika ada error ESLint atau PHP (Pint), commit akan ditolak. Anda bebas menulis judul commit apa pun, tapi jika relevan sebutkan nomor PBI-nya:
*Contoh:* `feat: [PBI-04] add chart to student dashboard`

### 3. Pemisahan Routing
Dilarang keras menumpuk route di `routes/web.php` secara langsung agar terhindar dari *merge conflict*. Gunakan file route yang sesuai aktor:
- `routes/web/admin.php` → PBI untuk fitur Admin.
- `routes/web/dosen.php` → PBI untuk fitur Dosen.
- `routes/web/mahasiswa.php` → PBI untuk Mahasiswa.

### 4. Pull Requests
Saat fitur Anda selesai, push branch Anda ke remote dan buat **Pull Request (PR)**. GitHub Actions (CI) akan mengecek kode Anda (Pint & PHPUnit). 
Harap isi dekskripsi PR sesuai *Template* yang muncul otomatis. Wajib lulus pengecekan teman se-tim (Code Review).

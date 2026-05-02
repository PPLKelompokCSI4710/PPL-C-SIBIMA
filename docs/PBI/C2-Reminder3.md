# Fitur C2 — Reminder3 (PBI 15–16)

Dokumen ini merekam **Product Backlog Item (PBI)** untuk fitur reminder pada modul bimbingan.

## PBI 15 — Reminder jadwal bimbingan multi-tahap otomatis

### User story

Sebagai **mahasiswa/dosen**, saya ingin menerima reminder otomatis bertahap (H-3, H-1, dan H-2 jam sebelum jadwal), sehingga saya memiliki waktu yang cukup untuk mempersiapkan diri dan tidak lupa terhadap jadwal bimbingan.

### Acceptance criteria

- Sistem mengirim reminder pada **H-3**, **H-1**, dan **H-2 jam** sebelum jadwal.
- Pengguna dapat memilih dan menonaktifkan tahap reminder tertentu.
- Reminder berisi detail lengkap: **nama dosen/mahasiswa**, **waktu**, **lokasi/link**, dan **topik**.
- Jika jadwal diubah, reminder lama dibatalkan dan jadwal reminder diperbarui otomatis.

### Implementasi (repo)

- **Preferensi user**: `reminder_preferences` (`/settings/reminders`)
- **Antrian reminder**: `bimbingan_reminders` (dibuat/di-sync otomatis saat `Bimbingan` disimpan)
- **Pengirim**: `php artisan bimbingan:dispatch-schedule-reminders` (direkomendasikan dijalankan tiap menit via scheduler)
- **Notifikasi**: `BimbinganScheduleReminderNotification` (channel: `database`)

## PBI 16 — Reminder progres & target akademik periodik

### User story

Sebagai **mahasiswa**, saya ingin mendapat reminder otomatis ketika target akademik saya belum tercapai atau saya belum melakukan bimbingan dalam periode tertentu, sehingga saya dapat tetap on-track dalam studi.

### Acceptance criteria

- Sistem mendeteksi mahasiswa yang tidak melakukan bimbingan selama **N hari** (**konfigurasi admin**).
- Reminder dikirim ke mahasiswa dan **tembusan ke dosen pembimbing**.
- Reminder menyertakan ringkasan progres: **target tercapai vs belum tercapai**.
- Frekuensi reminder progres dapat diatur: **mingguan** atau **dua mingguan**.

### Implementasi (repo)

- **Konfigurasi admin (N hari)**: `app_settings.key=progress_reminder_inactive_days` (`/admin/settings/reminders`)
- **Frekuensi user**: `mahasiswa.progress_reminder_frequency` (`weekly|biweekly`)
- **Deteksi & kirim**: `php artisan bimbingan:check-progress`
- **Notifikasi**: `AcademicProgressNotification` (channel: `database`, termasuk `progress_summary`)

## Testing (Laravel Dusk)

- Dusk test: `tests/Browser/ReminderFeatureTest.php`

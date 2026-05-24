# Fitur C2 — Reminder & Eskalasi (PBI 32–34)

Dokumen ini menyelaraskan **Product Backlog Item (PBI)** dengan implementasi di repositori.

## PBI 32 — Reminder jadwal bimbingan multi-tahap otomatis

| AC   | Ringkas                    | Implementasi                                                                                           |
| ---- | -------------------------- | ------------------------------------------------------------------------------------------------------ |
| 32.1 | H-3, H-1, H-2 jam          | `BimbinganReminderService`, `bimbingan_reminders`, `bimbingan:dispatch-schedule-reminders`             |
| 32.2 | Konten lengkap             | `BimbinganScheduleReminderNotification` (`detail`: nama, waktu, lokasi/tipe, topik)                    |
| 32.3 | Nonaktifkan tahap          | `reminder_preferences` + `/settings/reminders`                                                         |
| 32.4 | Ubah jadwal → jadwal ulang | Sync pada perubahan field relevan di `Bimbingan` (lihat `Bimbingan::booted`)                           |
| 32.5 | Tidak duplikasi kirim      | Baris `sent` tidak di-dispatch ulang; tes `test_dispatch_does_not_resend_already_sent_stage_reminders` |

## PBI 33 — Reminder progres & target akademik periodik

| AC   | Ringkas                      | Implementasi                                                      |
| ---- | ---------------------------- | ----------------------------------------------------------------- |
| 33.1 | Deteksi tidak aktif ≥ N hari | `CheckAcademicProgressCommand`, `progress_reminder_inactive_days` |
| 33.2 | CC dosen                     | Notifikasi `AcademicProgressNotification` (`isDosenCc`)           |
| 33.3 | Ringkasan progres            | `progress_summary`: SKS lulus/total, IPK, semester                |
| 33.4 | Frekuensi & cooldown         | `last_progress_reminder_sent_at` + `weekly` / `biweekly`          |
| 33.5 | Threshold admin              | `/admin/settings/reminders` → `AppSetting`                        |

## PBI 34 — Eskalasi ke admin

| AC   | Ringkas               | Implementasi                                                                                            |
| ---- | --------------------- | ------------------------------------------------------------------------------------------------------- |
| 34.1 | Record eskalasi       | `eskalasis`, threshold `escalation_reminder_threshold`, `consecutive_progress_reminders`                |
| 34.2 | Notifikasi admin      | `EskalasiNotification` (detail: mahasiswa, jumlah sesi selesai, terakhir bimbingan, ringkasan akademik) |
| 34.3 | Monitoring            | `/admin/eskalasi`, `EskalasiController@index`                                                           |
| 34.4 | Tutup setelah booking | `Bimbingan::saved` → `resolved` + reset `consecutive_progress_reminders`                                |
| 34.5 | Tidak duplikat        | Cek `Eskalasi` aktif sebelum `create`                                                                   |

## Testing (Laravel Dusk)

- `tests/Browser/LoginTest.php` — prasyarat login
- `tests/Browser/Pbi32ScheduleReminderTest.php` — PBI 32
- `tests/Browser/Pbi33ProgressReminderTest.php` — PBI 33
- `tests/Browser/Pbi34EskalasiTest.php` — PBI 34

Laporan langkah uji: `docs/PBI/Test_Report_Reminder_Dusk.md`.

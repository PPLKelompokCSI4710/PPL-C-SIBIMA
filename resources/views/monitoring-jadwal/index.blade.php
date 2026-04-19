<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Jadwal Bimbingan</title>
    <!-- Kamu bisa tambahkan Tailwind atau Bootstrap di sini nanti jika diperlukan -->
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn-cancel { background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
        .btn-cancel:hover { background-color: #c82333; }
        .alert { padding: 10px; background-color: #d4edda; color: #155724; margin-bottom: 20px; border-radius: 4px; }
        .alert-error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <h2>Monitoring Jadwal Bimbingan</h2>

    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Dosen ID</th>
                <th>Mahasiswa ID</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Topik</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwalBimbingans as $index => $jadwal)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $jadwal->dosen_id }}</td>
                    <td>{{ $jadwal->mahasiswa_id }}</td>
                    <td>{{ $jadwal->tanggal }}</td>
                    <td>{{ $jadwal->waktu }}</td>
                    <td>{{ $jadwal->topik_bimbingan }}</td>
                    <td>{{ ucfirst($jadwal->tipe) }}</td>
                    <td>
                        <strong>{{ ucfirst($jadwal->status) }}</strong>
                    </td>
                    <td>
                        @if(in_array($jadwal->status, ['pending', 'approved']))
                            <form action="{{ route('monitoring-jadwal.cancel', $jadwal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan jadwal ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-cancel">Batalkan</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Belum ada jadwal bimbingan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>

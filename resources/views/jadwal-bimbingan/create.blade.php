<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Jadwal Bimbingan</title>
</head>
<body>
    <h1>Buat Jadwal Bimbingan</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('jadwal-bimbingan.store') }}" method="POST">
        @csrf
        <label for="tanggal">Tanggal:</label>
        <input type="date" id="tanggal" name="tanggal" required>

        <label for="waktu">Waktu:</label>
        <input type="time" id="waktu" name="waktu" required>

        <label for="topik_bimbingan">Topik Bimbingan:</label>
        <input type="text" id="topik_bimbingan" name="topik_bimbingan">

        <button type="submit">Simpan</button>
    </form>
</body>
</html>
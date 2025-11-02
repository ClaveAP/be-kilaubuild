<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Edit Post</h1>
    <form action="/edit-statistic/{{$statis->id}}" method="POST">
        @csrf
        @method('PUT')
        <input type="number" name="tahun_pengalaman" value="{{$statis->tahun_pengalaman}}">
        <input type="number" name="proyek_selesai" value="{{$statis->proyek_selesai}}">
        <input type="number" name="klien_puas" value="{{$statis->klien_puas}}">
        <input type="number" name="sebaran_kota" value="{{$statis->sebaran_kota}}">
        <button>Save Changes</button>
    </form>
</body>
</html>
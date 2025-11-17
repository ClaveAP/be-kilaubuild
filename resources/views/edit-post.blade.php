<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Edit Post</h1>
    <form action="/edit-post/{{$post->id}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="text" name="title" value="{{$post->title}}"><br><br>
        <input type="text" name="instagram_url" value="{{$post->instagram_url}}"><br><br>
        <input type="file" name="image"><br><br>
        <label>
            <input type="checkbox" name="is_published" value="1" {{ $post->is_published ? 'checked' : '' }}>
            Tampilkan di Home Page
        </label><br><br>
        <button>Save Changes</button>
    </form>
</body>
</html>
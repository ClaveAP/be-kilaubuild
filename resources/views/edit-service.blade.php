<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Edit Service</h1>
    <form action="/edit-service/{{$srvc->id}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{$srvc->name}}">
        <input type="text" name="desc" value="{{$srvc->desc}}">
        <input type="file" name="image" value="{{$srvc->image}}">
        <button>Save Changes</button>
    </form>
</body>
</html>
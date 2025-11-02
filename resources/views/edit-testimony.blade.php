<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Edit Post</h1>
    <form action="/edit-testimony/{{$tstmn->id}}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{$tstmn->name}}">
        <input type="text" name="review" value="{{$tstmn->review}}">
        <input type="number" name="star" value="{{$tstmn->star}}">
        <button>Save Changes</button>
    </form>
</body>
</html>
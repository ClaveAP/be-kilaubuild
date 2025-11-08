<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="/dashboard" method="POST">
        @csrf
        <button>Dashboard</button>
    </form>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Kritik dan Saran</h2>
        <form action="/send-feedback" method="POST">
            @csrf
            <input name="name" type="text" placeholder="Nama...">
            <input name="email" type="email" placeholder="Email...">
            <input name="no_telp" type="text" placeholder="Nomor HP...">
            <textarea name="feedback" type="text" placeholder="Kritik atau Saran..."></textarea>
            <button>Send</button>
        </form>
    </div>

    @if(isset($posts) && $posts->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Posts</h2>
        @foreach ($posts as $post)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <a href="{{$post['instagram_url']}}">
                <h3>{{$post['title']}}</h3>
                <img src="{{ asset('storage/' . $post->image) }}" alt="{{$post['title']}}" style="max-width: 200px;">
            </a>
        </div>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Posts</h2>
        <p>No posts available</p>
    </div>
    @endif
    
</body>
</html>
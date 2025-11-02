<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @auth
    Sudah Login
    <form action="/logout" method="POST">
        @csrf
        <button>Logout</button>
    </form>
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Posts</h2>
        <form action="/create-post" method="POST" enctype="multipart/form-data">
            @csrf
            <input name="title" type="text" placeholder="title post">
            <textarea name="instagram_url" type="text" placeholder="instagram url..."></textarea>
            <input type="file" name="image">
            <button>Create post</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Ongoing Project</h2>
        <form action="/create-ongoing-project" method="POST" enctype="multipart/form-data">
            @csrf
            <input name="name" type="text" placeholder="Nama Proyek...">
            <textarea name="loc" type="text" placeholder="Lokasi..."></textarea>
            <input name="persen" type="text" placeholder="Persentase...">
            <input type="file" name="image">
            <button>Create Project</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Project Done</h2>
        <form action="/create-project-done" method="POST" enctype="multipart/form-data">
            @csrf
            <input name="name" type="text" placeholder="Nama Proyek...">
            <textarea name="desc" type="text" placeholder="Deskripsi..."></textarea>
            <input name="year" type="text" placeholder="Tahun...">
            <input type="file" name="image">
            <button>Create Project</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Desain Interior</h2>
        <form action="/create-desain-interior" method="POST" enctype="multipart/form-data">
            @csrf
            <input name="name" type="text" placeholder="Nama Desain...">
            <input type="file" name="image">
            <button>Create Desain</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create FAQ</h2>
        <form action="/create-faq" method="POST">
            @csrf
            <input name="question" type="text" placeholder="Pertanyaan...">
            <textarea name="answer" type="text" placeholder="Jawaban..."></textarea>
            <button>Create FAQ</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Testimony</h2>
        <form action="/create-testimony" method="POST">
            @csrf
            <input name="name" type="text" placeholder="Nama Klien...">
            <textarea name="review" type="text" placeholder="Isi testimoni..."></textarea>
            <input name="star" type="number" placeholder="Bintang..." max="5" min="1">
            <button>Create Testimony</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Statistic</h2>
        <form action="/create-statistic" method="POST">
            @csrf
            <input name="tahunPengalaman" type="number" placeholder="Tahun Pengalaman...">
            <input name="proyekSelesai" type="number" placeholder="Jumlah Proyek Selesai..."></input>
            <input name="klienPuas" type="number" placeholder="Jumlah Klien Puas...">
            <input name="sebaranKota" type="number" placeholder="Sebaran Kota..."></input>
            <button>Create Statistic</button>
        </form>
    </div>
    
    @if(isset($posts) && $posts->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Posts</h2>
        @foreach ($posts as $post)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$post['title']}} by {{$post->searchAuthor->name}}</h3>
            <img src="{{ asset('storage/' . $post->image) }}" alt="{{$post['title']}}" style="max-width: 200px;">
            {{$post['instagram_url']}}
        </div>
        <p><a href="/edit-post/{{$post->id}}">Edit</a></p>
        <form action="/delete-post/{{$post->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Posts</h2>
        <p>No posts available</p>
    </div>
    @endif

    @if(isset($OPs) && $OPs->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Ongoing Project</h2>
        @foreach ($OPs as $OP)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$OP['name']}}</h3>
            <img src="{{ asset('storage/' . $OP->image) }}" alt="{{$OP['name']}}" style="max-width: 200px;">
            {{$OP['loc']}}
            {{$OP['persen']}}%
        </div>
        <p><a href="/edit-ongoing-project/{{$OP->id}}">Edit</a></p>
        <form action="/delete-ongoing-project/{{$OP->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Ongoing Project</h2>
        <p>No posts available</p>
    </div>
    @endif

    @if(isset($PDs) && $PDs->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Project Done</h2>
        @foreach ($PDs as $PD)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$PD['name']}}</h3>
            <img src="{{ asset('storage/' . $PD->image) }}" alt="{{$PD['name']}}" style="max-width: 200px;">
            {{$PD['year']}}
            {{$PD['desc']}}
        </div>
        <p><a href="/edit-project-done/{{$PD->id}}">Edit</a></p>
        <form action="/delete-project-done/{{$PD->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Project Done</h2>
        <p>No posts available</p>
    </div>
    @endif

    @if(isset($DIs) && $DIs->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Desain Interior</h2>
        @foreach ($DIs as $DI)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$DI['name']}}</h3>
            <img src="{{ asset('storage/' . $DI->image) }}" alt="{{$DI['name']}}" style="max-width: 200px;">
        </div>
        <p><a href="/edit-desain-interior/{{$DI->id}}">Edit</a></p>
        <form action="/delete-desain-interior/{{$DI->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Desain Interior</h2>
        <p>No posts available</p>
    </div>
    @endif

    @if(isset($faqs) && $faqs->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>FAQs</h2>
        @foreach ($faqs as $faq)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$faq['question']}}</h3>
            {{$faq['answer']}}
        </div>
        <p><a href="/edit-faq/{{$faq->id}}">Edit</a></p>
        <form action="/delete-faq/{{$faq->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>FAQs</h2>
        <p>No FAQs available</p>
    </div>
    @endif

    @if(isset($tstmns) && $tstmns->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Testimoni</h2>
        @foreach ($tstmns as $tstmn)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$tstmn['name']}}</h3>
            {{$tstmn['review']}}
            Star: {{$tstmn['star']}}
        </div>
        <p><a href="/edit-testimony/{{$tstmn->id}}">Edit</a></p>
        <form action="/delete-testimony/{{$tstmn->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Testimoni</h2>
        <p>No Testimony available</p>
    </div>
    @endif

    {{-- @if(isset($statis) && $statis->count() > 0) --}}
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Statistic</h2>
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            {{ $statis->first()->tahun_pengalaman }}
            {{ $statis->first()->proyek_selesai }}
            {{ $statis->first()->klien_puas }}
            {{ $statis->first()->sebaran_kota }}
        </div>
        <p><a href="/edit-statistic/{{$statis->first()->id}}">Edit</a></p>
        <form action="/delete-statistic/{{$statis->first()->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
    </div>
    {{-- @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Statistic</h2>
        <p>No Statistic available</p>
    </div>
    @endif --}}
    
    @else
    {{-- <div style="border: 3px solid black; margin-bottom: 10px;">
        <form action="/register" method="POST">
            @csrf
            <h2>Register</h2>
            <input type="text" name="name" placeholder="name">
            <input type="password" name="password" placeholder="password">
            <button>Register</button>
        </form>
    </div> --}}
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <form action="/login" method="POST">
            @csrf
            <h2>Login</h2>
            <input type="text" name="loginname" placeholder="name">
            <input type="password" name="loginpassword" placeholder="password">
            <button>Login</button>
        </form>
    </div>
    @endauth
</body>
</html>
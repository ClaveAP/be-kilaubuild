<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .calendar-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .calendar-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .status-legend {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
        }
        
        .busy-color {
            background-color: #ff4444;
        }
        
        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 20px;
            border-radius: 5px;
            z-index: 1000;
        }

        /* Layout baru dengan dua kolom */
        .calendar-layout {
            display: flex;
            gap: 20px;
        }
        
        .calendar-section {
            flex: 2;
        }
        
        .schedule-section {
            flex: 1;
            min-width: 300px;
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
        }
        
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .schedule-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .selected-date {
            font-size: 14px;
            color: #666;
        }
        
        .no-events {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .schedule-table th {
            background-color: #e9ecef;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: 600;
        }
        
        .schedule-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        
        .schedule-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .event-detail {
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #4285f4;
        }
        
        .event-time {
            font-weight: bold;
            color: #4285f4;
        }
        
        .event-title {
            font-weight: bold;
            margin: 5px 0;
        }
        
        .event-description {
            color: #666;
            font-size: 14px;
        }
        
        .whatsapp-link {
            color: #25D366;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: rgba(37, 211, 102, 0.1);
            transition: background-color 0.2s;
        }
        
        .whatsapp-link:hover {
            background-color: rgba(37, 211, 102, 0.2);
            text-decoration: none;
            color: #128C7E;
        }
        
        .whatsapp-icon {
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .calendar-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .status-legend {
                flex-direction: column;
                gap: 10px;
            }
            
            .calendar-layout {
                flex-direction: column;
            }
            
            .schedule-section {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    @auth
    Sudah Login
    <form action="/logout" method="POST">
        @csrf
        <button>Logout</button>
    </form>

    <div class="calendar-container">
        <div class="calendar-header">
            <div class="calendar-title">📅 Kalender - Status Sibuk/Tersedia</div>
        </div>

        <div class="status-legend">
            <div class="status-item">
                <div class="status-color busy-color"></div>
                <span><strong>Sibuk:</strong> Waktu tidak tersedia</span>
            </div>
            <div class="status-item">
                <div class="status-color" style="background-color: transparent; border: 1px dashed #ddd"></div>
                <span><strong>Tersedia:</strong> Waktu kosong</span>
            </div>
        </div>

        <!-- Loading indicator -->
        <div id="loading" class="loading">
            ⏳ Memuat data kalender...
        </div>

        <!-- Layout baru dengan kalender dan tabel jadwal -->
        <div class="calendar-layout">
            <!-- Kolom kiri: Kalender -->
            <div class="calendar-section">
                <div id="calendar"></div>
            </div>
            
            <!-- Kolom kanan: Tabel Jadwal -->
            <div class="schedule-section">
                <div class="schedule-header">
                    <div class="schedule-title">Jadwal Harian</div>
                    <div class="selected-date" id="selectedDate">Pilih tanggal di kalender</div>
                </div>
                <div id="scheduleContent">
                    <p class="no-events">Silakan pilih tanggal di kalender untuk melihat jadwal.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>
    
    <!-- File JavaScript terpisah -->
    <script src="{{ asset('js/calendar.js') }}"></script>

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
        <h2>Create Service</h2>
        <form action="/create-service" method="POST" enctype="multipart/form-data">
            @csrf
            <input name="name" type="text" placeholder="Nama Layanan...">
            <textarea name="desc" type="text" placeholder="Deskripsi Layanan..."></textarea>
            <input type="file" name="image">
            <button>Create Service</button>
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
            <input name="tahun_pengalaman" type="number" placeholder="Tahun Pengalaman...">
            <input name="proyek_selesai" type="number" placeholder="Jumlah Proyek Selesai..."></input>
            <input name="klien_puas" type="number" placeholder="Jumlah Klien Puas...">
            <input name="sebaran_kota" type="number" placeholder="Sebaran Kota..."></input>
            <button>Create Statistic</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Contact</h2>
        <form action="/create-contact" method="POST">
            @csrf
            <input name="no_telp" type="text" placeholder="No Telepon...">
            <input name="alamat" type="text" placeholder="Alamat..."></input>
            <input name="link_gmaps" type="text" placeholder="Link Google Maps...">
            <input name="email" type="email" placeholder="Email..."></input>
            <input name="url_instagram" type="text" placeholder="Link Instagram..."></input>
            <input name="url_facebook" type="text" placeholder="Link Facebook..."></input>
            <input name="url_threads" type="text" placeholder="Link Threads..."></input>
            <input name="url_tiktok" type="text" placeholder="Link Tiktok..."></input>
            <input name="url_youtube" type="text" placeholder="Link Youtube..."></input>
            <input name="url_twitter" type="text" placeholder="Link Twiiter..."></input>
            <button>Create Contact</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Owner Profile</h2>
        <form action="/create-owner-profile" method="POST" enctype="multipart/form-data">
            @csrf
            <input name="name" type="text" placeholder="Nama Owner...">
            <textarea name="desc" type="text" placeholder="Deskripsi..."></textarea>
            <input name="url_instagram" type="text" placeholder="Link Instagram...">
            <input name="url_linkedin" type="text" placeholder="Link Linkedin...">
            <input type="file" name="image">
            <button>Create Owner Profile</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Visi Misi</h2>
        <form action="/create-visi-misi" method="POST" enctype="multipart/form-data">
            @csrf
            <textarea name="visi" type="text" placeholder="Visi..."></textarea>
            <textarea name="misi" type="text" placeholder="Misi..."></textarea>
            <button>Create Visi Misi</button>
        </form>
    </div>

    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Create Keunggulan</h2>
        <form action="/create-benefit" method="POST" enctype="multipart/form-data">
            @csrf
            <input name="title" type="text" placeholder="Judul Keunggulan...">
            <textarea name="desc" type="text" placeholder="Deskripsi..."></textarea>
            <input name="image" type="file" placeholder="Gambar..">
            <button>Create Keunggulan</button>
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

    @if(isset($srvcs) && $srvcs->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Services</h2>
        @foreach ($srvcs as $srvc)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$srvc['name']}}</h3>
            <img src="{{ asset('storage/' . $srvc->image) }}" alt="{{$srvc['name']}}" style="max-width: 200px;">
            {{$srvc['desc']}}
        </div>
        <p><a href="/edit-service/{{$srvc->id}}">Edit</a></p>
        <form action="/delete-service/{{$srvc->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Services</h2>
        <p>No Services available</p>
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

    @if(isset($statis) && $statis->count() > 0)
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
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Statistic</h2>
        <p>No Statistic available</p>
    </div>
    @endif
    
    @if(isset($cont) && $cont->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Contact</h2>
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$cont->first()->name}}</h3>
            {{$cont->first()->no_telp}}
            {{$cont->first()->alamat}}
            {{$cont->first()->email}}
            {{$cont->first()->link_gmaps}}
            {{$cont->first()->url_instagram}}
            {{$cont->first()->url_facebook}}
            {{$cont->first()->url_threads}}
            {{$cont->first()->url_tiktok}}
            {{$cont->first()->url_youtube}}
            {{$cont->first()->url_twitter}}
        </div>
        <p><a href="/edit-contact/{{$cont->first()->id}}">Edit</a></p>
        <form action="/delete-contact/{{$cont->first()->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Owner Profile</h2>
        <p>No Owner Profile available</p>
    </div>
    @endif

    @if(isset($owp) && $owp->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Owner Profile</h2>
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$owp->first()->name}}</h3>
            {{$owp->first()->desc}}
            {{$owp->first()->url_instagram}}
            {{$owp->first()->url_linkedin}}
            <img src="{{ asset('storage/' . $owp->first()->image) }}" alt="{{$owp->first()->name}}" style="max-width: 200px;">
        </div>
        <p><a href="/edit-owner-profile/{{$owp->first()->id}}">Edit</a></p>
        <form action="/delete-owner-profile/{{$owp->first()->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Owner Profile</h2>
        <p>No Owner Profile available</p>
    </div>
    @endif

    @if(isset($faqs) && $faqs->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>visi misi</h2>
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            {{$VM->first()->visi}}
            {{$VM->first()->misi}}
        </div>
        <p><a href="/edit-visi-misi/{{$VM->first()->id}}">Edit</a></p>
        <form action="/delete-visi-misi/{{$VM->first()->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>visi misi</h2>
        <p>No visi misi available</p>
    </div>
    @endif

    @if(isset($tstmns) && $tstmns->count() > 0)
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Keunggulan</h2>
        @foreach ($bnfts as $bnft)
        <div style="background-color: gray; padding: 10px; margin: 10px;">
            <h3>{{$bnft['title']}}</h3>
            {{$bnft['desc']}}
            <img src="{{ asset('storage/' . $bnft->image) }}" alt="{{$bnft['name']}}" style="max-width: 200px;">
        </div>
        <p><a href="/edit-benefit/{{$bnft->id}}">Edit</a></p>
        <form action="/delete-benefit/{{$bnft->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
        @endforeach
    </div>
    @else
    <div style="border: 3px solid black; margin-bottom: 10px;">
        <h2>Keunggulan</h2>
        <p>No Keunggulan available</p>
    </div>
    @endif

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
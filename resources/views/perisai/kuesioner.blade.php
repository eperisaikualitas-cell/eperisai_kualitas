<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kelola Kuesioner - E-PERISAI</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo1.png') }}">
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; min-height: 100vh; padding: 20px; color: #333; 
            background-image: url('{{ asset("bg-patnal.jpg") }}'); 
            background-size: cover; background-position: center; background-attachment: fixed; 
        }

        .container { 
            max-width: 1000px; width: 100%; margin: auto; padding: 30px; border-radius: 15px; position: relative;
            background: rgba(255, 255, 255, 0.6); 
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2); 
        }
        
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid rgba(0,0,0,0.1); padding-bottom: 15px; flex-wrap: wrap; gap: 15px;}
        .btn-back { background: rgba(108, 117, 125, 0.9); color: white; border: none; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block;}
        .btn-back:hover { background: #5a6268; }
        h2 { color: #1a73e8; margin: 0; font-size: 20px; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);}
        
        .tab { overflow: hidden; border-bottom: 2px solid #1a73e8; margin-bottom: 20px; display: flex; flex-wrap: wrap; }
        .tab button { background-color: rgba(255,255,255,0.5); border: none; outline: none; cursor: pointer; padding: 12px 15px; transition: 0.3s; font-weight: bold; border-radius: 8px 8px 0 0; margin-right: 5px; color: #555; flex: 1; min-width: 120px; margin-bottom: 5px; font-size: 13px;}
        .tab button.active { background-color: #1a73e8; color: white; }
        .tabcontent { display: none; animation: fadeEffect 0.5s; }
        @keyframes fadeEffect { from {opacity: 0;} to {opacity: 1;} }

        .form-box { background: rgba(255, 255, 255, 0.5); padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.5); margin-bottom: 20px; }
        .form-box-flex { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { margin-bottom: 15px; flex: 1; min-width: 200px; }
        .form-group.large { flex: 3; }
        .form-group label { font-weight: bold; display:block; margin-bottom: 5px; font-size: 13px;}
        .form-group select, .form-group input { width: 100%; padding: 10px; border: 1px solid rgba(0,0,0,0.1); border-radius: 4px; background: rgba(255,255,255,0.8);}
        .btn-submit { background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; transition:0.3s;}
        .btn-submit:hover { background: #218838; }
        .btn-delete { background: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size:11px; width: 100%; transition:0.3s;}
        .btn-delete:hover { background: #c82333; }
        
        .table-responsive { width: 100%; display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 8px;}
        table { width: 100%; min-width: 600px; border-collapse: collapse; margin-top: 10px; font-size: 13px; background: rgba(255,255,255,0.7); }
        th { background: rgba(26, 115, 232, 0.9); color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid rgba(0,0,0,0.1); }
        tr:nth-child(even) { background: rgba(255, 255, 255, 0.5); }
        
        .alert { padding: 12px; background: rgba(212, 237, 218, 0.9); color: #155724; border-radius: 4px; margin-bottom: 20px; font-weight: bold;}

        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; }
            .header-bar { flex-direction: column; text-align: center; }
            .btn-back { width: 100%; }
            .form-box-flex { flex-direction: column; align-items: stretch; gap: 0;}
            .form-group.large { margin-bottom: 15px; }
            .tab button { width: 100%; border-radius: 4px; margin-right: 0; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <h2>⚙️ KELOLA BANK SOAL</h2>
        <a href="{{ route('perisai.index') }}" class="btn-back">⬅ KEMBALI KE FORM</a>
    </div>

    @if(session('success'))
        <div class="alert">✅ {{ session('success') }}</div>
    @endif

    <div class="form-box">
        <form action="{{ route('perisai.kuesioner.store') }}" method="POST">
            @csrf
            <div class="form-box-flex">
                <div class="form-group">
                    <label>Kategori Soal:</label>
                    <select name="kategori" id="select_kategori" required>
                        <option value="Fasilitas">Fasilitas Penunjang</option>
                        <option value="Paspor">Penerbitan Paspor</option>
                        <option value="IzinTinggal">Izin Tinggal</option>
                        <option value="TPI">TPI</option>
                    </select>
                </div>
                <div class="form-group large">
                    <label>Butir Pertanyaan Baru:</label>
                    <input type="text" name="pertanyaan" placeholder="Ketik kalimat butir pemeriksaan di sini..." required autocomplete="off">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <button type="submit" class="btn-submit">➕ TAMBAH SOAL</button>
                </div>
            </div>
        </form>
    </div>

    @php
        $groupedSoal = $soal->groupBy('kategori');
        $daftarKategori = ['Fasilitas' => 'FASILITAS PENUNJANG', 'Paspor' => 'PENERBITAN PASPOR', 'IzinTinggal' => 'IZIN TINGGAL', 'TPI' => 'T.P.I'];
    @endphp

    <div class="tab">
        @foreach($daftarKategori as $key => $title)
            <button type="button" class="tablinks {{ $loop->first ? 'active' : '' }}" onclick="openTab(event, '{{ $key }}')">
                {{ $title }} ({{ isset($groupedSoal[$key]) ? count($groupedSoal[$key]) : 0 }})
            </button>
        @endforeach
    </div>

    @foreach($daftarKategori as $key => $title)
        <div id="{{ $key }}" class="tabcontent" style="{{ $loop->first ? 'display:block;' : '' }}">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Butir Pertanyaan ({{ $title }})</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($groupedSoal[$key]) && count($groupedSoal[$key]) > 0)
                            @foreach($groupedSoal[$key] as $s)
                                <tr>
                                    <td style="text-align:center;">{{ $loop->iteration }}</td>
                                    <td>{{ $s->pertanyaan }}</td>
                                    <td style="text-align:center;">
                                        <form action="{{ route('perisai.kuesioner.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                                            @csrf
                                            <button type="submit" class="btn-delete">HAPUS</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="3" style="text-align:center; padding: 20px; color:#555; font-style:italic;">Belum ada soal di kategori ini.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    document.getElementById('select_kategori').value = tabName;
}
</script>
</body>
</html>
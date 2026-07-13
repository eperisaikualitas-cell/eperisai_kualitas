<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>E-PERISAI KUALITAS</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo1.png') }}">
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; padding: 20px; color: #333; margin: 0; min-height: 100vh; position: relative;
            background-image: url('{{ asset("bg-patnal.jpg") }}');
            background-size: cover; background-position: center; background-attachment: fixed;
        }

        .container { 
            max-width: 1200px; width: 100%; margin: auto; padding: 30px; border-radius: 12px; position: relative;
            background: rgba(255, 255, 255, 0.6); 
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(0,0,0,0.2); 
        }
        
        .user-bar { display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.6); padding: 10px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.5); font-size: 14px; flex-wrap: wrap; gap: 10px; }
        
        .btn-logout, .btn-manage, .btn-history, .btn-tim { display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 15px; border-radius: 4px; font-weight: bold; text-decoration: none; color: white; border: none; cursor: pointer; font-size: 13px; margin-right: 8px; transition: 0.3s; }
        .btn-logout { background: #dc3545; margin-right:0; } .btn-logout:hover { background: #bd2130; }
        .btn-manage { background: #6f42c1; } .btn-manage:hover { background: #5a32a3; }
        .btn-history { background: #17a2b8; } .btn-history:hover { background: #138496; }
        .btn-tim { background: #e83e8c; } .btn-tim:hover { background: #c23375; }

        .header-logo { text-align: center; margin-bottom: 10px; }
        .header-logo img { width: 120px; height: auto; max-width: 100%; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a73e8; text-align: center; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; font-size: 22px; text-shadow: 1px 1px 2px rgba(255,255,255,0.8); }
        .peringatan-text { color: #dc3545; font-size: 12px; text-align: center; font-weight: bold; margin-bottom: 20px; background: rgba(255,255,255,0.7); padding: 5px; border-radius: 4px;}

        .config-box { background: rgba(255, 255, 255, 0.5); padding: 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.5); margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap;}
        .config-box .form-group { flex: 1; min-width: 250px; }
        .config-box label { font-weight: bold; font-size: 13px; color: #333; margin-bottom: 5px; display: block;}
        .config-box input, .config-box select { width: 100%; padding: 10px; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; background: rgba(255,255,255,0.8); }

        .tab { overflow: hidden; border-bottom: 2px solid #1a73e8; margin-bottom: 20px; display: flex; flex-wrap: wrap; }
        .tab button { background-color: rgba(255, 255, 255, 0.5); border: none; outline: none; cursor: pointer; padding: 14px 15px; transition: 0.3s; font-weight: bold; border-radius: 8px 8px 0 0; margin-right: 5px; color: #555; flex: 1; min-width: 120px; margin-bottom: 5px; font-size: 13px; }
        .tab button.active { background-color: #1a73e8; color: white; }
        .tabcontent { display: none; animation: fadeEffect 0.5s; }
        @keyframes fadeEffect { from {opacity: 0;} to {opacity: 1;} }
        
        .table-responsive { width: 100%; display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 8px; }
        table { width: 100%; min-width: 900px; border-collapse: collapse; margin-top: 10px; font-size: 13px; background: rgba(255,255,255,0.7); }
        th { background: rgba(26, 115, 232, 0.9); color: white; padding: 12px; position: sticky; top: 0; z-index: 10; }
        td { padding: 10px; border: 1px solid rgba(0,0,0,0.1); vertical-align: top; }
        tr:nth-child(even) { background: rgba(255, 255, 255, 0.5); }
        select, input[type="text"] { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc; background: rgba(255,255,255,0.9); }
        
        .sticky-footer { position: sticky; bottom: 0; background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); padding: 15px; border-top: 1px solid rgba(255,255,255,0.5); text-align: center; z-index: 100; box-shadow: 0 -4px 15px rgba(0,0,0,0.1); display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; border-radius: 0 0 12px 12px;}
        
        .btn-hitung { background: #28a745; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.3s; flex: 1; min-width: 200px; max-width: 300px; text-decoration:none; display:flex; justify-content:center; align-items:center; }
        .btn-excel { background: #107c10; } .btn-word { background: #0056b3; }
        .btn-hitung:hover { transform: translateY(-2px); opacity: 0.9; }

        .hasil-box { margin-top: 30px; padding: 25px; border-radius: 8px; text-align: center; }
        .score-big { font-size: 48px; font-weight: bold; display: block; margin: 10px 0; }
        .detail-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; margin-top: 20px; margin-bottom: 20px; }
        .detail-item { background: rgba(255,255,255,0.6); padding: 10px 15px; border-radius: 8px; border: 1px solid currentColor; min-width: 150px; }

        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; }
            .user-bar { flex-direction: column; align-items: stretch; text-align: center; }
            .user-bar > div:last-child { display: flex; flex-direction: column; gap: 8px; }
            .btn-manage, .btn-history, .btn-tim { margin-right: 0; width: 100%; }
            form[action*="logout"] { display: block !important; width: 100%; }
            .btn-logout { width: 100%; }
            .tab button { width: 100%; border-radius: 4px; margin-right: 0; }
            .sticky-footer { flex-direction: column; }
            .btn-hitung { max-width: 100%; width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="user-bar">
        <div>User Aktif: <strong>{{ Auth::user()->name }}</strong></div>
        <div>
            <a href="{{ route('perisai.tim') }}" class="btn-tim">👥 KELOLA TIM</a>
            <a href="{{ route('perisai.kuesioner') }}" class="btn-manage">⚙️ KUESIONER</a>
            <a href="{{ route('perisai.riwayat') }}" class="btn-history">📋 RIWAYAT</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline-block; margin:0; padding:0; vertical-align:middle;">
                @csrf
                <button type="submit" class="btn-logout">LOGOUT</button>
            </form>
        </div>
    </div>

    <div class="header-logo">
        <img src="{{ asset('logo-patnal.jpg') }}" alt="Logo PATNAL" onerror="this.style.display='none'">
    </div>
    
    <h2>E-PERISAI KUALITAS : Formulir Penjaminan Kualitas</h2>
    <p class="peringatan-text">*Harap tulis nama satker dengan benar (Terdapat kata 'TPI' atau 'NON TPI') agar sistem tidak salah mengartikan kategori.</p>

    @if($soal->isEmpty())
        <div style="text-align:center; padding:50px; color:red; font-weight:bold;">Bank Soal masih kosong! Silakan isi melalui menu Kelola Kuesioner.</div>
    @else
        <form method="POST" action="{{ route('perisai.store') }}">
            @csrf
            <div class="config-box">
                <div class="form-group">
                    <label>Nama Satker yang dinilai:</label>
                    <input type="text" name="nama_satker" id="input_nama_satker" placeholder="Contoh: Kanim Kelas II Non TPI Wonosobo" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Jenis Satuan Kerja:</label>
                    <select name="jenis_satker" id="select_jenis_satker" onchange="toggleTPI()">
                        <option value="TPI">TPI</option>
                        <option value="NON_TPI">NON TPI</option>
                    </select>
                </div>
                <!-- BAGIAN BARU: DROPDOWN TIM PENILAI -->
                <div class="form-group">
                    <label>Penandatangan BA (Cetak Word):</label>
                    <select name="penandatangan_id">
                        <option value="">-- Cetak Default --</option>
                        @php $tim_penilai = \App\Models\TimPenilai::all(); @endphp
                        @foreach($tim_penilai as $tim)
                            <option value="{{ $tim->id }}">{{ $tim->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="tab">
                @foreach($soal as $kategori => $items)
                    <button type="button" class="tablinks {{ $loop->first ? 'active' : '' }}" id="btn_tab_{{ $kategori }}" onclick="openTab(event, '{{ $kategori }}')">{{ strtoupper($kategori) }}</button>
                @endforeach
            </div>

            @foreach($soal as $kategori => $items)
                <div id="{{ $kategori }}" class="tabcontent" style="{{ $loop->first ? 'display:block;' : '' }}">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Butir Pemeriksaan {{ $kategori }}</th>
                                    <th width="12%">Ya/Tidak</th>
                                    <th width="12%">Nilai Kepatuhan</th>
                                    <th width="22%">Temuan Ketidaksesuaian</th>
                                    <th width="22%">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->pertanyaan }}
                                        <input type="hidden" name="soal_{{ $kategori }}[]" value="{{ $item->id }}">
                                        <input type="hidden" name="teks_{{ $kategori }}[]" value="{{ $item->pertanyaan }}">
                                    </td>
                                    <td>
                                        <select name="yt_{{ $kategori }}[]">
                                            <option value="Ya">Ya</option><option value="Tidak">Tidak</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="val_{{ $kategori }}[]">
                                            <option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option><option value="0">0</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="tm_{{ $kategori }}[]" placeholder="Temuan..."></td>
                                    <td><input type="text" name="ct_{{ $kategori }}[]" placeholder="Catatan..."></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div class="sticky-footer">
                <button type="submit" name="action" value="hitung" class="btn-hitung">HITUNG & SIMPAN REKAPITULASI</button>
            </div>
        </form>
    @endif

    @if(isset($hasil))
        <div class="hasil-box" style="background: {{ $hasil['warna'] }}; color: {{ $hasil['teks'] }}; border: 2px solid {{ $hasil['teks'] }}; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            <h3>HASIL AKHIR: {{ strtoupper($hasil['satker']) }}</h3>
            <span class="score-big">{{ number_format($hasil['total'], 2) }}</span>
            PREDIKAT: <strong>{{ $hasil['predikat'] }}</strong>
            
            <div class="detail-grid">
                @foreach($hasil['detail'] as $kategori => $skor)
                    <div class="detail-item"><strong>{{ $kategori }}</strong><br><span>{{ number_format($skor, 2) }}</span></div>
                @endforeach
            </div>

            <div style="display:flex; justify-content:center; gap:15px; flex-wrap:wrap;">
                <a href="{{ route('perisai.riwayat.excel', $hasil['id']) }}" class="btn-hitung btn-excel">📥 CETAK EXCEL</a>
                <!-- BAGIAN BARU: MENGIRIM ID PEGAWAI DARI FORM KE CETAK WORD -->
                <form action="{{ route('perisai.riwayat.word', $hasil['id']) }}" method="GET" style="display:inline;">
                    <input type="hidden" name="penandatangan_id" id="hidden_penandatangan_id">
                    <button type="submit" class="btn-hitung btn-word" onclick="document.getElementById('hidden_penandatangan_id').value = document.querySelector('select[name=penandatangan_id]').value;">📄 CETAK WORD</button>
                </form>
            </div>
        </div>
    @endif
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
}
function toggleTPI() {
    var jenis = document.getElementById("select_jenis_satker").value;
    var btnTpi = document.getElementById("btn_tab_TPI");
    var contentTpi = document.getElementById("TPI");
    if(btnTpi) {
        if (jenis === 'NON_TPI') {
            btnTpi.style.display = 'none'; contentTpi.style.display = 'none';
            if (btnTpi.classList.contains("active")) { document.getElementsByClassName("tablinks")[0].click(); }
        } else {
            btnTpi.style.display = 'inline-block';
        }
    }
}
document.getElementById('input_nama_satker').addEventListener('input', function() {
    let nama = this.value.toUpperCase();
    let jenisSelect = document.getElementById('select_jenis_satker');
    if(nama.includes('NON TPI') || nama.includes('NON-TPI')) { jenisSelect.value = 'NON_TPI'; } 
    else if(nama.includes('TPI')) { jenisSelect.value = 'TPI'; }
    toggleTPI();
});
document.addEventListener("DOMContentLoaded", function() { toggleTPI(); });
</script>
</body>
</html>
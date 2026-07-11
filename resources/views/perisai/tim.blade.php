<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kelola Tim Penilai - E-PERISAI</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo1.png') }}">
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; min-height: 100vh; padding: 20px; color: #333; 
            background-image: url('{{ asset("bg-patnal.jpg") }}'); 
            background-size: cover; background-position: center; background-attachment: fixed; 
        }

        .container { 
            max-width: 900px; width: 100%; margin: auto; padding: 30px; border-radius: 15px; position: relative;
            background: rgba(255, 255, 255, 0.6); 
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.6); 
            box-shadow: 0 8px 32px rgba(0,0,0,0.2); 
        }
        
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid rgba(0,0,0,0.1); padding-bottom: 15px; }
        .btn-back { background: rgba(108, 117, 125, 0.9); color: white; border: none; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; }
        .form-box { background: rgba(255,255,255,0.5); padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.5); }
        input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid rgba(0,0,0,0.1); border-radius: 4px; background: rgba(255,255,255,0.8); }
        
        button[type="submit"] { background: #1a73e8; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s;}
        button[type="submit"]:hover { background: #1557b0; }
        .btn-cancel { background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 5px; display: none; transition: 0.3s;}
        
        .table-responsive { width: 100%; display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 8px;}
        table { width: 100%; min-width: 600px; border-collapse: collapse; margin-top: 10px; background: rgba(255,255,255,0.7); }
        th { background: rgba(26, 115, 232, 0.9); color: white; padding: 12px; }
        td { padding: 10px; border: 1px solid rgba(0,0,0,0.1); text-align: center; }
        
        .btn-delete { background: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-edit { background: #ffc107; color: #333; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-right: 5px; }

        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; }
            .header-bar { flex-direction: column; text-align: center; gap:10px; }
            .btn-back { width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <h2 style="margin:0; color:#1a73e8;">👥 KELOLA TIM PENILAI</h2>
        <a href="{{ route('perisai.index') }}" class="btn-back">⬅ KEMBALI</a>
    </div>

    @if(session('success'))
        <div style="background: rgba(212, 237, 218, 0.9); color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight:bold;">{{ session('success') }}</div>
    @endif

    <div class="form-box">
        <h3 id="form-title" style="margin-top:0; color:#1a73e8;">+ Tambah Pegawai</h3>
        <form id="form-tim" action="{{ route('perisai.tim.store') }}" method="POST">
            @csrf
            <input type="text" id="input-nama" name="nama" placeholder="Nama Lengkap (Contoh: M. Iqbal Ma'ruf)" required autocomplete="off">
            <input type="text" id="input-nip" name="nip" placeholder="NIP Pegawai (Contoh: 199001012014051001)" required autocomplete="off">
            <input type="text" id="input-jabatan" name="jabatan" placeholder="Jabatan (Contoh: Koordinator Fungsi Penjaminan Kualitas)" required autocomplete="off">
            <button type="submit" id="btn-submit">SIMPAN DATA PEGAWAI</button>
            <button type="button" id="btn-cancel" class="btn-cancel" onclick="cancelEdit()">BATAL EDIT</button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Pegawai</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tim as $t)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="text-align: left; font-weight:bold;">{{ $t->nama }}</td>
                    <td style="text-align: center;">{{ $t->nip }}</td> <td style="text-align: left;">{{ $t->jabatan }}</td>
                    <td>
                        <div style="display:flex; justify-content:center;">
                            <button type="button" class="btn-edit" onclick="editPegawai('{{ $t->id }}', '{{ addslashes($t->nama) }}', '{{ addslashes($t->nip) }}', '{{ addslashes($t->jabatan) }}')">Edit</button>
                            <form action="{{ route('perisai.tim.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding: 20px; color:#555;">Belum ada data pegawai yang ditambahkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // FUNGSI JAVASCRIPT MENERIMA DATA NIP
    function editPegawai(id, nama, nip, jabatan) {
        document.getElementById('input-nama').value = nama;
        document.getElementById('input-nip').value = nip; // Isi Form NIP
        document.getElementById('input-jabatan').value = jabatan;
        
        document.getElementById('form-tim').action = '/tim/update/' + id;
        document.getElementById('form-title').innerHTML = '✏️ Edit Data Pegawai';
        document.getElementById('btn-submit').innerHTML = 'UPDATE DATA PEGAWAI';
        document.getElementById('btn-submit').style.background = '#ffc107';
        document.getElementById('btn-submit').style.color = '#333';
        document.getElementById('btn-cancel').style.display = 'block';
    }

    function cancelEdit() {
        document.getElementById('input-nama').value = '';
        document.getElementById('input-nip').value = ''; // Kosongkan Form NIP
        document.getElementById('input-jabatan').value = '';
        
        document.getElementById('form-tim').action = '{{ route("perisai.tim.store") }}';
        document.getElementById('form-title').innerHTML = '+ Tambah Pegawai';
        document.getElementById('btn-submit').innerHTML = 'SIMPAN DATA PEGAWAI';
        document.getElementById('btn-submit').style.background = '#1a73e8';
        document.getElementById('btn-submit').style.color = 'white';
        document.getElementById('btn-cancel').style.display = 'none';
    }
</script>
</body>
</html>
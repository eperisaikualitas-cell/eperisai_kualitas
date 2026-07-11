<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Riwayat Penilaian - E-PERISAI</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo1.png') }}">
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; min-height: 100vh; padding: 20px; color: #333; 
            background-image: url('{{ asset("bg-patnal.jpg") }}'); 
            background-size: cover; background-position: center; background-attachment: fixed; 
        }

        .container { 
            max-width: 1100px; width: 100%; margin: auto; padding: 30px; border-radius: 15px; position: relative;
            background: rgba(255, 255, 255, 0.6); 
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.6); 
            box-shadow: 0 8px 32px rgba(0,0,0,0.2); 
        }
        
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid rgba(0,0,0,0.1); padding-bottom: 15px; flex-wrap: wrap; gap: 15px;}
        .btn-back { background: rgba(108, 117, 125, 0.9); color: white; border: none; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block;}
        .btn-back:hover { background: #5a6268; }
        h2 { color: #1a73e8; margin: 0; font-size: 20px; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);}
        
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; font-weight: bold; text-align: center; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }

        .table-responsive { width: 100%; display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 8px;}
        table { width: 100%; min-width: 800px; border-collapse: collapse; margin-top: 10px; font-size: 14px; background: rgba(255,255,255,0.7); }
        th { background: rgba(26, 115, 232, 0.9); color: white; padding: 12px; white-space: nowrap; }
        td { padding: 12px; border: 1px solid rgba(0,0,0,0.1); text-align: center; vertical-align: middle; }
        tr:nth-child(even) { background: rgba(255, 255, 255, 0.5); }
        tr:hover { background: rgba(255, 255, 255, 0.9); }
        
        .badge { padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; display: inline-block; white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #000;}
        
        /* 5 WARNA BADGE BARU */
        .bg-cat-a { background: #66FF66; } /* Kategori A */
        .bg-cat-b { background: #32CD32; } /* Kategori B */
        .bg-cat-c { background: #FFFF00; } /* Kategori C */
        .bg-cat-d { background: #FF0000; color: #fff;} /* Kategori D */
        .bg-cat-e { background: #FF0000; color: #fff;} /* Kategori E */

        .action-group { display: flex; justify-content: center; gap: 4px; align-items: center; }
        .btn-sm { padding: 6px 10px; font-size: 11px; color: white; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; white-space: nowrap; border: none; cursor: pointer; font-family: inherit;}
        .btn-sm-excel { background-color: #107c10; }
        .btn-sm-word { background-color: #0056b3; }
        .btn-sm-delete { background-color: #dc3545; }
        .btn-sm-excel:hover { background-color: #0c5c0c; }
        .btn-sm-word:hover { background-color: #004494; }
        .btn-sm-delete:hover { background-color: #bd2130; }

        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; }
            .header-bar { flex-direction: column; text-align: center; }
            .btn-back { width: 100%; }
            .action-group { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header-bar">
        <h2>📋 DAFTAR RIWAYAT AUDIT</h2>
        <a href="{{ route('perisai.index') }}" class="btn-back">⬅ KEMBALI KE FORM</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal Audit</th>
                    <th width="22%">Nama Satker</th>
                    <th width="10%">Jenis</th>
                    <th width="13%">Nilai Akhir</th>
                    <th width="20%">Predikat</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $r)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="white-space: nowrap;">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        <td style="text-align:left; font-weight:bold;">{{ $r->nama_satker }}</td>
                        <td>{{ $r->jenis_satker }}</td>
                        <td style="font-weight:bold; font-size:16px;">{{ number_format($r->total_nilai, 2) }}</td>
                        <td>
                            @php
                                if ($r->total_nilai >= 88.00) {
                                    $bg = 'bg-cat-a';
                                } elseif ($r->total_nilai >= 78.00) {
                                    $bg = 'bg-cat-b';
                                } elseif ($r->total_nilai >= 54.00) {
                                    $bg = 'bg-cat-c';
                                } elseif ($r->total_nilai >= 32.00) {
                                    $bg = 'bg-cat-d';
                                } else {
                                    $bg = 'bg-cat-e';
                                }
                            @endphp
                            <span class="badge {{ $bg }}">{{ $r->predikat }}</span>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('perisai.riwayat.excel', $r->id) }}" class="btn-sm btn-sm-excel" title="Cetak ke Excel">EXCEL</a>
                                <a href="{{ route('perisai.riwayat.word', $r->id) }}" class="btn-sm btn-sm-word" title="Cetak ke Word">WORD</a>
                                
                                <form action="{{ route('perisai.riwayat.destroy', $r->id) }}" method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('Peringatan: Yakin ingin menghapus riwayat audit {{ $r->nama_satker }} ini? Data yang dihapus tidak bisa dikembalikan!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm btn-sm-delete" title="Hapus Riwayat">HAPUS</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding: 30px; color: #555;">Belum ada riwayat penilaian yang tersimpan di sistem.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
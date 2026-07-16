<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Riwayat Penilaian - E-PERISAI</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo1.png') }}">
    <!-- SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; min-height: 100vh; padding: 20px; color: #333; 
            background-image: url('{{ asset("images/gambar_bg.jpeg") }}'); 
            background-size: cover; background-position: center; background-attachment: fixed; 
            animation: fadeInPage 0.4s ease-out forwards;
            transition: opacity 0.4s ease-out, transform 0.4s ease-out;
        }

        .container { 
            max-width: 1100px; width: 100%; margin: auto; padding: 30px; border-radius: 15px; position: relative;
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.8); 
            box-shadow: 0 8px 32px rgba(0,0,0,0.15); 
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
        
        .bg-cat-a { background: #66FF66; }
        .bg-cat-b { background: #32CD32; }
        .bg-cat-c { background: #FFFF00; }
        .bg-cat-d { background: #FF0000; color: #fff;}
        .bg-cat-e { background: #FF0000; color: #fff;}

        .action-group { display: flex; justify-content: center; gap: 4px; align-items: center; }
        .btn-sm { padding: 6px 10px; font-size: 11px; color: white; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; white-space: nowrap; border: none; cursor: pointer; font-family: inherit;}
        .btn-sm-excel { background-color: #107c10; }
        .btn-sm-word { background-color: #0056b3; }
        .btn-sm-delete { background-color: #dc3545; }
        .btn-sm-excel:hover { background-color: #0c5c0c; }
        .btn-sm-word:hover { background-color: #004494; }
        .btn-sm-delete:hover { background-color: #bd2130; }

        .pagination-container { margin-top: 25px; display: flex; justify-content: center; }
        .pagination { display: flex; padding-left: 0; list-style: none; gap: 8px; align-items: center; margin: 0; }
        .pagination li a, .pagination li span { position: relative; display: block; padding: 8px 14px; text-decoration: none; background-color: rgba(255,255,255,0.9); border: 1px solid #1a73e8; border-radius: 6px; color: #1a73e8; font-weight: bold; transition: 0.3s; }
        .pagination li a:hover { background-color: #1a73e8; color: white; }
        .pagination li.active span { background-color: #1a73e8; color: white; border-color: #1a73e8; box-shadow: 0 4px 10px rgba(26, 115, 232, 0.3); }
        .pagination li.disabled span { color: #6c757d; background-color: rgba(255,255,255,0.6); border-color: #dee2e6; cursor: not-allowed; }

        body.fade-out { opacity: 0; transform: translateY(-15px); }
        @keyframes fadeInPage { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; }
            .header-bar { flex-direction: column; text-align: center; }
            .btn-back { width: 100%; }
            .action-group { flex-wrap: wrap; }
            .pagination { flex-wrap: wrap; justify-content: center; }
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
                @forelse($riwayat as $index => $r)
                    <tr>
                        <td>{{ $riwayat->firstItem() + $index }}</td>
                        <td style="white-space: nowrap;">{{ $r->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</td>
                        <td style="text-align:left; font-weight:bold;">{{ $r->nama_satker }}</td>
                        <td>{{ $r->jenis_satker }}</td>
                        <td style="font-weight:bold; font-size:16px;">{{ number_format($r->total_nilai, 2) }}</td>
                        <td>
                            @if($r->status === 'draft')
                                <span class="badge" style="background: #ffc107; color: #000;">📝 DRAFT</span>
                            @else
                                @php
                                    if ($r->total_nilai >= 88.00) { $bg = 'bg-cat-a'; } 
                                    elseif ($r->total_nilai >= 78.00) { $bg = 'bg-cat-b'; } 
                                    elseif ($r->total_nilai >= 54.00) { $bg = 'bg-cat-c'; } 
                                    elseif ($r->total_nilai >= 32.00) { $bg = 'bg-cat-d'; } 
                                    else { $bg = 'bg-cat-e'; }
                                @endphp
                                <span class="badge {{ $bg }}">{{ $r->predikat }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                @if($r->status === 'draft')
                                    <a href="{{ route('perisai.edit', $r->id) }}" class="btn-sm" style="background: #ffc107; color: #000;">✏️ LANJUTKAN</a>
                                @else
                                    <a href="{{ route('perisai.riwayat.excel', $r->id) }}" class="btn-sm btn-sm-excel" title="Cetak ke Excel">EXCEL</a>
                                    <a href="{{ route('perisai.riwayat.word', $r->id) }}" class="btn-sm btn-sm-word" title="Cetak ke Word">WORD</a>
                                @endif
                                
                                <form action="{{ route('perisai.riwayat.destroy', $r->id) }}" method="POST" class="delete-form" style="margin: 0; display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-sm btn-sm-delete" onclick="confirmDelete(this)" title="Hapus Riwayat">HAPUS</button>
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

    @if ($riwayat->lastPage() > 1)
        <div class="pagination-container">
            <ul class="pagination">
                @if ($riwayat->onFirstPage())
                    <li class="disabled"><span>&laquo; Sebelumnya</span></li>
                @else
                    <li><a href="{{ $riwayat->previousPageUrl() }}">&laquo; Sebelumnya</a></li>
                @endif
                @for ($i = 1; $i <= $riwayat->lastPage(); $i++)
                    @if ($i == $riwayat->currentPage())
                        <li class="active"><span>{{ $i }}</span></li>
                    @else
                        <li><a href="{{ $riwayat->url($i) }}">{{ $i }}</a></li>
                    @endif
                @endfor
                @if ($riwayat->hasMorePages())
                    <li><a href="{{ $riwayat->nextPageUrl() }}">Selanjutnya &raquo;</a></li>
                @else
                    <li class="disabled"><span>Selanjutnya &raquo;</span></li>
                @endif
            </ul>
        </div>
    @endif
</div>

<script>
function confirmDelete(button) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('.delete-form').submit();
        }
    })
}

document.addEventListener("DOMContentLoaded", function() {
    const links = document.querySelectorAll('a[href]:not([href^="#"]):not([target="_blank"]):not(.btn-excel):not(.btn-word):not(.btn-sm-excel):not(.btn-sm-word)');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); 
            const targetUrl = this.href;
            document.body.classList.add('fade-out');
            setTimeout(() => { window.location.href = targetUrl; }, 350); 
        });
    });
});
</script>
</body>
</html>
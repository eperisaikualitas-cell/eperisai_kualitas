<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login E-Perisai Kualitas</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo1.png') }}">
    <style>
        * { box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; 
            /* BG BARU */
            background-image: url('{{ asset("images/gambar_bg.jpeg") }}');
            background-size: cover; background-position: center; background-attachment: fixed;
            animation: fadeInPage 0.4s ease-out forwards;
            transition: opacity 0.4s ease-out, transform 0.4s ease-out;
        }
        
        body.fade-out {
            opacity: 0;
            transform: translateY(-15px);
        }
        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-box { 
            padding: 40px; border-radius: 15px; width: 100%; max-width: 400px; text-align: center; position: relative;
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.8); 
            box-shadow: 0 8px 32px rgba(0,0,0,0.15); 
        }
        
        .login-box img { width: 100px; margin-bottom: 20px; height: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #1a73e8; margin-top: 0; margin-bottom: 25px; font-size: 24px; text-shadow: 1px 1px 2px rgba(255,255,255,0.8); }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid rgba(0,0,0,0.1); border-radius: 6px; font-size: 14px; outline: none; transition: 0.3s; background: rgba(255,255,255,0.8); }
        input:focus { border-color: #1a73e8; box-shadow: 0 0 5px rgba(26,115,232,0.5); background: white; }
        button { width: 100%; background: #1a73e8; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 6px rgba(26,115,232,0.3); }
        button:hover { background: #1557b0; transform: translateY(-2px); }
        .error { color: #dc3545; font-size: 13px; margin-bottom: 15px; text-align: left; background: rgba(248, 215, 218, 0.9); padding: 10px; border-radius: 5px; }

        @media (max-width: 480px) {
            .login-box { padding: 30px 20px; }
            .login-box img { width: 80px; margin-bottom: 15px; }
            h2 { font-size: 20px; margin-bottom: 20px; }
            input { padding: 10px; font-size: 13px; margin-bottom: 12px; }
            button { padding: 12px; font-size: 15px; }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="{{ asset('logo-patnal.jpg') }}" alt="Logo PATNAL" onerror="this.style.display='none'">
        <h2>E-PERISAI KUALITAS</h2>
        
        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf
            <input type="text" name="username" placeholder="Username" required autofocus autocomplete="off">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function() {
            document.body.classList.add('fade-out');
        });
    </script>
</body>
</html>
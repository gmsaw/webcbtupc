<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password UPC 2026</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            letter-spacing: 1px;
        }
        .header p {
            color: #e0e7ff;
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .content h2 {
            color: #111827;
            font-size: 20px;
            margin-top: 0;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }
        .warning-text {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            margin: 25px 0;
            font-size: 14px;
            color: #92400e;
        }
        .footer {
            background-color: #f9fafb;
            padding: 25px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .raw-link {
            font-size: 12px;
            color: #6b7280;
            word-break: break-all;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
        }
    </style>
</head>
<body>

    <div class="email-wrapper">
        <div class="header">
            <h1>HIMAFI UPC 2026</h1>
            <p>Udayana Physics Championship</p>
        </div>

        <div class="content">
            <h2>Halo, {{ $user->name }}!</h2>
            <p>Kami menerima permintaan untuk mereset password akun peserta Anda. Jika Anda merasa tidak pernah melakukan permintaan ini, silakan abaikan email ini dan akun Anda akan tetap aman.</p>
            
            <p>Jika Anda memang ingin mereset password, silakan klik tombol di bawah ini:</p>

            <div class="button-container">
                <a href="{{ $url }}" class="button">Reset Password Saya</a>
            </div>

            <div class="warning-text">
                <b>Penting:</b> Tautan reset password ini hanya berlaku selama 60 menit. Jangan bagikan email atau tautan ini kepada siapapun demi keamanan akun Anda.
            </div>

            <p>Terima kasih,<br><b>Panitia HIMAFI UPC 2026</b></p>

            <div class="raw-link">
                Jika Anda kesulitan mengklik tombol "Reset Password Saya", salin dan tempel URL berikut ke peramban (browser) Anda:<br>
                <a href="{{ $url }}" style="color: #4f46e5;">{{ $url }}</a>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Himpunan Mahasiswa Fisika Universitas Udayana. Hak Cipta Dilindungi.<br>
            Email ini dikirim secara otomatis oleh sistem keamanan kami.
        </div>
    </div>

</body>
</html>
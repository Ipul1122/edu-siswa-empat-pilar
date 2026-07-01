<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #d32f2f 0%, #9e1c1c 100%);
            color: #ffffff;
            padding: 32px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 32px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .body-text {
            color: #475569;
            font-size: 15px;
            margin-bottom: 32px;
        }
        .otp-container {
            background: #f1f5f9;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            margin-bottom: 32px;
        }
        .otp-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 6px;
            color: #d32f2f;
            margin: 0;
        }
        .expiry-warning {
            font-size: 13px;
            color: #dc2626;
            font-weight: 500;
            text-align: center;
            margin-bottom: 24px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Empat Pilar Kebangsaan</h1>
        </div>
        <div class="content">
            <div class="greeting">Halo Siswa,</div>
            <div class="body-text">
                {{ $body }}
            </div>
            
            <div class="otp-container">
                <div class="otp-label">Kode Verifikasi OTP Anda</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="expiry-warning">
                *Kode OTP ini hanya berlaku selama 15 menit. Jangan bagikan kode ini kepada siapapun demi keamanan akun Anda.
            </div>
            
            <div class="body-text" style="margin-bottom: 0;">
                Jika Anda tidak merasa meminta kode ini, harap abaikan email ini.
                <br><br>
                Salam hangat,<br>
                <strong>Tim Pengembang Empat Pilar</strong>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Empat Pilar SMA/K. Hak Cipta Dilindungi.</p>
            <p>Pendidikan Karakter & Kebangsaan Masa Kini</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Konfirmasi Pesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .info-box {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .total {
            background: #667eea;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Konfirmasi Pesanan</h1>
        <p>Terima kasih telah memesan paket wisata kami!</p>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $pesanan->nama }}</strong>,</p>
        <p>Pesanan Anda telah berhasil kami terima. Berikut adalah detail pesanan Anda:</p>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #667eea;">Detail Pesanan</h3>
            <div class="info-row">
                <span class="label">No. Pesanan:</span>
                <span class="value">{{ $pesanan->order_id }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal Pesanan:</span>
                <span class="value">{{ \Carbon\Carbon::parse($pesanan->waktu)->format('d F Y, H:i') }}</span>
            </div>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #667eea;">Detail Paket Wisata</h3>
            <div class="info-row">
                <span class="label">Destinasi:</span>
                <span class="value">{{ $kategori }}</span>
            </div>
            <div class="info-row">
                <span class="label">Durasi:</span>
                <span class="value">{{ $paketHari }} Hari</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal Keberangkatan:</span>
                <span class="value">{{ \Carbon\Carbon::parse($pesanan->tanggal_berangkat)->format('d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Jumlah Peserta:</span>
                <span class="value">{{ $pesanan->jumlah_orang }} Orang</span>
            </div>
            @if($pesanan->catatan)
            <div class="info-row">
                <span class="label">Catatan:</span>
                <span class="value">{{ $pesanan->catatan }}</span>
            </div>
            @endif
        </div>

        <div class="total">
            Total: Rp {{ number_format($pesanan->total, 0, ',', '.') }}
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;">
            <strong>Status Pembayaran:</strong> Menunggu Pembayaran<br>
            <p style="margin: 10px 0 0 0;">Silakan lakukan pembayaran untuk mengkonfirmasi pesanan Anda. Anda akan menerima email notifikasi setelah pembayaran berhasil diproses.</p>
        </div>

        <p style="margin-top: 30px;">Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami:</p>
        <ul>
            <li>Email: info@iconictour.com</li>
            <li>Telepon: (021) 1234-5678</li>
            <li>WhatsApp: +62 812-3456-7890</li>
        </ul>
    </div>

    <div class="footer">
        <p><strong>ICONIC TOUR</strong></p>
        <p>Travel & Tour Agency</p>
        <p style="font-size: 12px; color: #999;">Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
    </div>
</body>
</html>

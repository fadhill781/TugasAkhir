<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Status Pembayaran</title>
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
            @if($status === 'confirmed')
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            @elseif($status === 'cancelled')
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            @else
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            @endif
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
        .status-box {
            @if($status === 'confirmed')
            background: #d4edda;
            border-left: 4px solid #28a745;
            @elseif($status === 'cancelled')
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            @else
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            @endif
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>
            @if($status === 'confirmed')
            Pembayaran Berhasil!
            @elseif($status === 'cancelled')
            Pembayaran Dibatalkan
            @else
            Status Pembayaran
            @endif
        </h1>
        <p>Notifikasi Status Pembayaran Pesanan Anda</p>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $pesanan->nama }}</strong>,</p>

        <div class="status-box">
            <h3 style="margin-top: 0;">
                @if($status === 'confirmed')
                ✓ Pembayaran Anda Telah Berhasil!
                @elseif($status === 'cancelled')
                ✗ Pembayaran Dibatalkan
                @else
                ⏳ Menunggu Pembayaran
                @endif
            </h3>
            <p style="margin: 0;">
                @if($status === 'confirmed')
                Terima kasih! Pembayaran Anda telah kami terima dan pesanan Anda telah dikonfirmasi. Kami akan segera memproses pesanan Anda.
                @elseif($status === 'cancelled')
                Pembayaran untuk pesanan ini telah dibatalkan. Jika Anda masih ingin melanjutkan pesanan, silakan hubungi kami atau buat pesanan baru.
                @else
                Pesanan Anda masih menunggu pembayaran. Silakan selesaikan pembayaran untuk mengkonfirmasi pesanan Anda.
                @endif
            </p>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #667eea;">Detail Pesanan</h3>
            <div class="info-row">
                <span class="label">No. Pesanan:</span>
                <span class="value">{{ $pesanan->order_id }}</span>
            </div>
            <div class="info-row">
                <span class="label">Status:</span>
                <span class="value">
                    @if($status === 'confirmed')
                    <strong style="color: #28a745;">Pembayaran Berhasil</strong>
                    @elseif($status === 'cancelled')
                    <strong style="color: #dc3545;">Dibatalkan</strong>
                    @else
                    <strong style="color: #ffc107;">Menunggu Pembayaran</strong>
                    @endif
                </span>
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
        </div>

        <div class="total">
            Total: Rp {{ number_format($pesanan->total, 0, ',', '.') }}
        </div>

        @if($status === 'confirmed')
        <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; border-left: 4px solid #0c5460;">
            <strong>Langkah Selanjutnya:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Tim kami akan menghubungi Anda dalam 1x24 jam untuk konfirmasi lebih lanjut</li>
                <li>Pastikan nomor telepon Anda aktif: {{ $pesanan->telepon }}</li>
                <li>Siapkan dokumen yang diperlukan untuk perjalanan</li>
            </ul>
        </div>
        @endif

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

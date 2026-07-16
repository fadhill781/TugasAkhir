<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $pesanan->order_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
        }
        .invoice-details {
            background: #f3f4f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .detail-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .detail-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
        }
        .detail-value {
            display: table-cell;
            width: 60%;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .total-label {
            display: table-cell;
            text-align: right;
            padding-right: 20px;
            font-size: 16px;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            width: 200px;
            font-size: 16px;
        }
        .grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            padding-top: 10px;
            border-top: 2px solid #2563eb;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">ICONIC TOUR</div>
                <div>Travel & Tour Agency</div>
                <div>Jl. Contoh No. 123, Kota</div>
                <div>Telp: (021) 1234-5678</div>
                <div>Email: info@iconictour.com</div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div style="margin-top: 10px;">
                    <strong>No. Invoice:</strong><br>
                    {{ $pesanan->order_id }}
                </div>
                <div style="margin-top: 10px;">
                    <strong>Tanggal:</strong><br>
                    {{ \Carbon\Carbon::parse($pesanan->waktu)->format('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="section-title">Informasi Pelanggan</div>
        <div class="invoice-details">
            <div class="detail-row">
                <div class="detail-label">Nama:</div>
                <div class="detail-value">{{ $pesanan->nama }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div class="detail-value">{{ $pesanan->email }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Telepon:</div>
                <div class="detail-value">{{ $pesanan->telepon }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Status Pembayaran:</div>
                <div class="detail-value">
                    <span class="status-badge status-{{ $pesanan->status }}">
                        @if($pesanan->status == 'confirmed') LUNAS
                        @elseif($pesanan->status == 'pending') PENDING
                        @else DIBATALKAN
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Package Details -->
        <div class="section-title">Detail Paket Wisata</div>
        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $kategori }}</strong><br>
                        <small>{{ $paketHari }}</small><br>
                        <small>Tanggal Keberangkatan: {{ \Carbon\Carbon::parse($pesanan->tanggal_berangkat)->format('d F Y') }}</small>
                        @if($pesanan->catatan)
                        <br><small>Catatan: {{ $pesanan->catatan }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $pesanan->jumlah_orang }} Orang</td>
                    <td style="text-align: right;">Rp {{ number_format($hargaPerOrang, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</div>
            </div>
            <div class="total-row grand-total">
                <div class="total-label">TOTAL:</div>
                <div class="total-value">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Terima kasih atas kepercayaan Anda!</strong></p>
            <p>Invoice ini digenerate secara otomatis oleh sistem.</p>
            <p>Untuk pertanyaan, hubungi kami di info@iconictour.com atau (021) 1234-5678</p>
        </div>
    </div>
</body>
</html>

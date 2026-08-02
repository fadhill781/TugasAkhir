<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota  - {{ $noNota }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #222;
            background: #f5f5f5;
        }

        .page {
            width: 210mm;
            min-height: 148mm;
            margin: 0 auto;
            background: #fff;
            padding: 20mm 18mm;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 14px;
            border-bottom: 3px solid #1d4ed8;
            margin-bottom: 18px;
        }
        .logo-area .company-name {
            font-size: 22px;
            font-weight: 900;
            color: #1d4ed8;
            letter-spacing: 1px;
        }
        .logo-area .company-tagline {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .logo-area .company-contact {
            font-size: 11px;
            color: #475569;
            margin-top: 6px;
            line-height: 1.6;
        }
        .nota-info { text-align: right; }
        .nota-info .nota-title {
            font-size: 26px;
            font-weight: 900;
            color: #1d4ed8;
            letter-spacing: 2px;
        }
        .nota-info table {
            margin-top: 8px;
            margin-left: auto;
            border: none;
        }
        .nota-info table td {
            padding: 2px 4px;
            font-size: 12px;
            border: none;
        }
        .nota-info table td:first-child { color: #64748b; text-align: right; }
        .nota-info table td:last-child  { font-weight: 700; text-align: left; padding-left: 8px; }

        /* ── STATUS BADGE ── */
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .status-lunas     { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .status-pending   { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .status-dp        { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }

        /* ── INFO GRID ── */
        .info-grid { display: flex; gap: 16px; margin-bottom: 18px; }
        .info-box {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
        }
        .info-box .box-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1d4ed8;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dbeafe;
        }
        .info-box .row { display: flex; margin-bottom: 5px; font-size: 12px; }
        .info-box .row .label { width: 130px; color: #64748b; flex-shrink: 0; }
        .info-box .row .value { font-weight: 600; color: #1e293b; }

        /* ── ITEM TABLE ── */
        .section-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1d4ed8;
            margin-bottom: 8px;
        }
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .item-table thead tr { background: #1d4ed8; color: #fff; }
        .item-table thead th {
            padding: 9px 12px;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            letter-spacing: 0.4px;
        }
        .item-table thead th.right  { text-align: right; }
        .item-table thead th.center { text-align: center; }
        .item-table tbody tr { border-bottom: 1px solid #e2e8f0; }
        .item-table tbody tr:last-child { border-bottom: none; }
        .item-table tbody td { padding: 10px 12px; font-size: 12px; vertical-align: top; }
        .item-table tbody td.right  { text-align: right; }
        .item-table tbody td.center { text-align: center; }
        .item-table tfoot tr { background: #eff6ff; }
        .item-table tfoot td { padding: 8px 12px; font-size: 12px; }
        .item-table tfoot td.total-label { text-align: right; font-weight: 700; color: #475569; }
        .item-table tfoot td.total-value { text-align: right; font-weight: 700; }
        .item-table tfoot tr.grand-total td {
            background: #1d4ed8; color: #fff;
            font-size: 14px; font-weight: 900;
        }
        .item-name { font-weight: 700; color: #1e293b; }
        .item-sub  { font-size: 11px; color: #64748b; margin-top: 2px; }

        /* ── DP ROW ── */
        .dp-row td { background: #fefce8 !important; }

        /* ── CATATAN ── */
        .catatan-box {
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 18px;
        }
        .catatan-box strong { color: #854d0e; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-left { font-size: 11px; color: #64748b; line-height: 1.8; }
        .footer-left strong { color: #1e293b; }
        .ttd-box { text-align: center; font-size: 11px; color: #475569; }
        .ttd-box .ttd-space {
            width: 120px; height: 60px;
            border-bottom: 1px solid #94a3b8;
            margin: 0 auto 4px;
        }

        @media print {
            body { background: #fff; }
            .page { margin: 0; padding: 14mm 14mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<!-- Toolbar (hidden on print) -->
<div class="no-print" style="text-align:center; padding:16px; background:#1d4ed8;">
    <button onclick="window.print()"
        style="background:#fff; color:#1d4ed8; font-weight:800; font-size:14px;
               border:none; padding:10px 32px; border-radius:6px; cursor:pointer; margin-right:10px;">
        🖨️ Cetak Nota
    </button>
    <button onclick="window.history.back()"
        style="background:transparent; color:#fff; font-weight:700; font-size:13px;
               border:2px solid #fff; padding:9px 24px; border-radius:6px; cursor:pointer;">
        ← Kembali &amp; Edit
    </button>
</div>

<div class="page">

    <!-- HEADER -->
    <div class="header">
        <div class="logo-area">
            <div class="company-name">ICONIC</div>
            <div class="company-tagline">Travel &amp; Tour Agency</div>
            <div class="company-contact">
                Jl. Contoh No. 123, Kota<br>
                Telp: (021) 1234-5678 &nbsp;|&nbsp; info@iconictour.com
            </div>
        </div>
        <div class="nota-info">
            <div class="nota-title">NOTA</div>
            <table>
                <tr>
                    <td>No. Nota</td>
                    <td>{{ $noNota }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($status) }}">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- INFO GRID -->
    <div class="info-grid">
        <div class="info-box">
            <div class="box-title">Data Pelanggan</div>
            <div class="row"><span class="label">Nama</span><span class="value">{{ $nama }}</span></div>
            @if(!empty($telepon))
            <div class="row"><span class="label">Telepon</span><span class="value">{{ $telepon }}</span></div>
            @endif
            @if(!empty($alamat))
            <div class="row"><span class="label">Alamat</span><span class="value">{{ $alamat }}</span></div>
            @endif
        </div>
        <div class="info-box">
            <div class="box-title">Detail Perjalanan</div>
            @if(!empty($tanggalBerangkat))
            <div class="row"><span class="label">Tgl. Berangkat</span><span class="value">{{ \Carbon\Carbon::parse($tanggalBerangkat)->translatedFormat('d F Y') }}</span></div>
            @endif
            @if(!empty($jumlahOrang))
            <div class="row"><span class="label">Jumlah Peserta</span><span class="value">{{ $jumlahOrang }} Orang</span></div>
            @endif
            @if(!empty($durasi))
            <div class="row"><span class="label">Durasi</span><span class="value">{{ $durasi }}</span></div>
            @endif
        </div>
    </div>

    <!-- ITEM TABLE -->
    <div class="section-title">Rincian Pemesanan</div>
    <table class="item-table">
        <thead>
            <tr>
                <th style="width:40%">Deskripsi</th>
                <th class="center" style="width:12%">Qty</th>
                <th class="right" style="width:24%">Harga Satuan</th>
                <th class="right" style="width:24%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item['deskripsi'] }}</div>
                    @if(!empty($item['keterangan']))
                    <div class="item-sub">{{ $item['keterangan'] }}</div>
                    @endif
                </td>
                <td class="center">{{ $item['qty'] }}</td>
                <td class="right">Rp. {{ number_format($item['harga'], 0, ',', '.') }}</td>
                <td class="right">Rp. {{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php $subtotal = collect($items)->sum(fn($i) => $i['qty'] * $i['harga']); @endphp
            <tr>
                <td colspan="2"></td>
                <td class="total-label">Subtotal</td>
                <td class="total-value right">Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>

            @if(!empty($dp) && $dp > 0)
            <tr class="dp-row">
                <td colspan="2"></td>
                <td class="total-label">DP Dibayar</td>
                <td class="total-value right" style="color:#1d4ed8;">Rp. {{ number_format($dp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td class="total-label">Sisa Bayar</td>
                <td class="total-value right" style="color:#b91c1c;">Rp. {{ number_format($subtotal - $dp, 0, ',', '.') }}</td>
            </tr>
            @endif

            <tr class="grand-total">
                <td colspan="2"></td>
                <td class="total-label right" style="color:#fff;">TOTAL</td>
                <td class="total-value right">Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- CATATAN -->
    @if(!empty($catatan))
    <div class="catatan-box">
        <strong>Catatan:</strong> {{ $catatan }}
    </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-left">
            <strong>Terima kasih atas kepercayaan Anda!</strong><br>
            Nota ini merupakan bukti transaksi yang sah.<br>
            Hubungi kami: <strong>(021) 1234-5678</strong>
        </div>
        <div class="ttd-box">
            <div class="ttd-space"></div>
            <div>Hormat Kami,</div>
            <div style="margin-top:2px; font-weight:700;">ICONIC TOUR</div>
        </div>
    </div>

</div>
</body>
</html>

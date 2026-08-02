<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $noInvoice }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #f1f5f9;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 20mm 18mm 16mm;
        }

        /* ── HEADER ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 4px solid #1d4ed8;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: 900;
            color: #1d4ed8;
            letter-spacing: 1px;
        }
        .company-tagline { font-size: 11px; color: #64748b; margin-top: 2px; }
        .company-contact { font-size: 11px; color: #475569; margin-top: 8px; line-height: 1.7; }

        .invoice-meta { text-align: right; }
        .invoice-title {
            font-size: 32px;
            font-weight: 900;
            color: #1d4ed8;
            letter-spacing: 3px;
        }
        .invoice-meta table { margin-top: 10px; margin-left: auto; border: none; }
        .invoice-meta td { padding: 2px 5px; font-size: 12px; border: none; }
        .invoice-meta td:first-child { color: #64748b; text-align: right; }
        .invoice-meta td:last-child  { font-weight: 700; color: #1e293b; padding-left: 10px; }

        /* ── STATUS ── */
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .status-lunas   { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .status-pending { background: #fef9c3; color: #a16207; border: 1px solid #fde047; }
        .status-dp      { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }

        /* ── BILL TO ── */
        .bill-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }
        .bill-box {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
        }
        .bill-box .box-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1d4ed8;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #dbeafe;
        }
        .bill-box .row { display: flex; margin-bottom: 5px; font-size: 12px; }
        .bill-box .row .lbl { width: 120px; color: #64748b; flex-shrink: 0; }
        .bill-box .row .val { font-weight: 600; color: #1e293b; }

        /* ── TABLE ── */
        .section-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1d4ed8;
            margin-bottom: 8px;
        }
        .inv-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .inv-table thead tr { background: #1d4ed8; color: #fff; }
        .inv-table thead th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            letter-spacing: 0.5px;
        }
        .inv-table thead th.right  { text-align: right; }
        .inv-table thead th.center { text-align: center; }
        .inv-table tbody tr { border-bottom: 1px solid #e2e8f0; }
        .inv-table tbody tr:nth-child(even) { background: #f8fafc; }
        .inv-table tbody tr:last-child { border-bottom: 2px solid #e2e8f0; }
        .inv-table tbody td { padding: 11px 14px; font-size: 12px; vertical-align: top; }
        .inv-table tbody td.right  { text-align: right; }
        .inv-table tbody td.center { text-align: center; }

        .item-desc { font-weight: 700; color: #1e293b; }
        .item-note { font-size: 11px; color: #64748b; margin-top: 2px; }

        /* ── TOTALS ── */
        .totals-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .totals-table { width: 280px; border-collapse: collapse; }
        .totals-table td { padding: 6px 14px; font-size: 12px; }
        .totals-table .lbl { text-align: right; color: #64748b; font-weight: 600; }
        .totals-table .val { text-align: right; font-weight: 700; color: #1e293b; }
        .totals-table .sep td { border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .totals-table .grand td {
            background: #1d4ed8;
            color: #fff;
            font-size: 14px;
            font-weight: 900;
            padding: 10px 14px;
        }
        .totals-table .grand .lbl { text-align: right; color: #bfdbfe; }
        .totals-table .grand .val { text-align: right; color: #fff; }
        .totals-table .dp-row td { background: #eff6ff; color: #1d4ed8; }

        /* ── KETERANGAN ── */
        .ket-box {
            background: #fefce8;
            border: 1px solid #fde047;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .ket-box strong { color: #854d0e; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-note { font-size: 11px; color: #64748b; line-height: 1.9; }
        .footer-note strong { color: #1e293b; }
        .ttd { text-align: center; font-size: 11px; color: #475569; }
        .ttd-space { width: 130px; height: 65px; border-bottom: 1px solid #94a3b8; margin: 0 auto 5px; }

        @media print {
            body { background: #fff; }
            .page { margin: 0; padding: 12mm 14mm; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<!-- Toolbar -->
<div class="no-print" style="background:#1d4ed8; text-align:center; padding:14px 16px; display:flex; justify-content:center; gap:10px;">
    <button onclick="window.print()"
        style="background:#fff; color:#1d4ed8; font-weight:800; font-size:14px;
               border:none; padding:10px 30px; border-radius:8px; cursor:pointer; display:flex; align-items:center; gap:6px;">
        🖨️ Cetak Invoice
    </button>
    <button onclick="window.history.back()"
        style="background:transparent; color:#fff; font-weight:700; font-size:13px;
               border:2px solid rgba(255,255,255,0.7); padding:9px 24px; border-radius:8px; cursor:pointer;">
        ← Kembali &amp; Edit
    </button>
</div>

<div class="page">

    <!-- HEADER -->
    <div class="header">
        <div>
            <div class="company-name">ICONIC</div>
            <div class="company-tagline">Travel &amp; Tour Agency</div>
            <div class="company-contact">
                Jl. Contoh No. 123, Kota<br>
                Telp: (021) 1234-5678 &nbsp;|&nbsp; info@iconictour.com
            </div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">INVOICE</div>
            <table>
                <tr>
                    <td>No. Invoice</td>
                    <td>{{ $noInvoice }}</td>
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

    <!-- BILL TO / DETAIL -->
    <div class="bill-section">
        <div class="bill-box">
            <div class="box-title">Tagihan Kepada</div>
            <div class="row"><span class="lbl">Nama</span><span class="val">{{ $nama }}</span></div>
            @if(!empty($telepon))
            <div class="row"><span class="lbl">No. Telepon</span><span class="val">{{ $telepon }}</span></div>
            @endif
        </div>
        <div class="bill-box">
            <div class="box-title">Detail Perjalanan</div>
            @if(!empty($tanggalBerangkat))
            <div class="row"><span class="lbl">Tgl. Berangkat</span><span class="val">{{ \Carbon\Carbon::parse($tanggalBerangkat)->translatedFormat('d F Y') }}</span></div>
            @endif
        </div>
    </div>

    <!-- ITEM TABLE -->
    <div class="section-title">Rincian Layanan</div>
    <table class="inv-table">
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:42%">Deskripsi</th>
                <th class="center" style="width:11%">Qty</th>
                <th class="right" style="width:21%">Harga Satuan</th>
                <th class="right" style="width:21%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
            <tr>
                <td class="center" style="color:#94a3b8;">{{ $i + 1 }}</td>
                <td>
                    <div class="item-desc">{{ $item['deskripsi'] }}</div>
                    @if(!empty($item['keterangan']))
                    <div class="item-note">{{ $item['keterangan'] }}</div>
                    @endif
                </td>
                <td class="center">{{ $item['qty'] }}</td>
                <td class="right">Rp. {{ number_format($item['harga'], 0, ',', '.') }}</td>
                <td class="right">Rp. {{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALS -->
    @php
        $subtotal = collect($items)->sum(fn($i) => $i['qty'] * $i['harga']);
        $dpVal    = !empty($dp) ? intval($dp) : 0;
    @endphp
    <div class="totals-wrap">
        <table class="totals-table">
            <tr>
                <td class="lbl">Subtotal</td>
                <td class="val">Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($dpVal > 0)
            <tr class="dp-row">
                <td class="lbl">DP Dibayar</td>
                <td class="val">Rp. {{ number_format($dpVal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="lbl" style="color:#b91c1c;">Sisa Tagihan</td>
                <td class="val" style="color:#b91c1c;">Rp. {{ number_format($subtotal - $dpVal, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="grand">
                <td class="lbl">TOTAL</td>
                <td class="val">Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- KETERANGAN -->
    @if(!empty($keterangan))
    <div class="ket-box">
        <strong>Keterangan:</strong> {{ $keterangan }}
    </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-note">
            <strong>Terima kasih atas kepercayaan Anda!</strong><br>
            Invoice ini merupakan bukti tagihan yang sah.<br>
            Pembayaran dapat dilakukan melalui transfer ke rekening perusahaan.<br>
            Informasi: <strong>(021) 1234-5678</strong>
        </div>
        <div class="ttd">
            <div class="ttd-space"></div>
            <div>Hormat Kami,</div>
            <div style="font-weight:800; margin-top:3px;">ICONIC TOUR</div>
        </div>
    </div>

</div>
</body>
</html>

@use('App\Support\Format')
@php
    $isLunas = $faktur->status === 'lunas';
    $sisaTagihan = max(0, (float) $faktur->total_tagihan - (float) $faktur->total_dibayar);
    $tanggalNota = Format::date($faktur->tanggal_nota, 'd F Y');
    $tanggalSetor = Format::date($faktur->tanggal_pembayaran, 'd F Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur {{ $nomorInvoice }}</title>
    <style>
        @page { margin: 34px 40px; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111827;
            line-height: 1.5;
        }
        .header { width: 100%; border-bottom: 3px solid #0F47A1; padding-bottom: 16px; }
        .header td { vertical-align: top; }
        .brand { font-size: 11px; font-weight: bold; letter-spacing: 2px; color: #0F47A1; text-transform: uppercase; }
        .brand-title { font-size: 25px; font-weight: bold; color: #111827; margin-top: 3px; }
        .invoice-title { font-size: 30px; font-weight: bold; color: #0F47A1; text-align: right; letter-spacing: 1px; }
        .invoice-number { font-size: 12px; color: #4B5563; text-align: right; margin-top: 3px; }
        .status {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 10px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .status-lunas { background: #D1FAE5; color: #047857; }
        .status-belum { background: #FFEDD5; color: #C2410C; }
        .section { margin-top: 20px; }
        .label { font-size: 9px; font-weight: bold; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.7px; }
        .value { font-size: 11px; font-weight: bold; color: #111827; margin-top: 2px; }
        .muted { font-size: 10px; color: #6B7280; }
        .info-table { width: 100%; }
        .info-table td { vertical-align: top; }
        .box {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 12px 14px;
            background: #F9FAFB;
        }
        .items { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items th {
            background: #0F47A1;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 9px 12px;
            text-align: left;
        }
        .items th.right, .items td.right { text-align: right; }
        .items td {
            border-bottom: 1px solid #E5E7EB;
            padding: 12px;
            font-size: 11px;
        }
        .items .desc-title { font-weight: bold; color: #111827; }
        .items .desc-sub { font-size: 9px; color: #6B7280; margin-top: 2px; }
        .totals { width: 100%; margin-top: 16px; }
        .totals td { padding: 5px 0; font-size: 11px; }
        .totals .total-label { color: #6B7280; }
        .totals .total-value { text-align: right; font-weight: bold; color: #111827; }
        .totals .grand-label { border-top: 1px solid #E5E7EB; padding-top: 10px; font-size: 12px; font-weight: bold; color: #111827; }
        .totals .grand-value { border-top: 1px solid #E5E7EB; padding-top: 10px; text-align: right; font-size: 16px; font-weight: bold; color: #EA580C; }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #E5E7EB;
            padding-top: 12px;
            font-size: 9px;
            color: #9CA3AF;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 60%;">
                <div class="brand">Sales Ledger</div>
                <div class="brand-title">Faktur Tagihan</div>
            </td>
            <td style="width: 40%;">
                <div class="invoice-title">FAKTUR</div>
                <div class="invoice-number">{{ $nomorInvoice }}</div>
                <div style="text-align: right;">
                    <span class="status {{ $isLunas ? 'status-lunas' : 'status-belum' }}">
                        {{ $isLunas ? 'LUNAS' : 'BELUM LUNAS' }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table section">
        <tr>
            <td style="width: 55%; padding-right: 18px;">
                <div class="box">
                    <div class="label">Ditagihkan Kepada</div>
                    <div class="value" style="font-size: 14px; margin-top: 5px;">{{ $faktur->toko->nama_toko ?? '-' }}</div>
                    <div class="muted" style="margin-top: 4px;">{{ $faktur->toko->alamat ?: '-' }}</div>
                    <div class="muted">
                        {{ collect([$faktur->toko->kota ?? null, $faktur->toko->provinsi ?? null, $faktur->toko->kode_pos ?? null])->filter()->join(', ') ?: '-' }}
                    </div>
                    <div class="muted" style="margin-top: 3px;">Telp: {{ $faktur->toko->no_telp ?: '-' }}</div>
                </div>
            </td>
            <td style="width: 45%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding-bottom: 9px;">
                            <div class="label">Tanggal Nota</div>
                            <div class="value">{{ $tanggalNota }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 9px;">
                            <div class="label">Metode Bayar</div>
                            <div class="value">{{ $faktur->metode_bayar ?: '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 9px;">
                            <div class="label">Sales</div>
                            <div class="value">{{ $faktur->user->full_name ?? '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="label">Tanggal Setor</div>
                            <div class="value">{{ $tanggalSetor }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 48%;">Deskripsi</th>
                <th class="right" style="width: 17%;">Jumlah</th>
                <th class="right" style="width: 17%;">Harga</th>
                <th class="right" style="width: 18%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="desc-title">Tagihan Faktur</div>
                    <div class="desc-sub">No. {{ $nomorInvoice }}</div>
                    @if($faktur->catatan)
                        <div class="desc-sub">Catatan: {{ $faktur->catatan }}</div>
                    @endif
                </td>
                <td class="right">1</td>
                <td class="right">{{ Format::rupiah($faktur->total_tagihan) }}</td>
                <td class="right" style="font-weight: bold;">{{ Format::rupiah($faktur->total_tagihan) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="total-label" style="width: 70%;">Total Tagihan</td>
            <td class="total-value">{{ Format::rupiah($faktur->total_tagihan) }}</td>
        </tr>
        <tr>
            <td class="total-label">Sudah Dibayar</td>
            <td class="total-value" style="color: #047857;">{{ Format::rupiah($faktur->total_dibayar) }}</td>
        </tr>
        <tr>
            <td class="grand-label">Sisa Tagihan</td>
            <td class="grand-value">{{ Format::rupiah($sisaTagihan) }}</td>
        </tr>
    </table>

    <div class="footer">
        Faktur ini dibuat otomatis oleh Sales Ledger pada {{ now()->translatedFormat('d F Y, H:i') }}.
    </div>
</body>
</html>

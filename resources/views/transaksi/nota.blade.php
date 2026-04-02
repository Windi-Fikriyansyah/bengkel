<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Service #{{ $transaksi->id }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 16px; line-height: 24px; position: relative; }
        .box { width: 100%; display: flex; justify-content: space-between; align-items: flex-start; }
        .title h1 { margin: 0; color: #222; text-transform: uppercase; font-size: 28px; }
        .title p { margin: 5px 0 0; font-size: 14px; color: #666; }
        .info { text-align: right; }
        .info h2 { margin: 0; font-size: 20px; color: #444; }
        .info p { margin: 5px 0 0; font-size: 14px; color: #888; }
        .customer-info { margin-top: 40px; display: flex; justify-content: space-between; border-top: 2px solid #eee; padding-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        table th { background-color: #f9f9f9; text-align: left; padding: 12px; border-bottom: 2px solid #ddd; font-size: 14px; }
        table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .total-section { margin-top: 30px; float: right; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .total-label { font-weight: bold; }
        .grand-total { border-top: 2px solid #333; margin-top: 10px; padding-top: 10px; font-size: 18px; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; clear: both; }
        .footer p { font-size: 13px; color: #777; }
        .stamp { position: absolute; top: 180px; right: 50px; transform: rotate(-15deg); border: 4px double; padding: 10px 20px; font-weight: bold; font-size: 32px; opacity: 0.6; }
        .stamp-lunas { color: #28a745; border-color: #28a745; }
        .stamp-pending { color: #dc3545; border-color: #dc3545; }
        @media print { .no-print { display: none; } .invoice-box { border: none; box-shadow: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="background: #333; padding: 15px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 25px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">CETAK NOTA</button>
        <button onclick="window.close()" style="padding: 10px 25px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 10px;">TUTUP</button>
    </div>

    <div class="invoice-box">
        <div class="box">
            <div class="title">
                <h1>{{ Auth::user()->nama_bengkel ?? 'BENGKEL SERVICE' }}</h1>
                <p>{{ Auth::user()->alamat_bengkel ?? 'Alamat Bengkel' }}</p>
                <p>WA: {{ Auth::user()->phone ?? '-' }}</p>
            </div>
            <div class="info">
                <h2>NOTA SERVICE</h2>
                <p>No: #{{ $transaksi->id }}</p>
                <p>{{ date('d F Y H:i', strtotime($transaksi->created_at)) }}</p>
            </div>
        </div>

        <div class="stamp {{ $transaksi->status == 'lunas' ? 'stamp-lunas' : 'stamp-pending' }}">
            {{ $transaksi->status == 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
        </div>

        <div class="customer-info">
            <div>
                <p><strong>PELANGGAN:</strong></p>
                <p>{{ $transaksi->nama_pelanggan }}</p>
                <p>{{ $transaksi->no_wa }}</p>
            </div>
            <div style="text-align: right;">
                <p><strong>KENDARAAN:</strong></p>
                <p>{{ $transaksi->tipe_kendaraan ?? '-' }}</p>
                <p>{{ $transaksi->no_polisi ?? '-' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ITEM LAYANAN / SPAREPART</th>
                    <th style="text-align: right;">HARGA</th>
                    <th style="text-align: center;">QTY</th>
                    <th style="text-align: right;">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $item)
                <tr>
                    <td>{{ $item->nama_item }} <br> <small style="color:#999">{{ ucfirst($item->tipe) }}</small></td>
                    <td style="text-align: right;">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $item->jumlah }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row grand-total">
                <span class="total-label">TOTAL</span>
                <span class="total-value">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            @if($transaksi->status == 'lunas')
            <div class="total-row">
                <span class="total-label">Dibayar</span>
                <span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Kembalian</span>
                <span>Rp {{ number_format($transaksi->kembali, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="footer">
            <div>
                <p><strong>Catatan:</strong> {{ $transaksi->catatan ?? '-' }}</p>
                <p>Terima kasih telah melakukan service di tempat kami!</p>
            </div>
            <div style="text-align: center; width: 200px;">
                <p>Hormat Kami,</p>
                <br><br>
                <p>( {{ Auth::user()->name }} )</p>
            </div>
        </div>
    </div>
</body>
</html>

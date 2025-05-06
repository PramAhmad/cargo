<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Kiriman - {{ $shipping->invoice }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 24pt;
        }
        .section-title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin: 30px 0;
        }
        .marking-info {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .marking-code {
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 5px;
        }
        .marking-details {
            display: flex;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        .marking-item {
            width: 33.33%;
            display: flex;
            margin-bottom: 10px;
        }
        .marking-label {
            width: 100px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .summary-table {
            width: 40%;
            margin-left: auto;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #ddd;
            padding: 5px 0;
        }
        .summary-label {
            font-weight: bold;
        }
        .notes {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 20px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Faktur Kiriman</h1>
        </div>
        
        <div class="marking-info">
            <div class="marking-code">KODE MARKING</div>
            <div>({{ $shipping->marking }})</div>
            
            <div class="marking-details">
                <div class="marking-item">
                    <div class="marking-label">CONTAINER</div>
                    <div>: {{ strtoupper($shipping->shipping_type) ?? 'LCL' }} / {{ $shipping->supplier ?? 'SEA' }}</div>
                </div>
                <div class="marking-item">
                    <div class="marking-label">MARKETING</div>
                    <div>: {{ $shipping->marketing ? $shipping->marketing->name : '-' }}</div>
                </div>
                <div class="marking-item">
                    <div class="marking-label">ETD</div>
                    <div>: {{ $shipping->stuffing_date ? $shipping->stuffing_date->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="marking-item">
                    <div class="marking-label">ETA</div>
                    <div>: {{ $shipping->receipt_date ? $shipping->receipt_date->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="marking-item">
                    <div class="marking-label">SUPPLIER</div>
                    <div>: {{ $shipping->supplier ?? '-' }}</div>
                </div>
                <div class="marking-item">
                    <div class="marking-label">BUYER</div>
                    <div>: {{ $shipping->customer ? $shipping->customer->name : '-' }}</div>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>NAME</th>
                    <th>CTN NO</th>
                    <th>QTY/CTN</th>
                    <th>CTNS</th>
                    <th>QTY</th>
                    <th>LENGTH</th>
                    <th>WIDTH</th>
                    <th>HIGH</th>
                    <th>VOLUME</th>
                    <th>GW/CTN</th>
                    <th>TOTAL GW</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalCtns = 0;
                    $totalQty = 0;
                    $totalVolume = 0;
                    $totalGw = 0;
                @endphp
                
                @forelse($shipping->shippingDetails as $detail)
                <tr>
                    <td>{{ $detail->product->name }}</td>
                    <td class="text-center">{{ $detail->ctn ?? '1-' . ($detail->ctns ?? 1) }}</td>
                    <td class="text-center">{{ $detail->qty_per_ctn ?? 1 }}</td>
                    <td class="text-center">{{ $detail->ctns ?? 1 }}</td>
                    <td class="text-center">{{ $detail->qty ?? ($detail->qty_per_ctn * $detail->ctns) ?? 1 }}</td>
                    <td class="text-center">{{ number_format($detail->length ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($detail->width ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($detail->high ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($detail->volume ?? 0, 3) }}</td>
                    <td class="text-center">{{ number_format($detail->gw_per_ctn ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format($detail->total_gw ?? 0, 3) }}</td>
                </tr>
                @php
                    $totalCtns += $detail->ctns ?? 1;
                    $totalQty += $detail->qty ?? ($detail->qty_per_ctn * $detail->ctns) ?? 1;
                    $totalVolume += $detail->volume ?? 0;
                    $totalGw += $detail->total_gw ?? 0;
                @endphp
                @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data detail</td>
                </tr>
                @endforelse
                
                <tr>
                    <td colspan="2" class="text-right"><strong>TOTAL :</strong></td>
                    <td></td>
                    <td class="text-center"><strong>{{ $totalCtns }}</strong></td>
                    <td class="text-center"><strong>{{ $totalQty }}</strong></td>
                    <td colspan="3"></td>
                    <td class="text-center"><strong>{{ number_format($totalVolume, 3) }}</strong></td>
                    <td></td>
                    <td class="text-center"><strong>{{ number_format($totalGw, 3) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <table class="summary-table">
            <tr>
                <td>CTNS</td>
                <td>{{ $totalCtns }}</td>
            </tr>
            <tr>
                <td>VOLUME</td>
                <td>{{ number_format($totalVolume, 3) }}</td>
            </tr>
            <tr>
                <td>GW</td>
                <td>{{ number_format($totalGw, 3) }}</td>
            </tr>
            <tr>
                <td>ONGKOS KIRIM</td>
                <td>{{ number_format($shipping->biaya_kirim ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td><strong>{{ number_format($shipping->grand_total ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
        
        <div class="notes">
            <div class="notes-title">NOTE:</div>
            <p>Tidak ada claim setelah barang di terima, karena sebelum loading sudah konfirmasi dengan customer.</p>
            <p>Jika saat pengiriman barang ke lokasi customer terjadi kecelakaan, hal tersebut diluar tanggung jawab kami.</p>
        </div>
    </div>
</body>
</html>
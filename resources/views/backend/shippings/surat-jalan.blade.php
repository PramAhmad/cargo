<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $shipping->invoice }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }
        .container {
            width: 100%;
            max-width: 750px;
            margin: 0 auto;
            padding: 10px;
        }
        .company-info {
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .company-name {
            font-weight: bold;
            font-size: 12pt;
        }
        .document-info {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            font-size: 8pt;
        }
        .customer-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .document-details {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .info-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        .info-row {
            margin-bottom: 2px;
        }
        .info-label {
            display: inline-block;
            width: 70px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .section-title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 8px 0;
            font-size: 10pt;
        }
        .location-date {
            text-align: right;
            margin: 10px 0;
            font-size: 8pt;
        }
        /* Signature styling untuk horizontal layout */
        .signatures {
            margin-top: 30px;
        }
        .signature-row {
            margin-bottom: 20px;
        }
        .signature {
            text-align: center;
            display: inline-block;
            width: 22%;
            margin-right: 3%;
            vertical-align: top;
        }
        .signature:last-child {
            margin-right: 0;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            width: 100%;
            display: inline-block;
            margin-top: 25px;
        }
        .notes {
            margin-top: 15px;
            font-size: 7pt;
            font-style: italic;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="company-info">
            <div class="company-name">{{ config('app.name', 'CARGO INTERNATIONAL') }}</div>
            <p>{{ config('app.address', 'Perum Puri Jambul 2 Jl. Puri Ayu B23 no 4-5') }}</p>
            <p>{{ config('app.city', 'Pepelegi-Sidoarjo (61253)') }}</p>
            <p>Phone: {{ config('app.phone', '031 - 99038652') }}</p>
        </div>
        
        <div class="document-info">
            <div class="customer-info">
                <div class="info-title">Kepada</div>
                <div>: {{ $shipping->customer->name }}</div>
                
                <div class="info-row">
                    <span class="info-label">Phone 1</span>
                    <span>: {{ $shipping->customer->phone1 ?? '-' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Phone 2</span>
                    <span>: {{ $shipping->customer->phone2 ?? '-' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Perusahaan</span>
                    <span>: {{ $shipping->customer->company_name ?? '-' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Kantor/Toko</span>
                    <span>: {{ $shipping->customer->office_name ?? '-' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Alamat</span>
                    <span>: {{ $shipping->customer->address ?? '-' }}</span>
                </div>
            </div>
            
            <div class="document-details">
                <div class="info-row">
                    <span class="info-label">No SRJL</span>
                    <span>: {{ $shipping->invoice }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Tgl Kirim</span>
                    <span>: {{ $shipping->transaction_date ? $shipping->transaction_date->format('d / m / Y') : '-' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Kendaraan</span>
                    <span>: {{ $shipping->transportation ?? 'PickUp' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Plat Nomor</span>
                    <span>: {{ $shipping->vehicle_number ?? '-' }}</span>
                </div>
            </div>
        </div>
        
        <div class="section-title">SURAT JALAN</div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">QTY</th>
                    <th width="50%">MARKING CODE</th>
                    <th width="15%">Satuan</th>
                    <th width="20%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="text-center">{{ $shipping->ctns_total ?? 0 }}</td>
                    <td>{{ $shipping->marking }}</td>
                    <td class="text-center">Karton</td>
                    <td>{{ $shipping->description ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="location-date">
            {{ $shipping->warehouse ? $shipping->warehouse->city : 'Sidoarjo' }}, {{ now()->format('d F Y') }}
        </div>
        
        <!-- Signatures styled horizontally -->
        <div class="signatures">
            <div class="signature">
                Admin
                <div class="signature-line"></div>
                <div>(..........................)</div>
            </div>
            
            <div class="signature">
                Gudang
                <div class="signature-line"></div>
                <div>(..........................)</div>
            </div>
            
            <div class="signature">
                Sopir
                <div class="signature-line"></div>
                <div>(..........................)</div>
            </div>
            
            <div class="signature">
                Di terima oleh
                <div class="signature-line"></div>
                <div>(..........................)</div>
            </div>
        </div>
        
        <div class="notes">
            Note : Jika pengiriman barang dalam & luar kota ada terjadi kecelakaan di luar tanggung jawab kami.
        </div>
    </div>
</body>
</html>
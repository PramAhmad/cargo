<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Detail Pengiriman" page="Pengiriman" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <!-- Shipping Header with Actions -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-500/10 text-primary-500">
                    <i class="h-7 w-7" data-feather="truck"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-100">
                        {{ $shipping->invoice }}
                   
                    </h2>
                    <div class="mt-1 flex items-center gap-3">
                        <span class="badge badge-{{ $shipping->status->getColor() }}">
                            {{ $shipping->status->getLabel() }}
                        </span>
                        @if($shipping->transaction_date)
                        <span class="text-sm text-slate-500 dark:text-slate-400">
                            <i class="h-4 w-4 inline" data-feather="calendar"></i>
                            {{ $shipping->transaction_date->format('d M Y') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="h-4 w-4 mr-1" data-feather="printer"></i>
                        <span>Cetak</span>
                    </button>
                    <div class="dropdown-content w-40">
                        <ul class="dropdown-list">
                            <li class="dropdown-list-item">
                                <a class="dropdown-link" href="{{ route('shippings.surat-jalan', $shipping->id) }}" target="_blank">
                                    <i class="h-4 w-4 mr-1" data-feather="file-text"></i>
                                    <span>Surat Jalan</span>
                                </a>
                            </li>
                            <li class="dropdown-list-item">
                                <a class="dropdown-link" href="{{ route('shippings.faktur', $shipping->id) }}" target="_blank">
                                    <i class="h-4 w-4 mr-1" data-feather="file"></i>
                                    <span>Faktur</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <a href="{{ route('shippings.edit', $shipping->id) }}" class="btn btn-secondary">
                    <i class="h-4 w-4 mr-1" data-feather="edit-2"></i>
                    <span>Edit</span>
                </a>
                <form action="{{ route('shippings.destroy', $shipping->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="deleteShippingBtn">
                        <i class="h-4 w-4 mr-1" data-feather="trash-2"></i>
                        <span>Hapus</span>
                    </button>
                </form>
                <a href="{{ route('shippings.index') }}" class="btn btn-outline-secondary">
                    <i class="h-4 w-4 mr-1" data-feather="arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column - Main Info -->
            <div class="md:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="info"></i>
                            <h4 class="card-title">Informasi Pengiriman</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">No. Invoice</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">{{ $shipping->invoice }}</p>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Customer</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $shipping->customer->name ?? 'Belum ditentukan' }}
                                </p>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Marketing</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $shipping->marketing->name ?? 'Belum ditentukan' }}
                                </p>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Mitra</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $shipping->mitra->name ?? 'Belum ditentukan' }}
                                </p>
                            </div>

                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Gudang</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $shipping->warehouse->name ?? 'Belum ditentukan' }}
                                </p>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Marking</h5>
                                <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                    {{ $shipping->marking ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Dates Section -->
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-300 mb-4">Tanggal</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Tanggal Transaksi</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->transaction_date ? $shipping->transaction_date->format('d M Y') : '-' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Tanggal Terima</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->receipt_date ? $shipping->receipt_date->format('d M Y') : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Tanggal Stuffing</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->stuffing_date ? $shipping->stuffing_date->format('d M Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-300 mb-4">Informasi Pembayaran</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Jatuh Tempo</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->due_date ? $shipping->due_date->format('d M Y') : '-' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">TOP</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->top ? $shipping->top . ' hari' : 'Cash' }}
                                    </p>
                                </div>

                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Metode Pembayaran</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        @if($shipping->payment_type)
                                            {{ $shipping->payment_type instanceof \App\Enums\PaymentType ? $shipping->payment_type->name : $shipping->payment_type }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-300 mb-4">Informasi Tambahan</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Layanan</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->service ?? '-' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Supplier</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->supplier ?? '-' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Keterangan</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->description ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Files Section -->
                        @if($shipping->invoice_file || $shipping->packagelist_file)
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <h5 class="text-base font-medium text-slate-700 dark:text-slate-300 mb-4">Dokumen</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($shipping->invoice_file)
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">File Invoice</h5>
                                    <div class="flex items-center">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-700">
                                            <i class="h-5 w-5 text-primary-500" data-feather="file-text"></i>
                                        </div>
                                        <div class="ml-3 truncate">
                                            <p class="text-sm text-slate-600 dark:text-slate-400 truncate">
                                                {{ $shipping->invoice_file }}
                                            </p>
                                            <a href="{{ asset('shipping/invoices/' . $shipping->invoice_file) }}" target="_blank" class="text-sm text-primary-500 hover:text-primary-600">
                                                <i class="h-4 w-4 inline" data-feather="external-link"></i> Lihat Dokumen
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($shipping->packagelist_file)
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">File Package List</h5>
                                    <div class="flex items-center">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-700">
                                            <i class="h-5 w-5 text-primary-500" data-feather="file"></i>
                                        </div>
                                        <div class="ml-3 truncate">
                                            <p class="text-sm text-slate-600 dark:text-slate-400 truncate">
                                                {{ $shipping->packagelist_file }}
                                            </p>
                                            <a href="{{ asset('shipping/packagelists/' . $shipping->packagelist_file) }}" target="_blank" class="text-sm text-primary-500 hover:text-primary-600">
                                                <i class="h-4 w-4 inline" data-feather="external-link"></i> Lihat Dokumen
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="mr-2 h-5 w-5 text-primary-500" data-feather="package"></i>
                                <h4 class="card-title">Detail Produk</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        @if($shipping->shippingDetails && $shipping->shippingDetails->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">#</th>
                                            <th class="whitespace-nowrap">Produk</th>
                                            <th class="whitespace-nowrap text-center">No. Karton</th>
                                            <th class="whitespace-nowrap text-center">Qty/CTN</th>
                                            <th class="whitespace-nowrap text-center">Jml Karton</th>
                                            <th class="whitespace-nowrap text-center">Total Qty</th>
                                            <th class="whitespace-nowrap text-center">Dimensi (cm)</th>
                                            <th class="whitespace-nowrap text-center">Volume (CBM)</th>
                                            <th class="whitespace-nowrap text-center">GW/CTN (kg)</th>
                                            <th class="whitespace-nowrap text-center">Total GW (kg)</th>
                                            <th class="whitespace-nowrap text-center">Gambar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php 
                                            $totalCtns = 0;
                                            $totalQty = 0;
                                            $totalCbm = 0;
                                            $totalWeight = 0;
                                        @endphp
                                        
                                        @foreach($shipping->shippingDetails as $index => $detail)
                                            @php 
                                                $totalCtns += $detail->ctns;
                                                $totalQty += $detail->qty;
                                                $totalCbm += $detail->volume;
                                                $totalWeight += $detail->total_gw;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if($detail->product)
                                                        {{ $detail->product->name ?? 'Unknown Product' }}
                                                    @else
                                                        {{ $detail->name ?? ($detail->description ?? 'Produk Tidak Diketahui') }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $detail->ctn }}</td>
                                                <td class="text-center">{{ number_format($detail->qty_per_ctn, 0) }}</td>
                                                <td class="text-center">{{ number_format($detail->ctns, 0) }}</td>
                                                <td class="text-center">{{ number_format($detail->qty, 0) }}</td>
                                                <td class="text-center whitespace-nowrap">
                                                    {{ number_format($detail->length, 1) }} × {{ number_format($detail->width, 1) }} × {{ number_format($detail->high, 1) }}
                                                </td>,
                                                <td class="text-center">{{ number_format($detail->volume, 3) }}</td>
                                                <td class="text-center">{{ number_format($detail->gw_per_ctn, 2) }}</td>
                                                <td class="text-center">{{ number_format($detail->total_gw, 2) }}</td>
                                                <td class="text-center">
                                                    @if($detail->product_image)
                                                        <a href="{{ asset('shipping/products/' . $detail->product_image) }}" target="_blank" class="text-primary-500 hover:text-primary-600">
                                                            <i class="h-5 w-5" data-feather="image"></i>
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        
                                        <tr class="bg-slate-100 dark:bg-slate-700 font-medium">
                                            <td colspan="4" class="text-right">TOTAL:</td>
                                            <td class="text-center">{{ number_format($totalCtns, 0) }}</td>
                                            <td class="text-center">{{ number_format($totalQty, 0) }}</td>
                                            <td></td>
                                            <td class="text-center">{{ number_format($totalCbm, 3) }}</td>
                                            <td></td>
                                            <td class="text-center">{{ number_format($totalWeight, 2) }}</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <div class="mb-3 rounded-full bg-slate-100 p-3 dark:bg-slate-700">
                                    <i class="h-6 w-6 text-slate-500 dark:text-slate-400" data-feather="package"></i>
                                </div>
                                <h5 class="mb-1 text-base font-medium text-slate-700 dark:text-slate-300">Tidak Ada Produk</h5>
                                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-4">
                                    Pengiriman ini belum memiliki detail produk.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Shipping Log Activity -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="activity"></i>
                            <h4 class="card-title">Riwayat Aktivitas</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        @if($shipping->logs && $shipping->logs->count() > 0)
                            <div class="relative pl-8 before:absolute before:left-4 before:top-0 before:h-full before:border-l-2 before:border-slate-200 dark:before:border-slate-700">
                                @foreach($shipping->logs->sortByDesc('created_at') as $log)
                                    <div class="relative mb-6 last:mb-0">
                                        <div class="absolute -left-8 top-1 flex h-8 w-8 items-center justify-center rounded-full bg-primary-500/10 border-4 border-white dark:border-slate-900">
                                            <i class="h-4 w-4 text-primary-500" data-feather="check-circle"></i>
                                        </div>
                                        <div class="pl-6">
                                            <time class="mb-1 text-xs font-medium text-slate-400 dark:text-slate-500">
                                                {{ $log->created_at->format('d M Y, H:i') }} ({{ $log->created_at->diffForHumans() }})
                                            </time>
                                            <div class="flex items-center">
                                                <h5 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                                    {{ $log->user->name ?? 'System' }}
                                                </h5>
                                                <span class="ml-2 badge badge-{{ $log->status === 'waiting' ? 'warning' : ($log->status === 'done' ? 'success' : 'info') }}">
                                                    @if(in_array($log->status, \App\Enums\ShippingStatus::cases()))
                                                        {{ \App\Enums\ShippingStatus::from($log->status)->getLabel() }}
                                                    @else
                                                        {{ \App\Enums\ShippingStatus::from($log->status)->getLabel() }}
                                                    @endif
                                                </span>
                                            </div>
                                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                                {{ $log->notes }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="inline-block rounded-full bg-slate-100 p-3 dark:bg-slate-700 mb-2">
                                    <i class="h-6 w-6 text-slate-400" data-feather="clock"></i>
                                </div>
                                <p class="text-slate-500 dark:text-slate-400">Belum ada aktivitas yang tercatat</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Financial & Summary -->
            <div class="space-y-6">
                <!-- Shipping Status Card -->
                <div class="card bg-gradient-to-br from-primary-50 to-primary-100 dark:from-slate-800 dark:to-slate-700">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-lg text-primary-700 dark:text-primary-400">
                                Status Pengiriman
                            </h4>
                            <div class="badge badge-{{ $shipping->status->getColor() }}">
                                {{ $shipping->status->getLabel() }}
                            </div>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <!-- Status Update Form -->
                            <form action="{{ route('shippings.updateStatus', $shipping->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="status" class="form-label">Perbarui Status</label>
                                    <select name="status" id="status" class="select">
                                        @foreach(\App\Enums\ShippingStatus::cases() as $status)
                                            <option value="{{ $status->value }}" {{ $shipping->status->value === $status->value ? 'selected' : '' }}>
                                                {{ $status->getLabel() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="notes" class="form-label">Catatan</label>
                                    <textarea name="notes" id="notes" rows="2" class="input" placeholder="Tambahkan catatan tentang perubahan status"></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-full">
                                    <i class="h-4 w-4 mr-1" data-feather="refresh-cw"></i>
                                    Perbarui Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Summary Info -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="clipboard"></i>
                            <h4 class="card-title">Ringkasan Pengiriman</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Karton</h5>
                                    <p class="mt-1 text-xl font-bold text-primary-600 dark:text-primary-500">
                                        {{ number_format($shipping->ctns_total, 0) }}
                                    </p>
                                </div>
                                
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Berat</h5>
                                    <p class="mt-1 text-xl font-bold text-primary-600 dark:text-primary-500">
                                        {{ number_format($shipping->gw_total, 2) }} kg
                                    </p>
                                </div>
                                
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Volume</h5>
                                    <p class="mt-1 text-xl font-bold text-primary-600 dark:text-primary-500">
                                        {{ number_format($shipping->cbm_total, 3) }} CBM
                                    </p>
                                </div>
                                
                                <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Metode Kalkulasi</h5>
                                    <p class="mt-1 text-xl font-bold text-primary-600 dark:text-primary-500">
                                        {{ strtoupper($shipping->calculation_method ?? 'N/A') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                                <table class="w-full">
                                    <tr>
                                        <td class="py-2 text-sm text-slate-500 dark:text-slate-400">
                                            Harga per KG:
                                        </td>
                                        <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                            Rp {{ number_format($shipping->kg_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm text-slate-500 dark:text-slate-400">
                                            Total Harga (KG):
                                        </td>
                                        <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                            Rp {{ number_format($shipping->total_price_gw, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm text-slate-500 dark:text-slate-400">
                                            Harga per CBM:
                                        </td>
                                        <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                            Rp {{ number_format($shipping->cbm_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 text-sm text-slate-500 dark:text-slate-400">
                                            Total Harga (CBM):
                                        </td>
                                        <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                            Rp {{ number_format($shipping->total_price_cbm, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr class="border-t border-slate-200 dark:border-slate-700">
                                        <td class="py-2 text-base font-medium text-slate-700 dark:text-slate-300">
                                            Biaya Pengiriman:
                                        </td>
                                        <td class="py-2 text-base text-right font-bold text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($shipping->biaya_kirim, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Information -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="dollar-sign"></i>
                            <h4 class="card-title">Rincian Keuangan</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="overflow-y-auto max-h-[400px] pr-2">
                            <table class="w-full">
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Nilai:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->nilai, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Sup Agent:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->sup_agent, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Cukai:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->cukai, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">PPNBM:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->ppnbm, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Freight:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->freight, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">DO:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->do, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">PFPD:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->pfpd, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Charge:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->charge, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">JKT-SDA:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->jkt_sda, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">SDA-User:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->sda_user, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">BKR:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->bkr, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Asuransi:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->asuransi, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-t border-slate-200 dark:border-slate-700">
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Total Biaya:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->biaya, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Nilai Biaya:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->nilai_biaya, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">PPh:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->pph, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">PPN ({{ $shipping->ppn }}%):</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->ppn_total, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-sm text-slate-500 dark:text-slate-400">Biaya Pengiriman:</td>
                                    <td class="py-2 text-sm text-right font-medium text-slate-900 dark:text-slate-200">
                                        Rp {{ number_format($shipping->biaya_kirim, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-t border-slate-200 dark:border-slate-700">
                                    <td class="py-3 text-base font-medium text-slate-700 dark:text-slate-300">Grand Total:</td>
                                    <td class="py-3 text-base text-right font-bold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($shipping->grand_total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        @if($shipping->bank)
                        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">Bank Pembayaran</h5>
                            <div class="bg-slate-50 dark:bg-slate-800 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                            <i class="h-5 w-5 text-primary-500" data-feather="credit-card"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <h6 class="text-slate-700 dark:text-slate-200 font-medium">{{ $shipping->bank->name }}</h6>
                                        <p class="text-slate-500 dark:text-slate-400 text-sm">
                                            {{ $shipping->rek_no ?? '-' }} {{ $shipping->rek_name ? '- '.$shipping->rek_name : '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Customer Contact -->
                <div class="card">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="user"></i>
                            <h4 class="card-title">Kontak Customer</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        @if($shipping->customer)
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="user"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Nama</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->customer->name }}
                                    </p>
                                </div>
                            </li>
                            
                            @if($shipping->customer->company_name)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="briefcase"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Perusahaan</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->customer->company_name }}
                                    </p>
                                </div>
                            </li>
                            @endif
                            
                            @if($shipping->customer->phone1)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="phone"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Telepon</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->customer->phone1 }}
                                        @if($shipping->customer->phone2)
                                            <span class="text-sm text-slate-500">(Alt: {{ $shipping->customer->phone2 }})</span>
                                        @endif
                                    </p>
                                </div>
                            </li>
                            @endif
                            
                            @if($shipping->customer->email)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="mail"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Email</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->customer->email }}
                                    </p>
                                </div>
                            </li>
                            @endif
                            
                            @if($shipping->customer->address)
                            <li class="flex items-start">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50 dark:bg-slate-800">
                                    <i class="h-5 w-5 text-primary-500" data-feather="map-pin"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Alamat</h5>
                                    <p class="mt-1 text-base font-medium text-slate-900 dark:text-slate-200">
                                        {{ $shipping->customer->address }}
                                        @if($shipping->customer->city)
                                            <span class="block text-sm text-slate-500">{{ $shipping->customer->city }}</span>
                                        @endif
                                    </p>
                                </div>
                            </li>
                            @endif
                        </ul>
                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('customers.show', $shipping->customer->id) }}" class="btn btn-sm btn-outline-primary w-full">
                                <i class="h-4 w-4 mr-1" data-feather="eye"></i>
                                Lihat Detail Customer
                            </a>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <div class="inline-block rounded-full bg-slate-100 p-3 dark:bg-slate-700 mb-2">
                                <i class="h-6 w-6 text-slate-400" data-feather="user-x"></i>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400">Informasi customer tidak tersedia</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Kode Resi Section -->
                <div class="card mt-4">
                    <div class="card-header border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center">
                            <i class="mr-2 h-5 w-5 text-primary-500" data-feather="hash"></i>
                            <h4 class="card-title">Kode Resi Pengiriman</h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                @if($shipping->kode_resi)
                                    <div class="flex flex-col">
                                        <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Kode Resi</h5>
                                        <div class="flex items-center mt-1">
                                            <p class="text-lg font-bold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 px-3 py-2 rounded-md">
                                                {{ $shipping->kode_resi }}
                                            </p>
                                            <button 
                                                class="ml-2 text-slate-500 hover:text-primary-500 focus:outline-none p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                                                onclick="copyToClipboard('{{ $shipping->kode_resi }}')"
                                                title="Copy to clipboard">
                                                <i class="h-4 w-4" data-feather="copy"></i>
                                            </button>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                            Dibuat pada: {{ $shipping->updated_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                @else
                                    <div class="flex flex-col">
                                        <h5 class="text-sm font-medium text-slate-500 dark:text-slate-400">Kode Resi</h5>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                            Belum dibuat
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- QR Code & Actions -->
                            <div class="flex flex-col md:flex-row items-center gap-4">
                                @if($shipping->qr_resi)
                                    <div class="bg-white dark:bg-slate-700 p-3 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600">
                                        <img src="{{ asset('shipping/qrcodes/' . $shipping->qr_resi) }}" 
                                             alt="QR Code {{ $shipping->kode_resi }}" 
                                             class="h-28 w-28 object-contain" />
                                    </div>
                                    {{-- <div class="flex flex-col gap-2">
                                        <a href="{{ route('shippings.downloadQr', $shipping->id) }}" class="btn btn-sm btn-secondary">
                                            <i class="h-4 w-4 mr-1" data-feather="download"></i>
                                            Download QR Code
                                        </a>
                                       
                                        <a href="{{ route('shippings.regenerateResi', $shipping->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="h-4 w-4 mr-1" data-feather="refresh-cw"></i>
                                            Regenerate
                                        </a>
                                    </div> --}}
                                @elseif($shipping->kode_resi)
                                    <div class="flex flex-col gap-2">
                                        <span class="text-sm text-slate-500 dark:text-slate-400">QR Code belum dibuat</span>
                                        <a href="{{ route('shippings.generateResi', $shipping->id) }}" class="btn btn-sm btn-primary">
                                            <i class="h-4 w-4 mr-1" data-feather="qr-code"></i>
                                            Generate QR Code
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('shippings.generateResi', $shipping->id) }}" class="btn btn-primary">
                                        <i class="h-4 w-4 mr-1" data-feather="hash"></i>
                                        Generate Kode Resi
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status update form handling
        const statusForm = document.querySelector('form[action*="updateStatus"]');
        if (statusForm) {
            statusForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Perbarui Status?',
                    text: 'Apakah Anda yakin ingin memperbarui status pengiriman ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, perbarui',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        }
        
        // Delete shipping confirmation
        const deleteButton = document.getElementById('deleteShippingBtn');
        if (deleteButton) {
            deleteButton.closest('form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Hapus Pengiriman?',
                    html: 'Apakah Anda yakin ingin menghapus pengiriman ini? <br>Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        }
        
        // Reinitialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
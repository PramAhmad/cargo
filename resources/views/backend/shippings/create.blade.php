<x-app-layout>
    <!-- Page Title Starts -->
    <x-page-title header="Create Shipping" page="Shipping" />
    <!-- Page Title Ends -->

    <div class="space-y-6">
        <form action="{{ route('shippings.store') }}" method="POST" enctype="multipart/form-data" id="shippingForm">
            @csrf
            
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="grid grid-cols-4 gap-6">
                <!-- Left Column: Main Information -->
                <div class="col-span-4">
                    <div class="card">
                        <div class="card-header bg-primary-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                            <h5 class="card-title flex items-center">
                                <i class="fas fa-truck-loading mr-2 text-primary-500"></i>
                                Informasi Kirim Barang
                            </h5>
                        </div>
                        <div class="card-body p-6">
                            <div class="grid grid-cols-2 gap-5">
                                <!-- Row 1 -->
                                <div class="col-span-1">
                                    <label for="invoice" class="label mb-1 text-sm font-medium">No Invoice</label>
                                    <input type="text" id="invoice" name="invoice" class="input" value="{{ $defaultInvoice }}" readonly>
                                </div>
                                
                                <div class="col-span-1">
                                    <label for="status" class="label label-required mb-1 text-sm font-medium">Status</label>
                                    <select id="status" name="status" class="select" required>
                                        @foreach(\App\Enums\ShippingStatus::cases() as $status)
                                            <option value="{{ $status->value }}" {{ $status->value == \App\Enums\ShippingStatus::waiting->value ? 'selected' : '' }}>
                                                {{ $status->getLabel() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Row 2 -->
                                <div class="col-span-1">
                                    <label for="customer_id" class="label label-required mb-1 text-sm font-medium">Customer</label>
                                    <select id="customer_id" name="customer_id" class="tom-select" required>
                                        <option value="">Pilih Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-marketing-id="{{ $customer->marketing_id }}">
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-span-1">
                                    <label for="marketing_id" class="label label-required mb-1 text-sm font-medium">Marketing</label>
                                    <select id="marketing_id" name="marketing_id" class="select" required disabled>
                                        <option value="">Pilih Marketing</option>
                                        @foreach($marketings as $marketing)
                                            <option value="{{ $marketing->id }}" data-code="{{ $marketing->code ?? '' }}">{{ $marketing->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Row 3 -->
                                <div class="col-span-1">
                                    <label for="transaction_date" class="label label-required mb-1 text-sm font-medium">Tanggal Transaksi</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="input" value="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <div class="col-span-1">
                                    <label for="receipt_date" class="label mb-1 text-sm font-medium">Tanggal Terima Barang</label>
                                    <input type="date" id="receipt_date" name="receipt_date" class="input" max="{{ date('Y-m-d') }}">
                                </div>
                                
                                <!-- Row 4 -->
                                <div class="col-span-1">
                                    <label for="payment_type" class="label label-required mb-1 text-sm font-medium">Pembayaran</label>
                                    <select id="payment_type" name="payment_type" class="select" required>
                                        @foreach(\App\Enums\PaymentType::cases() as $paymentType)
                                            <option value="{{ $paymentType->value }}" {{ $paymentType->value == \App\Enums\PaymentType::Transfer->value ? 'selected' : '' }}>
                                                {{ $paymentType->getLabel() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-span-1">
                                    <label for="stuffing_date" class="label mb-1 text-sm font-medium">Tanggal Stuffing</label>
                                    <input type="date" id="stuffing_date" name="stuffing_date" class="input" max="{{ date('Y-m-d') }}">
                                </div>
                                
                                <!-- Row 5 -->
                                <div class="col-span-1">
                                    <label for="top" class="label mb-1 text-sm font-medium">Term of Payment (Hari)</label>
                                    <input type="number" id="top" name="top" class="input" min="0">
                                </div>
                                
                                <div class="col-span-1">
                                    <label for="due_date" class="label mb-1 text-sm font-medium">Tanggal Jatuh Tempo</label>
                                    <input type="date" id="due_date" name="due_date" class="input">
                                </div>
                                
                                <!-- Row 6 -->
                                <div class="col-span-1">
                                    <label for="service" class="label mb-1 text-sm font-medium">Layanan</label>
                                    <select id="service" name="service" class="select">
                                        <option value="">Pilih Layanan</option>
                                        <option value="SEA">SEA</option>
                                        <option value="AIR">AIR</option>
                                    </select>
                                </div>
                                
                                <div class="col-span-1">
                                    <label for="bank_id" class="label mb-1 text-sm font-medium">Bank</label>
                                    <select id="bank_id" name="bank_id" class="select">
                                        <option value="">Pilih Bank</option>
                                    </select>
                                </div>
                                
                                <!-- Row 7 -->
                                <div class="col-span-2">
                                    <label for="description" class="label mb-1 text-sm font-medium">Keterangan</label>
                                    <textarea id="description" name="description" class="textarea" rows="2" placeholder="Keterangan pengiriman"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Detail & Biaya -->
              
            </div>
            
            <!-- Barang Section -->
            <div class="card mt-5">
                <div class="card-header bg-primary-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                    <h5 class="card-title flex items-center">
                        <i class="fas fa-boxes mr-2 text-primary-500"></i>
                        Input Barang
                    </h5>
                </div>
                <div class="card-body p-6">
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-6">
                            <label for="mitra_id" class="label label-required mb-1 text-sm font-medium">Mitra</label>
                            <select id="mitra_id" name="mitra_id" class="tom-select" required>
                                <option value="">Pilih Mitra</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" data-marking-code="{{ $mitra->code ?? '' }}">
                                        {{ $mitra->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Warehouse -->
                        <div class="col-span-6">
                            <label for="warehouse_id" class="label label-required mb-1 text-sm font-medium">Gudang</label>
                            <select id="warehouse_id" name="warehouse_id" class="select" required disabled>
                                <option value="">Pilih Gudang</option>
                            </select>
                        </div>
                        
                        <!-- Kode Marking -->
                        <div class="col-span-4">
                            <label for="marking" class="label mb-1 text-sm font-medium">Kode Marking</label>
                            <div class="relative">
                                <input type="text" id="marking" name="marking" class="input">
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 text-sm">
                                    <i class="fas fa-tag text-gray-400"></i>
                                </div>
                            </div>
                            <p class="text-xs text-amber-600 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Format: [MitraCode]/[MarketingCode]/[SEA atau AIR]
                            </p>
                        </div>
                        
                        <!-- Jenis Kiriman -->
                        <div class="col-span-4">
                            <label for="shipping_type" class="label label-required mb-1 text-sm font-medium">Jenis Kiriman</label>
                            <select id="shipping_type" name="shipping_type" class="select" required>
                                @foreach(\App\Enums\ShippingType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ $type->value == \App\Enums\ShippingType::LCL->value ? 'selected' : '' }}>
                                        {{ $type->getLabel() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Supplier -->
                        <div class="col-span-4">
                            <label for="supplier" class="label label-required mb-1 text-sm font-medium">Supplier</label>
                            <input type="text" id="supplier" name="supplier" class="input" required>
                        </div>
                        
                        <!-- Invoice File -->
                        <div class="col-span-6">
                            <label for="invoice_file" class="label mb-1 text-sm font-medium">Invoice File</label>
                            <input type="file" id="invoice_file" name="invoice_file" class="input" accept="application/pdf,image/*">
                        </div>
                        
                        <!-- Package List File -->
                        <div class="col-span-6">
                            <label for="packagelist_file" class="label label-required mb-1 text-sm font-medium">Package List File</label>
                            <input type="file" id="packagelist_file" name="packagelist_file" class="input" accept="application/pdf,image/*" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Warehouse Products -->
            <div id="warehouseProductsSection" class="card mt-4 hidden">
                <div class="card-header bg-blue-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-600">
                    <div class="flex justify-between items-center">
                        <h5 class="card-title flex items-center">
                            <i class="fas fa-cubes mr-2 text-blue-500"></i>
                            Barang Tersedia di Gudang
                        </h5>
                        <div class="w-1/3 relative">
                            <input type="text" id="product_search" class="input input-sm pl-8" placeholder="Cari barang...">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="overflow-x-auto">
                        <table class="table-default">
                            <thead>
                                <tr class="bg-blue-100 dark:bg-slate-700">
                                    <th>Nama Barang</th>
                                    <th class="text-center">Harga/KG</th>
                                    <th class="text-center">Harga/CBM</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="warehouseProductsList">
                                <!-- Warehouse products will be loaded here -->
                            </tbody>
                        </table>
                        <!-- Products Pagination -->
                        <div class="mt-4 flex justify-between items-center">
                            <div id="product-pagination-info" class="text-sm text-gray-500">
                                Menampilkan 1-10 dari 0 barang
                            </div>
                            <div id="product-pagination" class="flex space-x-2">
                                <!-- Pagination will be rendered here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Barang Cards -->
            <div class="card mt-5">
                <div class="card-header bg-primary-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                    <h5 class="card-title flex items-center">
                        <i class="fas fa-shopping-cart mr-2 text-primary-500"></i>
                        Daftar Barang Transaksi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="overflow-x-auto">
                        <table class="table-default" id="productTransactionTable">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-slate-700">
                                    <th class="text-center" width="80px">Gambar</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center" width="80px">CTN/NO</th>
                                    <th class="text-center" width="80px">Qty/CTN</th>
                                    <th class="text-center" width="80px">Total CTN</th>
                                    <th class="text-center" width="80px">Total Qty</th>
                                    <th class="text-center" width="60px">Panjang (cm)</th>
                                    <th class="text-center" width="60px">Lebar (cm)</th>
                                    <th class="text-center" width="60px">Tinggi (cm)</th>
                                    <th class="text-center" width="100px">GW/CTN (kg)</th>
                                    <th class="text-center" width="100px">Volume (m³)</th>
                                    <th class="text-center" width="100px">Total GW (kg)</th>
                                    <th class="text-center" width="100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="barangList">
                                <!-- Product items will be added here -->
                            </tbody>
                        </table>
                            
                        <!-- Empty state message -->
                        <div id="emptyProductState" class="text-center py-8">
                            <div class="text-gray-400 dark:text-gray-500">
                                <i class="fas fa-box-open text-4xl mb-2"></i>
                                <p>Belum ada barang yang ditambahkan</p>
                                <p class="text-sm">Pilih barang dari daftar di atas untuk menambahkan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Cost Calculation Card - Hapus tombol "Terapkan Biaya Kirim" -->
<div class="card mt-4" id="shippingCostCalculationCard">
    <div class="card-header bg-green-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
        <h5 class="card-title flex items-center">
            <i class="fas fa-calculator mr-2 text-green-500"></i>
            Kalkulasi Ongkir
        </h5>
    </div>
    <div class="card-body p-4">
        <!-- Calculation methods -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
            <!-- Volume-based Calculation -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800/30 p-4">
                <div class="flex items-center mb-2">
                    <h6 class="text-sm font-medium flex items-center">
                        <i class="fas fa-cube mr-2 text-blue-500"></i>
                        Perhitungan Berdasarkan Volume
                    </h6>
                </div>
                
                <div class="grid grid-cols-12 gap-2 mb-3">
                    <div class="col-span-4">
                        <div class="text-xs text-gray-500 mb-1">Total Volume (m³)</div>
                        <div class="font-medium" id="total_volume_display">0,00</div>
                    </div>
                    <div class="col-span-4">
                        <div class="text-xs text-gray-500 mb-1">Harga per CBM</div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                            <input type="number" id="harga_ongkir_cbm" name="harga_ongkir_cbm" class="input input-sm h-7 pl-6 text-right" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-span-4">
                        <div class="text-xs text-gray-500 mb-1">Biaya Volume</div>
                        <div class="font-medium text-blue-600" id="volume_cost_display">Rp 0,00</div>
                    </div>
                </div>
                
                <div class="text-xs text-blue-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Rumus: Total Volume (m³) × Harga per CBM
                </div>
            </div>
            
            <!-- Weight-based Calculation -->
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-800/30 p-4">
                <div class="flex items-center mb-2">
                    <h6 class="text-sm font-medium flex items-center">
                        <i class="fas fa-weight mr-2 text-green-500"></i>
                        Perhitungan Berdasarkan Berat
                    </h6>
                </div>
                
                <div class="grid grid-cols-12 gap-2 mb-3">
                    <div class="col-span-4">
                        <div class="text-xs text-gray-500 mb-1">Total Berat (kg)</div>
                        <div class="font-medium" id="total_weight_display">0,00</div>
                    </div>
                    <div class="col-span-4">
                        <div class="text-xs text-gray-500 mb-1">Harga per KG</div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                            <input type="number" id="harga_ongkir_wg" name="harga_ongkir_wg" class="input input-sm h-7 pl-6 text-right" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-span-4">
                        <div class="text-xs text-gray-500 mb-1">Biaya Berat</div>
                        <div class="font-medium text-green-600" id="weight_cost_display">Rp 0,00</div>
                    </div>
                </div>
                
                <div class="text-xs text-green-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Rumus: Total Berat (kg) × Harga per KG
                </div>
            </div>
        </div>
        
        <!-- Selected calculation method info -->
        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-800/30">
            <div class="flex items-start">
                <div class="mr-3 text-blue-500">
                    <i class="fas fa-calculator text-lg"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-blue-800 dark:text-blue-400 mb-1">Metode Perhitungan yang Digunakan</div>
                    <div class="text-xs text-blue-700 dark:text-blue-500" id="used_calculation_message">
                        Menunggu kalkulasi...
                    </div>
                    <div class="text-xs text-blue-600 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Regulasi perhitungan:</strong> Jika total berat ≤ batas maksimum, menggunakan metode Volume. Jika total berat > batas maksimum, menggunakan metode Berat.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total shipping cost - Tanpa tombol terapkan, hanya tampilan -->
        <div class="mt-3 p-3 bg-gray-50 dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="selected_shipping_cost" class="text-sm font-medium">Total Biaya Kirim (Otomatis tersimpan)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                        <input type="text" id="selected_shipping_cost" name="selected_shipping_cost" class="input pl-10 text-right font-bold bg-gray-100 dark:bg-gray-800" readonly>
                        <input type="hidden" id="calculation_method_used" name="calculation_method_used" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            
            <!-- Informasi max weight -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg border border-yellow-100 dark:border-yellow-800/30 mt-3 mb-3">
                <div class="flex items-start">
                    <div class="mr-3 text-yellow-500">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-yellow-800 dark:text-yellow-400 mb-1">Batas Maksimum Berat</div>
                        <div class="text-xs text-yellow-700 dark:text-yellow-500" id="max_weight_info">
                            Batas maksimum berat: <span id="max_weight_display">0,00</span> kg
                            <input type="hidden" id="max_weight" name="max_weight" value="0">
                        </div>
                    </div>
                </div>
            </div>
          
<div class="grid grid-cols-3 gap-4 py-5">
    <!-- Left: Tax Information -->
    <div class="col-span-2">
        <div class="card h-full">
            <div class="card-header bg-primary-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                <h5 class="card-title flex items-center">
                    <i class="fas fa-file-invoice-dollar mr-2 text-primary-500"></i>
                    Informasi Pajak & Total
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <label for="pph" class="label mb-1 text-sm font-medium">PPH Pasal 22</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                            <input type="text" id="pph" name="pph" class="input pl-10 text-right money-mask" data-calculate-total="true">
                        </div>
                    </div>
                    
                    <!-- PPN -->
                    <div class="col-span-1">
                        <label for="ppn" class="label mb-1 text-sm font-medium">PPN</label>
                        <div class="relative">
                            <input type="text" id="ppn" name="ppn" class="input pr-8 text-right" value="{{ $ppn }}" data-calculate-total="true">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">%</span>
                        </div>
                    </div>
                    
                    <!-- PPN Total -->
                    <div class="col-span-1">
                        <label for="ppn_total" class="label mb-1 text-sm font-medium">PPN (Total)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                            <input type="text" id="ppn_total" name="ppn_total" class="input pl-10 text-right bg-gray-50 dark:bg-gray-800 money-mask" readonly data-calculate-total="true">
                        </div>
                    </div>
                    
                    <!-- Biaya Kirim -->
                    <div class="col-span-1">
                        <label for="biaya_kirim" class="label mb-1 text-sm font-medium">Biaya Kirim</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                            <input type="text" id="biaya_kirim" name="biaya_kirim" class="input pl-10 text-right bg-gray-50 dark:bg-gray-800 money-mask" readonly data-calculate-total="true">
                        </div>
                    </div>
                    
                    <!-- Nomor Faktur Pajak -->
                    <div class="col-span-2">
                        <label for="pajak" class="label mb-1 text-sm font-medium">Nomor Faktur Pajak</label>
                        <input type="text" id="pajak" name="pajak" class="input" placeholder="Input manual">
                    </div>
                    
                    <!-- Grand Total -->
                    <div class="col-span-3 mt-4">
                        <label for="grand_total" class="label mb-1 font-semibold">Grand Total</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                            <input type="text" id="grand_total" name="grand_total" class="input pl-10 text-right text-lg font-bold bg-blue-50 dark:bg-slate-700 money-mask" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right: Details and Cost -->
    <div class="col-span-1">
        <div class="card h-full">
            <div class="card-header py-3 bg-primary-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                <h5 class="card-title text-sm flex items-center">
                    <i class="fas fa-calculator mr-2 text-primary-500"></i>
                    Detail Total & Biaya
                </h5>
            </div>
            <div class="card-body p-3">
                <!-- Total Shipping Summary -->
                <div class="bg-gray-50 dark:bg-gray-800 p-2 rounded-lg border border-gray-200 dark:border-gray-700 mb-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="col-span-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                            Carton
                        </div>
                        <div class="col-span-1 text-right">
                            <input type="text" id="carton_display" class="input input-sm h-7 w-full text-right text-xs bg-gray-50 dark:bg-gray-800" value="0,00" readonly>
                        </div>

                        <div class="col-span-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                            Gross Weight
                        </div>
                        <div class="col-span-1 text-right">
                            <input type="text" id="gw_display" class="input input-sm h-7 w-full text-right text-xs bg-gray-50 dark:bg-gray-800" value="0,00" readonly>
                        </div>

                        <div class="col-span-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                            Volume
                        </div>
                        <div class="col-span-1 text-right">
                            <input type="text" id="volume_display" class="input input-sm h-7 w-full text-right text-xs bg-gray-50 dark:bg-gray-800" value="0,00" readonly>
                        </div>

                        <div class="col-span-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                            CBM
                        </div>
                        <div class="col-span-1 text-right">
                            <input type="text" id="cbm_display" class="input input-sm h-7 w-full text-right text-xs bg-gray-50 dark:bg-gray-800" value="0,00" readonly>
                        </div>
                    </div>
                </div>
                
                <!-- Fee Components Section with Ringkasan Biaya Integrated -->
                <div class="bg-blue-50 dark:bg-slate-800 rounded-md p-2">
                    <div class="flex justify-between items-center mb-2">
                        <h6 class="text-xs font-medium text-blue-600 dark:text-blue-400">Komponen Biaya</h6>
                        <div class="text-xs text-gray-500 italic">
                            Nilai Barang: <span id="nilai_simple" class="font-medium">Rp 0,00</span>
                        </div>
                    </div>
                    
                    <!-- Individual fee components - Compact Grid -->
                    <div class="space-y-1">
                        <div class="grid grid-cols-2 gap-x-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Sup-Agent</span>
                                <input type="text" id="sup_agent" name="sup_agent" class="input input-sm h-7 pl-16 text-right text-xs money-mask" data-fee="true">
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Cukai</span>
                                <input type="text" id="cukai" name="cukai" class="input input-sm h-7 pl-10 text-right text-xs money-mask" data-fee="true">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-x-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Tbh</span>
                                <input type="text" id="tbh" name="tbh" class="input input-sm h-7 pl-8 text-right text-xs money-mask" data-fee="true">
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">PPnBM</span>
                                <input type="text" id="ppnbm" name="ppnbm" class="input input-sm h-7 pl-10 text-right text-xs money-mask" data-fee="true">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-x-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Freight</span>
                                <input type="text" id="freight" name="freight" class="input input-sm h-7 pl-11 text-right text-xs money-mask" data-fee="true">
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">DO</span>
                                <input type="text" id="do" name="do" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-x-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">PFPD</span>
                                <input type="text" id="pfpd" name="pfpd" class="input input-sm h-7 pl-9 text-right text-xs money-mask" data-fee="true">
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Charge</span>
                                <input type="text" id="charge" name="charge" class="input input-sm h-7 pl-12 text-right text-xs money-mask" data-fee="true">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-x-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Jkt-Sda</span>
                                <input type="text" id="jkt_sda" name="jkt_sda" class="input input-sm h-7 pl-12 text-right text-xs money-mask" data-fee="true">
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Sda-User</span>
                                <input type="text" id="sda_user" name="sda_user" class="input input-sm h-7 pl-14 text-right text-xs money-mask" data-fee="true">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-x-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Jasa Bkr</span>
                                <input type="text" id="bkr" name="bkr" class="input input-sm h-7 pl-12 text-right text-xs money-mask" data-fee="true">
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Asuransi</span>
                                <input type="text" id="asuransi" name="asuransi" class="input input-sm h-7 pl-12 text-right text-xs money-mask" data-fee="true">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Biaya footer -->
                    <div class="mt-2 pt-2 border-t border-blue-200 dark:border-slate-700">
                        <div class="grid grid-cols-2 gap-1 items-center">
                            <div class="text-xs font-medium text-blue-800 dark:text-blue-400">Total Murni Biaya</div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-blue-500 text-xs">Rp</span>
                                <input type="text" id="biaya" name="biaya" class="input input-sm h-7 pl-6 text-right text-xs bg-blue-100 dark:bg-slate-800 font-medium money-mask" readonly>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-1 items-center mt-1">
                            <div class="text-xs font-medium text-blue-800 dark:text-blue-400">Nilai + Biaya</div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-blue-500 text-xs">Rp</span>
                                <input type="text" id="nilai_biaya" name="nilai_biaya" class="input input-sm h-7 pl-6 text-right text-xs bg-blue-100 dark:bg-slate-800 money-mask" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="rek_no" name="rek_no" value="">
                <input type="hidden" id="rek_name" name="rek_name" value="">
                
                <input type="hidden" id="nilai" name="nilai" value="0">
                <input type="hidden" id="selected_shipping_cost" name="selected_shipping_cost" value="0">
                <input type="hidden" id="calculation_method_used" name="calculation_method_used" value="">
            </div>
        </div>
    </div>
</div>


            <div class="flex justify-end mt-6">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
        </div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Table Styles */
    .table-default {
        width: 100%;
        text-align: left;
    }
    
    .table-default th {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .table-default td {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        white-space: nowrap;
        border-top: 1px solid;
        border-color: rgb(229 231 235 / 1);
    }

    /* Modal Styles */
    .modal {
        position: fixed;
        inset: 0;
        z-index: 50;
        overflow-y: auto;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-dialog {
        width: 100%;
        margin-left: 1rem;
        margin-right: 1rem;
        max-width: 48rem;
    }
    
    .modal-content {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        overflow: hidden;
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid;
        border-color: rgb(229 231 235 / 1);
    }
    
    .modal-title {
        font-size: 1.125rem;
        font-weight: 500;
        color: rgb(17 24 39 / 1);
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        padding: 1rem;
        border-top: 1px solid;
        border-color: rgb(229 231 235 / 1);
    }
    
    .close-modal {
        color: rgb(107 114 128 / 1);
    }
    .close-modal:hover {
        color: rgb(55 65 81 / 1);
    }

    /* Fix for dark mode if needed */
    .dark .table-default td {
        border-color: rgb(55 65 81 / 1);
    }
    
    .dark .modal-content {
        background-color: rgb(30 41 59 / 1);
    }
    
    .dark .modal-title {
        color: rgb(255 255 255 / 1);
    }
    
    .dark .modal-header,
    .dark .modal-footer {
        border-color: rgb(55 65 81 / 1);
    }
    
    .dark .close-modal {
        color: rgb(156 163 175 / 1);
    }
    .dark .close-modal:hover {
        color: rgb(209 213 219 / 1);
    }

    /* Grid classes for advanced layouts */
    .grid-cols-40 {
        grid-template-columns: repeat(40, minmax(0, 1fr));
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
<script src="{{ asset('js/shipping-form.js') }}"></script>

@endpush
</x-app-layout>



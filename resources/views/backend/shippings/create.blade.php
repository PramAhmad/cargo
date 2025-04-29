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
            
            <div class="grid  grid-cols-4 gap-6">
                <!-- Left Column: Shipping Information -->
                <div class="col-span-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Informasi Kirim Barang</h5>
                        </div>
                        <div class="card-body p-6">
                            <div class="grid grid-cols-2 gap-6">
                                <!-- No Invoice -->
                                <div class="col-span-1">
                                    <label for="invoice" class="label mb-1">No Invoice</label>
                                    <input type="text" id="invoice" name="invoice" class="input" value="{{ $defaultInvoice }}" readonly>
                                </div>
                                
                                <!-- Status -->
                                <div class="col-span-1">
                                    <label for="status" class="label label-required mb-1">Pilih Status</label>
                                    <select id="status" name="status" class="select" required>
                                        @foreach(\App\Enums\ShippingStatus::cases() as $status)
                                            <option value="{{ $status->value }}" {{ $status->value == \App\Enums\ShippingStatus::waiting->value ? 'selected' : '' }}>
                                                {{ $status->getLabel() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Customer -->
                                <div class="col-span-1">
                                    <label for="customer_id" class="label label-required mb-1">Customer</label>
                                    <select id="customer_id" name="customer_id" class="tom-select"  required>
                                        <option value="">Pilih Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-marketing-id="{{ $customer->marketing_id }}">
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Marketing -->
                                <div class="col-span-1">
                                    <label for="marketing_id" class="label label-required mb-1">Marketing</label>
                                    <select id="marketing_id" name="marketing_id" class="select" required disabled>
                                        <option value="">Pilih Marketing</option>
                                        @foreach($marketings as $marketing)
                                            <option value="{{ $marketing->id }}">{{ $marketing->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Transaction Date -->
                                <div class="col-span-1">
                                    <label for="transaction_date" class="label label-required mb-1">Tanggal Transaksi</label>
                                    <input type="date" id="transaction_date" name="transaction_date" class="input" value="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <!-- Receipt Date -->
                                <div class="col-span-1">
                                    <label for="receipt_date" class="label mb-1">Tanggal Terima Barang</label>
                                    <input type="date" id="receipt_date" name="receipt_date" class="input" max="{{ date('Y-m-d') }}">
                                </div>
                                
                                <!-- Payment Type -->
                                <div class="col-span-1">
                                    <label for="payment_type" class="label label-required mb-1">Pembayaran</label>
                                    <select id="payment_type" name="payment_type" class="select" required>
                                        @foreach(\App\Enums\PaymentType::cases() as $paymentType)
                                            <option value="{{ $paymentType->value }}" {{ $paymentType->value == \App\Enums\PaymentType::Transfer->value ? 'selected' : '' }}>
                                                {{ $paymentType->getLabel() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Stuffing Date -->
                                <div class="col-span-1">
                                    <label for="stuffing_date" class="label mb-1">Tanggal Stuffing</label>
                                    <input type="date" id="stuffing_date" name="stuffing_date" class="input" max="{{ date('Y-m-d') }}">
                                </div>
                                
                                <!-- TOP -->
                                <div class="col-span-1">
                                    <label for="top" class="label mb-1">Term of Payment (TOP) (Hari)</label>
                                    <input type="number" id="top" name="top" class="input" min="0">
                                </div>
                                
                                <!-- Due Date -->
                                <div class="col-span-1">
                                    <label for="due_date" class="label mb-1">Tanggal Jatuh Tempo</label>
                                    <input type="date" id="due_date" name="due_date" class="input">
                                </div>
                                
                                <!-- Description -->
                                <div class="col-span-1">
                                    <label for="description" class="label mb-1">Keterangan</label>
                                    <textarea id="description" name="description" class="textarea" rows="3" placeholder="Keterangan"></textarea>
                                </div>
                                <div class="col-span-1">
                                    <label for="service" class="label mb-1">Layanan</label>
                                    <select id="service" name="service" class="select">
                                        <option value="">Pilih Layanan</option>
                                        <option value="SEA">SEA</option>
                                        <option value="AIR">AIR</option>
                                    </select>
                                </div>
                                <!-- Bank -->
                                <div class="col-span-1">
                                    <label for="bank_id" class="label mb-1">Bank</label>
                                    <select id="bank_id" name="bank_id" class="select">
                                        <option value="">Pilih Bank</option>
                                     
                                    </select>
                                </div>
                            
                   
                               
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Detail & Biaya -->
                <div class="col-span-1">
                    <div class="card">
                        <div class="card-header py-2">
                            <h5 class="card-title text-sm">Detail Total Barang & Biaya</h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="grid grid-cols-1 gap-2">
                                <div class="grid grid-cols-2 gap-2 mb-2">
                                    <div class="col-span-1 text-center font-medium text-gray-600 dark:text-gray-400 text-xs">
                                        Carton
                                    </div>
                                    <div class="col-span-1">
                                        <input type="text" id="carton_display" class="input input-sm h-7 text-right text-xs" value="0,00" readonly>
                                    </div>

                                    <div class="col-span-1 text-center font-medium text-gray-600 dark:text-gray-400 text-xs">
                                        Gross Weight
                                    </div>
                                    <div class="col-span-1">
                                        <input type="text" id="gw_display" class="input input-sm h-7 text-right text-xs" value="0,00" readonly>
                                    </div>

                                    <div class="col-span-1 text-center font-medium text-gray-600 dark:text-gray-400 text-xs">
                                        Volume
                                    </div>
                                    <div class="col-span-1">
                                        <input type="text" id="volume_display" class="input input-sm h-7 text-right text-xs" value="0,00" readonly>
                                    </div>

                                    <div class="col-span-1 text-center font-medium text-gray-600 dark:text-gray-400 text-xs">
                                        CBM
                                    </div>
                                    <div class="col-span-1">
                                        <input type="text" id="cbm_display" class="input input-sm h-7 text-right text-xs" value="0,00" readonly>
                                    </div>
                                </div>

                                <hr class="border-gray-200 dark:border-gray-700 my-1">
                                
                                <!-- Sup Agent -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="sup_agent" class="label mb-0 text-xs col-span-2">Sup-Agent</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="sup_agent" name="sup_agent" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- Cukai -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="cukai" class="label mb-0 text-xs col-span-2">Cukai</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="cukai" name="cukai" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- Tbh Bayar -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="tbh" class="label mb-0 text-xs col-span-2">Tbh Bayar</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="tbh" name="tbh" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- PPnBM -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="ppnbm" class="label mb-0 text-xs col-span-2">PPnBM</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="ppnbm" name="ppnbm" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- Freight -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="freight" class="label mb-0 text-xs col-span-2">Freight</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="freight" name="freight" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- DO -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="do" class="label mb-0 text-xs col-span-2">DO</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="do" name="do" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- PFPD -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="pfpd" class="label mb-0 text-xs col-span-2">PFPD</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="pfpd" name="pfpd" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- Charge TT -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="charge" class="label mb-0 text-xs col-span-2">Charge TT</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="charge" name="charge" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- Jkt-Sda -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="jkt_sda" class="label mb-0 text-xs col-span-2">Jkt-Sda</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="jkt_sda" name="jkt_sda" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- Sda-User -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="sda_user" class="label mb-0 text-xs col-span-2">Sda-User</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="sda_user" name="sda_user" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- Jasa Bkr -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="bkr" class="label mb-0 text-xs col-span-2">Jasa Bkr</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="bkr" name="bkr" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <!-- Asuransi -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="asuransi" class="label mb-0 text-xs col-span-2">Asuransi</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="asuransi" name="asuransi" class="input input-sm h-7 pl-6 text-right text-xs money-mask" data-fee="true">
                                    </div>
                                </div>
                                
                                <hr class="border-gray-200 dark:border-gray-700 my-1">
                                
                                <!-- Nilai Barang -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="nilai" class="label mb-0 text-xs col-span-2">Nilai Barang</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="nilai" name="nilai" class="input input-sm h-7 pl-6 text-right text-xs money-mask" readonly>
                                    </div>
                                </div>
                                
                                <!-- Nilai Brg + Biaya -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="nilai_biaya" class="label mb-0 text-xs col-span-2">Nilai + Biaya</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="nilai_biaya" name="nilai_biaya" class="input input-sm h-7 pl-6 text-right text-xs money-mask" readonly>
                                    </div>
                                </div>
                                
                                <!-- Murni Biaya -->
                                <div class="grid grid-cols-5 gap-1 items-center">
                                    <label for="biaya" class="label mb-0 text-xs col-span-2">Murni Biaya</label>
                                    <div class="relative col-span-3">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-1 text-gray-500 text-xs">Rp</span>
                                        <input type="text" id="biaya" name="biaya" class="input input-sm h-7 pl-6 text-right text-xs money-mask" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tax Information -->
         
            
            <!-- Barang Section -->
            <div class="card mt-5">
                <div class="card-header">
                    <h5 class="card-title">Input Barang</h5>
                </div>
                <div class="card-body p-6">
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-6">
                            <label for="mitra_id" class="label label-required mb-1">Mitra</label>
                            <select id="mitra_id" name="mitra_id" class="tom-select" required>
                                <option value="">Pilih Mitra</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}" data-marking-code="{{ $mitra->code ?? '' }}">
                                        {{ $mitra->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-amber-600 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih mitra untuk menampilkan kode marking
                            </p>
                        </div>
                        
                        <!-- Warehouse -->
                        <div class="col-span-6">
                            <label for="warehouse_id" class="label label-required mb-1">Gudang</label>
                            <select id="warehouse_id" name="warehouse_id" class="select" required disabled>
                                <option value="">Pilih Gudang</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih gudang untuk melihat barang yang tersedia
                            </p>
                        </div>
                        
                        <!-- Kode Marking -->
                        <div class="col-span-4">
                            <label for="marking" class="label mb-1">Kode Marking</label>
                            <input type="text" id="marking" name="marking" class="input" readonly>
                        </div>
                        
                        <!-- Jenis Kiriman -->
                        <div class="col-span-4">
                            <label for="shipping_type" class="label label-required mb-1">Jenis Kiriman</label>
                            <select id="shipping_type" name="shipping_type" class="select" required>
                                @foreach(\App\Enums\ShippingType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ $type->value == \App\Enums\ShippingType::LCL->value ? 'selected' : '' }}>
                                        {{ $type->getLabel() }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Nomor invoice akan otomatis terbentuk sesuai jenis kiriman
                            </p>
                        </div>
                        
                        <!-- Supplier -->
                        <div class="col-span-4">
                            <label for="supplier" class="label label-required mb-1">Supplier</label>
                            <input type="text" id="supplier" name="supplier" class="input" required>
                        </div>
                        
                        <!-- Invoice File -->
                        <div class="col-span-6">
                            <label for="invoice_file" class="label mb-1">Invoice File</label>
                            <input type="file" id="invoice_file" name="invoice_file" class="input" accept="application/pdf,image/*">
                        </div>
                        
                        <!-- Package List File -->
                        <div class="col-span-6">
                            <label for="packagelist_file" class="label label-required mb-1">Package List File</label>
                            <input type="file" id="packagelist_file" name="packagelist_file" class="input" accept="application/pdf,image/*" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Warehouse Products -->
            <div id="warehouseProductsSection" class="card mt-4 hidden">
                <div class="card-header bg-blue-50 dark:bg-slate-800">
                    <h5 class="card-title">Barang Tersedia di Gudang</h5>
                    <div class="mt-2 relative">
                        <input type="text" id="product_search" class="input" placeholder="Cari barang...">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
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
                                    <th class="text-center">Stock</th>
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

            <!-- List Barang Table -->
            <div class="card">
                <div class="card-header bg-primary-50 dark:bg-slate-700">
                    <h5 class="card-title">Transaksi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="table-default">
                            <thead>
                                <tr class="bg-red-600 text-white">
                                    <th class="text-center">NO</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">FOTO</th>
                                    <th class="text-center">CTN / NO</th>
                                    <th class="text-center">QTY / CTN</th>
                                    <th class="text-center">CTNS</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">LENGTH</th>
                                    <th class="text-center">WIDTH</th>
                                    <th class="text-center">HIGH</th>
                                    <th class="text-center">GW</th>
                                    <th class="text-center">ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="barangList">
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 py-5">
                <div class="card">
                    <div class="card-body p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <!-- PPH Pasal 22 -->
                            <div class="col-span-1">
                                <label for="pph" class="label mb-1">PPH Pasal 22</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                    <input type="text" id="pph" name="pph" class="input pl-10 text-right money-mask" data-calculate-total="true">
                                </div>
                            </div>
                            
                            <!-- Biaya Kirim -->
                            <div class="col-span-1">
                                <label for="biaya_kirim" class="label mb-1">Biaya Kirim (Total sblm pajak)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                    <input type="text" id="biaya_kirim" name="biaya_kirim" class="input pl-10 text-right money-mask" data-calculate-total="true">
                                </div>
                            </div>
                            
                            <!-- PPN -->
                            <div class="col-span-1 flex items-end">
                                <div class="w-full">
                                    <label for="ppn" class="label mb-1">PPN</label>
                                    <div class="relative">
                                        <input type="text" id="ppn" name="ppn" class="input pr-8 text-right" value="{{ $ppn }}" data-calculate-total="true">
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- PPN Total -->
                            <div class="col-span-1">
                                <label for="ppn_total" class="label mb-1">PPN (Total)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                    <input type="text" id="ppn_total" name="ppn_total" class="input pl-10 text-right money-mask" readonly data-calculate-total="true">
                                </div>
                            </div>
                            
                            <!-- Nomor Faktur Pajak -->
                            <div class="col-span-2">
                                <label for="pajak" class="label mb-1">Nomor Faktur Pajak</label>
                                <input type="text" id="pajak" name="pajak" class="input" placeholder="Input manual">
                            </div>
                            
                            <!-- Grand Total -->
                            <div class="col-span-2">
                                <label for="grand_total" class="label mb-1 font-semibold">Grand Total</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                    <input type="text" id="grand_total" name="grand_total" class="input pl-10 text-right text-lg font-bold money-mask" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Submit Button -->
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
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            width: '100%',
            theme: document.documentElement.classList.contains('dark') ? 'classic' : 'default'
        });
        
        // Initialize money inputs for formatting
        function initMoneyInputs() {
            $('.money-mask').each(function() {
                new Cleave(this, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });
            });
        }
        
        initMoneyInputs();
        
        // Helper functions for number formatting/parsing
        function formatNumber(number) {
            if (!number) return '0,00';
            return parseFloat(number).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        
        function parseNumberFromFormatted(formattedNumber) {
            if (!formattedNumber) return 0;
            return parseFloat(formattedNumber.replace(/\./g, '').replace(',', '.'));
        }
        
        function normalizeNumber(formattedNumber) {
            return parseNumberFromFormatted(formattedNumber).toString();
        }
        
        // Initialize variables
        let detailCounter = 0;
        let warehouseProducts = [];
        let currentPage = 1;
        let itemsPerPage = 10;
        let filteredProducts = [];
        
        $('#customer_id').on('change', function() {
            const customerId = $(this).val();
            const marketingId = $(this).find('option:selected').data('marketing-id');
            
            if (marketingId) {
                $('#marketing_id').val(marketingId);
            } else {
                $('#marketing_id').val('').prop('disabled', true);
            }
            
            if (customerId) {
                $.get(`/api/customers/${customerId}/banks`, function(data) {
                    const bankSelect = $('#bank_id');
                    bankSelect.empty().append('<option value="">Pilih Bank</option>');
                    
                    if (data.length > 0) {
                        data.forEach(bank => {
                            const isDefault = bank.is_default ? ' (Default)' : '';
                            bankSelect.append(`
                                <option value="${bank.id}" ${bank.is_default ? 'selected' : ''}>
                                    ${bank.bank.name} - ${bank.rek_name} - ${bank.rek_no}${isDefault}
                                </option>
                            `);
                        });
                    } else {
                        bankSelect.append('<option value="" disabled>Customer tidak memiliki rekening bank</option>');
                    }
                });
            } else {
                $('#bank_id').empty().append('<option value="">Pilih Bank</option>');
            }
        });
        
        // Handle mitra selection to load warehouses
        $('#mitra_id').on('change', function() {
            const mitraId = $(this).val();
            const markingCode = $(this).find('option:selected').data('marking-code') || '';
            
            $('#marking').val(markingCode);
            
            if (mitraId) {
                // Load warehouses for this mitra
                $.get(`/api/mitras/${mitraId}/warehouses`, function(data) {
                    const warehouseSelect = $('#warehouse_id');
                    warehouseSelect.empty().append('<option value="">Pilih Gudang</option>');
                    
                    data.forEach(warehouse => {
                        warehouseSelect.append(`<option value="${warehouse.id}">${warehouse.name} - ${warehouse.products_count} item</option>`);
                    });
                    
                    warehouseSelect.prop('disabled', false);
                });
                
                // Hide the warehouse products section when mitra changes
                $('#warehouseProductsSection').addClass('hidden');
                $('#warehouseProductsList').empty();
            } else {
                $('#warehouse_id').empty().append('<option value="">Pilih Gudang</option>').prop('disabled', true);
                $('#warehouseProductsSection').addClass('hidden');
            }
        });
        
        // Handle warehouse selection to load products and warehouse details
        $('#warehouse_id').on('change', function() {
            const warehouseId = $(this).val();
            
            // Clear the existing product list in the red table
            $('#barangList').empty();
            // Reset counters and totals
            detailCounter = 0;
            calculateTotals();
            
            if (warehouseId) {
                // Load warehouse details
                $.get(`/api/warehouses/${warehouseId}`, function(warehouse) {
                    // Create and show warehouse info box if it doesn't exist
                    if ($('#warehouse_info').length === 0) {
                        const warehouseInfoHTML = `
                            <div id="warehouse_info" class="mt-2 p-3 text-sm bg-blue-50 dark:bg-slate-700 rounded-md">
                                <h6 class="font-semibold mb-1">Informasi Gudang:</h6>
                                <div class="grid grid-cols-2 gap-2">
                                    <div><span class="font-medium">Nama:</span> ${warehouse.name}</div>
                                    <div><span class="font-medium">Tipe:</span> ${warehouse.type || 'N/A'}</div>
                                    <div class="col-span-2"><span class="font-medium">Alamat:</span> ${warehouse.address || 'N/A'}</div>
                                </div>
                            </div>
                        `;
                        
                        // Add warehouse info after the warehouse select
                        $('#warehouse_id').closest('div').append(warehouseInfoHTML);
                    } else {
                        // Update existing warehouse info
                        $('#warehouse_info').html(`
                            <h6 class="font-semibold mb-1">Informasi Gudang:</h6>
                            <div class="grid grid-cols-2 gap-2">
                                <div><span class="font-medium">Nama:</span> ${warehouse.name}</div>
                                <div><span class="font-medium">Tipe:</span> ${warehouse.type || 'N/A'}</div>
                                <div class="col-span-2"><span class="font-medium">Alamat:</span> ${warehouse.address || 'N/A'}</div>
                            </div>
                        `);
                    }
                });
                
                // Load products for this warehouse
                $.get(`/api/warehouses/${warehouseId}/products`, function(data) {
                    warehouseProducts = data;
                    filteredProducts = [...warehouseProducts];
                    currentPage = 1;
                    
                    // Show the warehouse products section
                    $('#warehouseProductsSection').removeClass('hidden');
                    
                    // Render products with pagination
                    renderProducts();
                });
            } else {
                // Remove warehouse info box if no warehouse selected
                $('#warehouse_info').remove();
                $('#warehouseProductsSection').addClass('hidden');
            }
        });
        
        // Search products
        $('#product_search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            
            if (searchTerm === '') {
                filteredProducts = [...warehouseProducts];
            } else {
                filteredProducts = warehouseProducts.filter(product => 
                    product.name.toLowerCase().includes(searchTerm)
                );
            }
            
            currentPage = 1;
            renderProducts();
        });
        
        // Render products with pagination
        function renderProducts() {
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedProducts = filteredProducts.slice(startIndex, endIndex);
            const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
            
            const productsList = $('#warehouseProductsList');
            productsList.empty();
            
            if (paginatedProducts.length === 0) {
                productsList.append(`<tr><td colspan="5" class="text-center py-4">Tidak ada barang tersedia</td></tr>`);
            } else {
                paginatedProducts.forEach(product => {
                    productsList.append(`
                        <tr data-product-id="${product.id}">
                            <td>${product.name}</td>
                            <td class="text-right">${formatNumber(product.price_kg)}</td>
                            <td class="text-right">${formatNumber(product.price_cbm)}</td>
                            <td class="text-center">${product.stock || 'N/A'}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary add-product-btn" data-product-id="${product.id}">
                                    <i class="fas fa-plus mr-1"></i> Tambah
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }
            
            // Update pagination info
            $('#product-pagination-info').text(`Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredProducts.length)} dari ${filteredProducts.length} barang`);
            
            // Render pagination
            renderPagination(totalPages);
            
            // Attach event listeners to add buttons
            $('.add-product-btn').on('click', function() {
                const productId = parseInt($(this).data('product-id'));
                const product = warehouseProducts.find(p => p.id === productId);
                if (product) {
                    addProductToTable(product);
                }
            });
        }
        
        // Render pagination controls
        function renderPagination(totalPages) {
            const pagination = $('#product-pagination');
            pagination.empty();
            
            // Previous button
            pagination.append(`
                <button class="btn btn-sm ${currentPage === 1 ? 'btn-disabled' : 'btn-secondary'}" 
                        ${currentPage === 1 ? 'disabled' : ''} data-page="prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `);
            
            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, startPage + 4);
            
            for (let i = startPage; i <= endPage; i++) {
                pagination.append(`
                    <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-secondary'}" data-page="${i}">
                        ${i}
                    </button>
                `);
            }
            
            // Next button
            pagination.append(`
                <button class="btn btn-sm ${currentPage === totalPages ? 'btn-disabled' : 'btn-secondary'}" 
                        ${currentPage === totalPages ? 'disabled' : ''} data-page="next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `);
            
            // Attach event listeners to pagination buttons
            pagination.find('button').on('click', function() {
                if ($(this).attr('disabled')) return;
                
                const page = $(this).data('page');
                if (page === 'prev') {
                    currentPage--;
                } else if (page === 'next') {
                    currentPage++;
                } else {
                    currentPage = page;
                }
                
                renderProducts();
            });
        }
        
        // Add a product to the "List Barang" table
        function addProductToTable(product) {
            const rowIndex = detailCounter++;
            const productImageUrl = product.image_url || '/images/no-image.jpg';
            
            const row = `
                <tr data-index="${rowIndex}" data-product-id="${product.id}">
                    <td class="text-center">${rowIndex + 1}</td>
                    <td>${product.name}</td>
                    <td class="text-center">
                        <img src="${productImageUrl}" alt="${product.name}" class="h-10 w-10 rounded object-cover inline-block">
                        <input type="file" name="barang[${rowIndex}][product_image]" class="hidden product-image-upload" accept="image/*">
                        <button type="button" class="btn btn-xs btn-secondary upload-image-btn">
                            <i class="fas fa-upload"></i>
                        </button>
                    </td>
                    <td class="text-center">
                        <input type="text" class="input input-sm" name="barang[${rowIndex}][ctn]" value="" placeholder="CTN">
                    </td>
                    <td class="text-center">
                        <input type="number" class="input input-sm qty-per-ctn" name="barang[${rowIndex}][qty_per_ctn]" value="1" min="1" step="1">
                    </td>
                    <td class="text-center">
                        <input type="number" class="input input-sm total-ctns" name="barang[${rowIndex}][ctns]" value="1" min="1" step="1">
                    </td>
                    <td class="text-center">
                        <input type="text" class="input input-sm total-qty" name="barang[${rowIndex}][qty]" value="1" readonly>
                    </td>
                    <td class="text-center">
                        <input type="number" class="input input-sm dimension-input" name="barang[${rowIndex}][length]" value="" min="0.01" step="0.01" placeholder="L">
                    </td>
                    <td class="text-center">
                        <input type="number" class="input input-sm dimension-input" name="barang[${rowIndex}][width]" value="" min="0.01" step="0.01" placeholder="W">
                    </td>
                    <td class="text-center">
                        <input type="number" class="input input-sm dimension-input" name="barang[${rowIndex}][high]" value="" min="0.01" step="0.01" placeholder="H">
                    </td>
                    <td class="text-center">
                        <input type="number" class="input input-sm gw-per-ctn" name="barang[${rowIndex}][gw_per_ctn]" value="" min="0.01" step="0.01" placeholder="GW/CTN">
                        <input type="hidden" class="total-gw" name="barang[${rowIndex}][total_gw]" value="0">
                        <input type="hidden" class="volume" name="barang[${rowIndex}][volume]" value="0">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-icon btn-danger delete-barang">
                            <i class="fas fa-trash"></i>
                        </button>
                        <input type="hidden" name="barang[${rowIndex}][product_id]" value="${product.id}">
                    </td>
                </tr>
            `;
            
            $('#barangList').append(row);
            
            // Attach event listeners to the new row
            attachRowEventListeners($(`#barangList tr[data-index="${rowIndex}"]`));
            
            // Calculate totals
            calculateTotals();
        }
        
        // Attach event listeners to a row
        function attachRowEventListeners(row) {
            // Quantity calculations
            row.find('.qty-per-ctn, .total-ctns').on('input', function() {
                const qtyPerCtn = parseFloat(row.find('.qty-per-ctn').val()) || 0;
                const totalCtns = parseFloat(row.find('.total-ctns').val()) || 0;
                const totalQty = qtyPerCtn * totalCtns;
                
                row.find('.total-qty').val(totalQty);
                
                // Update GW if GW per CTN is set
                const gwPerCtn = parseFloat(row.find('.gw-per-ctn').val()) || 0;
                if (gwPerCtn > 0) {
                    const totalGw = gwPerCtn * totalCtns;
                    row.find('.total-gw').val(totalGw.toFixed(2));
                }
                
                calculateTotals();
            });
            
            // Dimension calculations for volume
            row.find('.dimension-input').on('input', function() {
                const length = parseFloat(row.find('input[name$="[length]"]').val()) || 0;
                const width = parseFloat(row.find('input[name$="[width]"]').val()) || 0;
                const height = parseFloat(row.find('input[name$="[high]"]').val()) || 0;
                
                // Volume in cubic meters (convert from cm to m)
                const volumeCbm = (length * width * height) / 1000000;
                row.find('.volume').val(volumeCbm.toFixed(6));
                
                calculateTotals();
            });
            
            // GW calculations
            row.find('.gw-per-ctn').on('input', function() {
                const gwPerCtn = parseFloat($(this).val()) || 0;
                const totalCtns = parseFloat(row.find('.total-ctns').val()) || 0;
                const totalGw = gwPerCtn * totalCtns;
                
                row.find('.total-gw').val(totalGw.toFixed(2));
                calculateTotals();
            });
            
            // Image upload button click handler
            row.find('.upload-image-btn').on('click', function() {
                row.find('.product-image-upload').click();
            });
            
            // Image file selected handler
            row.find('.product-image-upload').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        row.find('img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Delete button
            row.find('.delete-barang').on('click', function() {
                row.remove();
                calculateTotals();
            });
        }
        
        // Delete barang from the list (event delegation for dynamically added elements)
        $(document).on('click', '.delete-barang', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });
        
        // Calculate total amounts
        function calculateTotals() {
            let totalCtns = 0;
            let totalQty = 0;
            let totalVolume = 0;
            let totalGw = 0;
            
            $('#barangList tr').each(function() {
                totalCtns += parseFloat($(this).find('.total-ctns').val()) || 0;
                totalQty += parseFloat($(this).find('.total-qty').val()) || 0;
                totalVolume += parseFloat($(this).find('.volume').val()) || 0;
                totalGw += parseFloat($(this).find('.total-gw').val()) || 0;
            });
            
            // Update display fields
            $('#carton_display').val(formatNumber(totalCtns));
            $('#gw_display').val(formatNumber(totalGw));
            $('#volume_display').val(formatNumber(totalVolume));
            $('#cbm_display').val(formatNumber(totalVolume)); // CBM is same as volume in m³
            
            // Update hidden inputs for form submission
            $('#ctns_total').val(totalCtns);
            $('#qty_total').val(totalQty);
            $('#cbm_total').val(totalVolume);
            $('#gw_total').val(totalGw);
            
            // Update calculation section
            $('#gw_total_calc').val(formatNumber(totalGw));
            $('#cbm_total_calc').val(formatNumber(totalVolume));
            
            // Recalculate pricing
            calculateFees();
        }
        
        // Calculate fees and biaya
        function calculateFees() {
            let totalFees = 0;
            
            $('[data-fee="true"]').each(function() {
                totalFees += parseNumberFromFormatted($(this).val()) || 0;
            });
            
            // Biaya is the total of all fees
            $('#biaya').val(formatNumber(totalFees));
            
            // Calculate nilai_biaya
            const nilaiBarang = parseNumberFromFormatted($('#nilai').val()) || 0;
            $('#nilai_biaya').val(formatNumber(nilaiBarang + totalFees));
            
            // Update biaya kirim
            $('#biaya_kirim').val(formatNumber(totalFees));
            
            // Update PPN and grand total
            calculatePPN();
            calculateGrandTotal();
        }
        
        // Calculate PPN total
        function calculatePPN() {
            const biayaKirim = parseNumberFromFormatted($('#biaya_kirim').val()) || 0;
            const ppnRate = parseFloat($('#ppn').val()) || 0;
            const ppnAmount = biayaKirim * (ppnRate / 100);
            
            $('#ppn_total').val(formatNumber(ppnAmount));
        }
        
        // Calculate grand total
        function calculateGrandTotal() {
            const biayaKirim = parseNumberFromFormatted($('#biaya_kirim').val()) || 0;
            const pph = parseNumberFromFormatted($('#pph').val()) || 0;
            const ppnTotal = parseNumberFromFormatted($('#ppn_total').val()) || 0;
            
            const grandTotal = biayaKirim + pph + ppnTotal;
            $('#grand_total').val(formatNumber(grandTotal));
        }
        
        // Fee calculation
        $('[data-fee="true"]').on('input', calculateFees);
        
        // Tax calculations
        $('#ppn, [data-calculate-total="true"]').on('input', function() {
            calculatePPN();
            calculateGrandTotal();
        });
        
        // Handle shipping type change to update invoice number
        $('#shipping_type').on('change', function() {
            const shippingType = $(this).val();
            $.get(`/api/shippings/generate-invoice?type=${shippingType}`, function(data) {
                $('#invoice').val(data.invoice);
            });
        });
        
        // Handle TOP (Terms of Payment) change to calculate due date
        $('#top, #transaction_date').on('change', function() {
            const transactionDate = $('#transaction_date').val();
            const top = parseInt($('#top').val()) || 0;
            
            if (transactionDate && top > 0) {
                const dueDate = new Date(transactionDate);
                dueDate.setDate(dueDate.getDate() + top);
                
                const year = dueDate.getFullYear();
                const month = String(dueDate.getMonth() + 1).padStart(2, '0');
                const day = String(dueDate.getDate()).padStart(2, '0');
                
                $('#due_date').val(`${year}-${month}-${day}`);
            }
        });
        
        // Initialize form
        $('#shipping_type').trigger('change'); // Generate initial invoice number
    });
</script>
@endpush
</x-app-layout>



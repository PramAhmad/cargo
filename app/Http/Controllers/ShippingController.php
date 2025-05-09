<?php

namespace App\Http\Controllers;

use App\Enums\ShippingType;
use App\Enums\ShippingStatus;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\Marketing;
use App\Models\Mitra;
use App\Models\Shipping;
use App\Models\ShippingDetail;
use App\Models\ShippingLog;
use App\Models\User;
use App\Notifications\ShippingStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShippingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Shipping::with(['customer', 'mitra'])
            ->orderBy('transaction_date', 'desc');
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                  ->orWhere('marking', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Apply date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }
        
        $shippings = $query->paginate(10);
        
        return view('backend.shippings.index', [
            'shippings' => $shippings,
            'search' => $request->search,
            'statusFilter' => $request->status,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $marketings = Marketing::orderBy('name')->get();
        $mitras = Mitra::orderBy('name')->get();
        $banks = Bank::orderBy('name')->get();
        
        $ppn = 11;
        
        $dateNow = date('dmy');
        $countOfShipping = Shipping::where('shipping_type', ShippingType::LCL->value)->count() + 1;
        $defaultInvoice = strtoupper(ShippingType::LCL->value) . '-' . $dateNow . str_pad($countOfShipping, 3, '0', STR_PAD_LEFT) . '-1';
        
        return view('backend.shippings.create', compact(
            'customers', 
            'marketings', 
            'mitras', 
            'banks', 
            'ppn',
            'defaultInvoice'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'invoice' => 'required|string|unique:shippings,invoice',
                'customer_id' => 'required|exists:customers,id',
                'mitra_id' => 'required|exists:mitras,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'transaction_date' => 'required|date',
                'barang' => 'required|array|min:1',
                'barang.*.product_image' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:2048',
                'invoice_file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'packagelist_file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // generate resi
            $resi = 'RESI-' . strtoupper(ShippingType::LCL->value) . '-' . date('dmy') . str_pad(Shipping::count() + 1, 3, '0', STR_PAD_LEFT);
            $request->merge(['kode_resi' => $resi]);
             $qrPath = 'shipping/qrcodes';
             $qrFullPath = public_path($qrPath);
             $qrFileName = 'qr_' . time() . '_' . $resi . '.png';
             if (!file_exists($qrFullPath)) {
                 mkdir($qrFullPath, 0755, true);
             }
             
             QrCode::format('png')
                  ->size(300)
                  ->errorCorrection('H')
                  ->margin(1)
                  ->generate($resi, $qrFullPath . '/' . $qrFileName);
             
            
            DB::beginTransaction();
            
            $shippingData = [
                'kode_resi' => $request->input('kode_resi'),
                'qr_resi' => $qrFileName,
                'invoice' => $request->input('invoice'),
                'customer_id' => $request->input('customer_id'),
                'marketing_id' => $request->input('marketing_id'),
                'mitra_id' => $request->input('mitra_id'),
                'warehouse_id' => $request->input('warehouse_id'),
                'status' => ShippingStatus::waiting->value,
                'transaction_date' => $request->input('transaction_date'),
                'receipt_date' => $request->input('receipt_date'),
                'stuffing_date' => $request->input('stuffing_date'),
                'due_date' => $request->input('due_date'),
                'top' => $request->input('top'),
                'payment_type' => $request->input('payment_type'),
                'service' => $request->input('service'),
                'bank_id' => $request->input('bank_id'),
                'marking' => $request->input('marking'),
                'shipping_type' => $request->input('shipping_type'),
                'supplier' => $request->input('supplier'),
                'description' => $request->input('description'),
                'pajak' => $request->input('pajak'),
                'rek_no' => $request->input('rek_no'),
                'rek_name' => $request->input('rek_name'),
                
                // Komponsen biaya
                'nilai' => $this->parseAmount($request->input('nilai')),
                'sup_agent' => $this->parseAmount($request->input('sup_agent')),
                'cukai' => $this->parseAmount($request->input('cukai')),
                'tbh' => $this->parseAmount($request->input('tbh')),
                'ppnbm' => $this->parseAmount($request->input('ppnbm')),
                'freight' => $this->parseAmount($request->input('freight')),
                'do' => $this->parseAmount($request->input('do')),
                'pfpd' => $this->parseAmount($request->input('pfpd')),
                'charge' => $this->parseAmount($request->input('charge')),
                'jkt_sda' => $this->parseAmount($request->input('jkt_sda')),
                'sda_user' => $this->parseAmount($request->input('sda_user')),
                'bkr' => $this->parseAmount($request->input('bkr')),
                'asuransi' => $this->parseAmount($request->input('asuransi')),
                
                // Biaya total
                'biaya' => $this->parseAmount($request->input('biaya')),
                'nilai_biaya' => $this->parseAmount($request->input('nilai_biaya')),
                'pph' => $this->parseAmount($request->input('pph')),
                'ppn' => $request->input('ppn'),
                'ppn_total' => $this->parseAmount($request->input('ppn_total')),
                'biaya_kirim' => $this->parseAmount($request->input('biaya_kirim')),
                'grand_total' => $this->parseAmount($request->input('grand_total')),
                
                // Informasi summary
                'ctns_total' => $this->parseAmount($request->input('summary.total_carton')),
                'gw_total' => $this->parseAmount($request->input('summary.total_weight')),
                'cbm_total' => $this->parseAmount($request->input('summary.total_volume')),
                
                // Metode kalkulasi
                'calculation_method' => $request->input('calculation_method_used'),
                'cbm_price' => $this->parseAmount($request->input('harga_ongkir_cbm')),
                'kg_price' => $this->parseAmount($request->input('harga_ongkir_wg')),
            ];
            
            // Hitung total price berdasarkan kedua metode
            $shippingData['total_price_cbm'] = $shippingData['cbm_total'] * $shippingData['cbm_price'];
            $shippingData['total_price_gw'] = $shippingData['gw_total'] * $shippingData['kg_price'];
            
            $shipping = Shipping::create($shippingData);
            
            // Simpan file invoice jika ada
            if ($request->hasFile('invoice_file')) {
                $invoiceFile = $request->file('invoice_file');
                $invoiceFileName = 'invoice_' . time() . '_' . $invoiceFile->getClientOriginalName();
                
                $uploadPath = public_path('shipping/invoices');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $invoiceFile->move($uploadPath, $invoiceFileName);
                $shipping->invoice_file = $invoiceFileName;
                $shipping->save();
            }
            
            if ($request->hasFile('packagelist_file')) {
                $packagelistFile = $request->file('packagelist_file');
                $packagelistFileName = 'packagelist_' . time() . '_' . $packagelistFile->getClientOriginalName();
                
                $uploadPath = public_path('shipping/packagelists');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                $packagelistFile->move($uploadPath, $packagelistFileName);
                $shipping->packagelist_file = $packagelistFileName;
                $shipping->save();
            }
            
            // Simpan detail barang menggunakan mass assignment
            if (isset($request->barang) && is_array($request->barang)) {
                foreach ($request->barang as $index => $item) {
                    $detailData = [
                        'shipping_id' => $shipping->id,
                        'product_id' => $item['product_id'],
                        'ctn' => $item['ctn'],
                        'qty_per_ctn' => $item['qty_per_ctn'],
                        'ctns' => $item['ctns'],
                        'qty' => $item['qty'],
                        'length' => $item['length'],
                        'width' => $item['width'],
                        'high' => $item['high'],
                        'gw_per_ctn' => $item['gw_per_ctn'],
                        'volume' => $this->parseAmount($item['volume']),
                        'total_gw' => $this->parseAmount($item['total_gw']),
                    ];
                    
                    $detail = ShippingDetail::create($detailData);
                    
                    // Simpan gambar produk jika ada
                    if (isset($request->file('barang')[$index]['product_image'])) {
                        $productImage = $request->file('barang')[$index]['product_image'];
                        $imageFileName = 'product_' . $shipping->id . '_' . $index . '_' . time() . '_' . $productImage->getClientOriginalName();

                        $uploadPath = public_path('shipping/products');
                        if (!file_exists($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }
                        $productImage->move($uploadPath, $imageFileName);
                    }
                }
            }
            
            $this->logShippingActivity($shipping->id, ShippingStatus::waiting->value, 'Data shipping dibuat');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data shipping berhasil disimpan',
                'shipping_id' => $shipping->id,
                'redirect_url' => route('shippings.show', $shipping->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error saving shipping data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

   
    /**
     * Display the specified resource.
     */
    public function show(Shipping $shipping)
    {
        $shipping->load([
            'customer', 
            'marketing', 
            'mitra', 
            'warehouse', 
            'bank', 
            'shippingDetails.product', 
            'logs.user'
        ]);
        
        return view('backend.shippings.show', compact('shipping'));
    }
    
    /**
     * Update shipping status
     */
    public function updateStatus(Request $request, Shipping $shipping)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        $oldStatus = $shipping->status->value;
        $oldStatusName = $shipping->status->name;
        $shipping->status = $validated['status'];
        $shipping->save();
        
        // Log activity
        $this->logShippingActivity(
            $shipping->id, 
            $validated['status'],
            $validated['notes'] ?? 'Status updated from ' . $oldStatusName . ' to ' . $shipping->status->name
        );
        
        // Kirim notifikasi perubahan status
        $recipients = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['superadmin', 'admin', 'marketing']);
            })
            ->get();
            
        // Tambahkan marketing terkait jika ada
        if ($shipping->marketing_id) {
            $marketing = User::find($shipping->marketing_id);
            if ($marketing && !$recipients->contains('id', $marketing->id)) {
                $recipients->push($marketing);
            }
        }
        
        // Tambahkan pembuat shipping jika ada
        $shipping->load('logs.user');
        $creatorLog = $shipping->logs->where('status', 'waiting')->first();
        if ($creatorLog && $creatorLog->user && !$recipients->contains('id', $creatorLog->user->id)) {
            $recipients->push($creatorLog->user);
        }
        
        Notification::send($recipients, new ShippingStatusNotification($shipping, $oldStatusName, $shipping->status->name));
        
        return redirect()
            ->route('shippings.show', $shipping->id)
            ->with('success', 'Shipping status has been updated successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipping $shipping)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipping $shipping)
    {
        try {
            // Validasi data
            $validator = Validator::make($request->all(), [
                'invoice' => 'required|string|unique:shippings,invoice,' . $shipping->id,
                'customer_id' => 'required|exists:customers,id',
                'mitra_id' => 'required|exists:mitras,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'transaction_date' => 'required|date',
                'barang' => 'required|array|min:1',
                'barang.*.product_image' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:2048',
                'invoice_file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
                'packagelist_file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            
            DB::beginTransaction();
            
            // Update data shipping
            $shippingData = [
                'invoice' => $request->input('invoice'),
                'customer_id' => $request->input('customer_id'),
                'marketing_id' => $request->input('marketing_id'),
                'mitra_id' => $request->input('mitra_id'),
                'warehouse_id' => $request->input('warehouse_id'),
                'transaction_date' => $request->input('transaction_date'),
                'receipt_date' => $request->input('receipt_date'),
                'stuffing_date' => $request->input('stuffing_date'),
                'due_date' => $request->input('due_date'),
                'top' => $request->input('top'),
                'payment_type' => $request->input('payment_type'),
                'service' => $request->input('service'),
                'bank_id' => $request->input('bank_id'),
                'rek_no' => $request->input('rek_no'),
                'rek_name' => $request->input('rek_name'),

                'marking' => $request->input('marking'),
                'shipping_type' => $request->input('shipping_type'),
                'supplier' => $request->input('supplier'),
                'description' => $request->input('description'),
                'pajak' => $request->input('pajak'),
                
                // Komponsen biaya
                'nilai' => $this->parseAmount($request->input('nilai')),
                'sup_agent' => $this->parseAmount($request->input('sup_agent')),
                'cukai' => $this->parseAmount($request->input('cukai')),
                'tbh' => $this->parseAmount($request->input('tbh')),
                'ppnbm' => $this->parseAmount($request->input('ppnbm')),
                'freight' => $this->parseAmount($request->input('freight')),
                'do' => $this->parseAmount($request->input('do')),
                'pfpd' => $this->parseAmount($request->input('pfpd')),
                'charge' => $this->parseAmount($request->input('charge')),
                'jkt_sda' => $this->parseAmount($request->input('jkt_sda')),
                'sda_user' => $this->parseAmount($request->input('sda_user')),
                'bkr' => $this->parseAmount($request->input('bkr')),
                'asuransi' => $this->parseAmount($request->input('asuransi')),
                
                // Biaya total
                'biaya' => $this->parseAmount($request->input('biaya')),
                'nilai_biaya' => $this->parseAmount($request->input('nilai_biaya')),
                'pph' => $this->parseAmount($request->input('pph')),
                'ppn' => $request->input('ppn'),
                'ppn_total' => $this->parseAmount($request->input('ppn_total')),
                'biaya_kirim' => $this->parseAmount($request->input('biaya_kirim')),
                'grand_total' => $this->parseAmount($request->input('grand_total')),
                
                // Informasi summary
                'ctns_total' => $this->parseAmount($request->input('summary.total_carton')),
                'gw_total' => $this->parseAmount($request->input('summary.total_weight')),
                'cbm_total' => $this->parseAmount($request->input('summary.total_volume')),
                
                // Metode kalkulasi
                'calculation_method' => $request->input('calculation_method_used'),
                'cbm_price' => $this->parseAmount($request->input('harga_ongkir_cbm')),
                'kg_price' => $this->parseAmount($request->input('harga_ongkir_wg')),
            ];
            
            // Hitung total price berdasarkan kedua metode
            $shippingData['total_price_cbm'] = $shippingData['cbm_total'] * $shippingData['cbm_price'];
            $shippingData['total_price_gw'] = $shippingData['gw_total'] * $shippingData['kg_price'];
            
            $shipping->update($shippingData);
            
            // Update file invoice jika ada
            if ($request->hasFile('invoice_file')) {
                // Hapus file lama jika ada
                if ($shipping->invoice_file) {
                    $oldFilePath = public_path('shipping/invoices/' . $shipping->invoice_file);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                $invoiceFile = $request->file('invoice_file');
                $invoiceFileName = 'invoice_' . time() . '_' . $invoiceFile->getClientOriginalName();
                
                // Buat direktori jika belum ada
                $uploadPath = public_path('shipping/invoices');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Pindahkan file ke direktori public
                $invoiceFile->move($uploadPath, $invoiceFileName);
                $shipping->invoice_file = $invoiceFileName;
                $shipping->save();
            }
            
            // Update file packagelist jika ada
            if ($request->hasFile('packagelist_file')) {
                // Hapus file lama jika ada
                if ($shipping->packagelist_file) {
                    $oldFilePath = public_path('shipping/packagelists/' . $shipping->packagelist_file);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                $packagelistFile = $request->file('packagelist_file');
                $packagelistFileName = 'packagelist_' . time() . '_' . $packagelistFile->getClientOriginalName();
                
                // Buat direktori jika belum ada
                $uploadPath = public_path('shipping/packagelists');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Pindahkan file ke direktori public
                $packagelistFile->move($uploadPath, $packagelistFileName);
                $shipping->packagelist_file = $packagelistFileName;
                $shipping->save();
            }
            
            // Update atau tambahkan detail barang
            if (isset($request->barang) && is_array($request->barang)) {
                // Collect IDs of details that should be kept
                $existingDetailIds = [];
                
                foreach ($request->barang as $index => $item) {
                    if (isset($item['id'])) {
                        // Update existing detail
                        $detail = ShippingDetail::find($item['id']);
                        if ($detail && $detail->shipping_id == $shipping->id) {
                            $detail->update([
                                'product_id' => $item['product_id'],
                                'name' => $item['name'] ?? null,
                                'ctn' => $item['ctn'],
                                'qty_per_ctn' => $item['qty_per_ctn'],
                                'ctns' => $item['ctns'],
                                'qty' => $item['qty'],
                                'length' => $item['length'],
                                'width' => $item['width'],
                                'high' => $item['high'],
                                'gw_per_ctn' => $item['gw_per_ctn'],
                                'volume' => $this->parseAmount($item['volume']),
                                'total_gw' => $this->parseAmount($item['total_gw']),
                            ]);
                            $existingDetailIds[] = $detail->id;
                        }
                    } else {
                        // Create new detail
                        $detailData = [
                            'shipping_id' => $shipping->id,
                            'product_id' => $item['product_id'],
                            'name' => $item['name'] ?? null,
                            'ctn' => $item['ctn'],
                            'qty_per_ctn' => $item['qty_per_ctn'],
                            'ctns' => $item['ctns'],
                            'qty' => $item['qty'],
                            'length' => $item['length'],
                            'width' => $item['width'],
                            'high' => $item['high'],
                            'gw_per_ctn' => $item['gw_per_ctn'],
                            'volume' => $this->parseAmount($item['volume']),
                            'total_gw' => $this->parseAmount($item['total_gw']),
                        ];
                        
                        $detail = ShippingDetail::create($detailData);
                        $existingDetailIds[] = $detail->id;
                    }
                    
                    // Update product image if provided
                    if (isset($request->file('barang')[$index]['product_image'])) {
                        // Delete old image if exists
                        if ($detail->product_image) {
                            $oldImagePath = public_path('shipping/products/' . $detail->product_image);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                        
                        $productImage = $request->file('barang')[$index]['product_image'];
                        $imageFileName = 'product_' . $shipping->id . '_' . $index . '_' . time() . '_' . $productImage->getClientOriginalName();
                        
                        // Buat direktori jika belum ada
                        $uploadPath = public_path('shipping/products');
                        if (!file_exists($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }
                        
                        // Pindahkan file ke direktori public
                        $productImage->move($uploadPath, $imageFileName);
                        $detail->product_image = $imageFileName;
                        $detail->save();
                    }
                }
                
                // Remove details that are not in the request
                $shipping->shippingDetails()->whereNotIn('id', $existingDetailIds)->each(function ($detail) {
                    // Delete product image if exists
                    if ($detail->product_image) {
                        $imagePath = public_path('shipping/products/' . $detail->product_image);
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                    $detail->delete();
                });
            }
            
            $this->logShippingActivity($shipping->id, 'update', 'Data shipping diupdate');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data shipping berhasil diupdate',
                'shipping_id' => $shipping->id,
                'redirect_url' => route('shippings.show', $shipping->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error updating shipping data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipping $shipping)
    {
        //
    }
    
    /**
     * Generate invoice number
     */
    public function generateInvoice(Request $request)
    {
        $type = $request->query('type', ShippingType::LCL->value);
        $dateNow = date('dmy');
        $countOfShipping = Shipping::where('shipping_type', $type)->count() + 1;
        $invoice = strtoupper($type) . '-' . $dateNow . str_pad($countOfShipping, 3, '0', STR_PAD_LEFT) . '-1';
        
        return response()->json([
            'invoice' => $invoice
        ]);
    }
    
    /**
     * Helper method to parse amount from formatted string
     */
    private function parseAmount($formattedNumber)
    {
        if (empty($formattedNumber)) {
            return 0;
        }
        
        // Mengubah string "1.234,56" menjadi float 1234.56
        return (float) str_replace(['.',','], ['','.'], $formattedNumber);
    }
    
    /**
     * Log shipping activity
     */
    private function logShippingActivity($shippingId, $status, $notes = '')
    {
        ShippingLog::create([
            'shipping_id' => $shippingId,
            'user_id' => Auth::id() ?? 1,
            'status' => $status,
            'notes' => $notes,
           
        ]);
    }

    /**
     * Generate PDF Surat Jalan
     */
    public function suratJalan(Shipping $shipping)
    {
        $pdf = App::make('dompdf.wrapper');

       $pdf = $pdf->loadView('backend.shippings.surat-jalan', compact('shipping'));
        return $pdf->stream('surat-jalan-'.now().'.pdf');
    }

    /**
     * Generate PDF Faktur
     */
    public function faktur(Shipping $shipping)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf = $pdf->loadView('backend.shippings.faktur', compact('shipping'));
        return $pdf->stream('faktur-'.now().'.pdf');
    }
}
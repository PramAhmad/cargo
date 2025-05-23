<tr data-product-id="{{ $product->id }}" class="{{ $level > 0 ? 'child-row' : '' }}">
    <td>
        <div class="flex items-center">
            @if($level > 0)
                <div class="ml-{{ $level * 4 }} mr-2 h-6 border-l border-b border-slate-300 dark:border-slate-600 w-4"></div>
            @endif
            <span class="font-medium">{{ $product->name }}</span>
        </div>
    </td>
    <td>
        @if($product->category)
            <span class="badge badge-soft-primary">
                {{ $product->category->name }}
            </span>
        @else
            <span class="text-slate-400">-</span>
        @endif
    </td>
    
    @if($freightType == 'sea')
        <!-- SEA Pricing Columns -->
        <td>
            @if($product->category && $product->category->mit_price_cbm_sea)
                <span class="text-slate-700 dark:text-slate-300">
                    Rp {{ number_format($product->category->mit_price_cbm_sea, 0, ',', '.') }}
                </span>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </td>
        <td>
            @if($product->category && $product->category->mit_price_kg_sea)
                <span class="text-slate-700 dark:text-slate-300">
                    Rp {{ number_format($product->category->mit_price_kg_sea, 0, ',', '.') }}
                </span>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </td>
        <td>
            @if($product->category && $product->category->cust_price_cbm_sea)
                <span class="text-slate-700 dark:text-slate-300">
                    Rp {{ number_format($product->category->cust_price_cbm_sea, 0, ',', '.') }}
                </span>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </td>
        <td>
            @if($product->category && $product->category->cust_price_kg_sea)
                <span class="text-slate-700 dark:text-slate-300">
                    Rp {{ number_format($product->category->cust_price_kg_sea, 0, ',', '.') }}
                </span>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </td>
    @else
        <!-- AIR Pricing Columns -->
        <td>
            @if($product->category && $product->category->mit_price_cbm_air)
                <span class="text-slate-700 dark:text-slate-300">
                    Rp {{ number_format($product->category->mit_price_cbm_air, 0, ',', '.') }}
                </span>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </td>
        <td>
            @if($product->category && $product->category->mit_price_kg_air)
                <span class="text-slate-700 dark:text-slate-300">
                    Rp {{ number_format($product->category->mit_price_kg_air, 0, ',', '.') }}
                </span>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </td>
        <td>
            @if($product->category && $product->category->cust_price_cbm_air)
                <span class="text-slate-700 dark:text-slate-300">
                    Rp {{ number_format($product->category->cust_price_cbm_air, 0, ',', '.') }}
                </span>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </td>
        <td>
            @if($product->category && $product->category->cust_price_kg_air)
                <span class="text-slate-700 dark:text-slate-300">
                    Rp {{ number_format($product->category->cust_price_kg_air, 0, ',', '.') }}
                </span>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </td>
    @endif
    
    <td>
        <div class="flex space-x-2">
            <a href="{{ route('mitra.warehouses.products.edit', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id, 'product' => $product->id]) }}" 
               class="btn btn-icon btn-sm btn-secondary">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" 
                    class="btn btn-icon btn-sm btn-danger delete-product-btn" 
                    data-product-id="{{ $product->id }}" 
                    data-product-name="{{ $product->name }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </td>
</tr>

@if($product->children)
    @foreach($product->children as $child)
        @include('backend.mitras.warehouses.products.product_row', ['product' => $child, 'level' => $level + 1, 'freightType' => $freightType])
    @endforeach
@endif
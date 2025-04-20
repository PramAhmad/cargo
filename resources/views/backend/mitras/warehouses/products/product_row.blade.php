<tr data-product-id="{{ $product->id }}" class="{{ $level > 0 ? 'child-row' : '' }}">
    <td class="pl-{{ $level * 4 + 4 }}">
        @if($level > 0)
            <span class="inline-block w-4 border-l-2 border-b-2 border-slate-300 mr-2"></span>
        @endif
        {{ $product->name }}
    </td>
    <td>
        @if($product->category)
            <span class="badge badge-soft-primary">{{ $product->category->name }}</span>
        @else
            <span class="text-slate-400">-</span>
        @endif
    </td>
    <td>Rp {{ number_format($product->mit_price_cbm, 0, ',', '.') }}</td>
    <td>Rp {{ number_format($product->mit_price_kg, 0, ',', '.') }}</td>
    <td>Rp {{ number_format($product->cust_price_cbm, 0, ',', '.') }}</td>
    <td>Rp {{ number_format($product->cust_price_kg, 0, ',', '.') }}</td>
    <td>
        <div class="flex space-x-2">
            <a href="{{ route('mitra.warehouses.products.edit', ['mitra' => $mitra->id, 'warehouse' => $warehouse->id, 'product' => $product->id]) }}" class="btn btn-sm btn-info">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-sm btn-danger delete-product-btn" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </td>
</tr>

@if($product->children && $product->children->count() > 0)
    @foreach($product->children as $child)
        @include('backend.mitras.warehouses.products.product_row', ['product' => $child, 'level' => $level + 1])
    @endforeach
@endif
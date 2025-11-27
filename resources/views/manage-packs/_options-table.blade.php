<table class="table table-sm">
    <tbody>
        @foreach ($options as $option)
            <tr>
                <td>Option {{ $option->option }} (Price: ${{ number_format($option->price, 2) }})</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-info" data-bs-toggle="collapse"
                        data-bs-target="#productsForOption{{ $option->id }}">
                        View Products ({{ $option->products->count() }})
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="p-0">
                    <div class="collapse" id="productsForOption{{ $option->id }}">
                        <div class="p-3 bg-light">
                            {{-- Include partial for the product list --}}
                            @include('manage-packs._product-list-for-option', ['option' => $option])
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

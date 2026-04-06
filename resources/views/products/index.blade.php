<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Products
        </h2>
    </x-slot>

    <div class="p-6">
        <a href="{{ route('products.create') }}" style="background:#22c55e;color:white;padding:10px;border-radius:6px;">
            Add Product
        </a>

        <table border="1" width="100%" style="margin-top:20px">
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
            </tr>

            @forelse($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->price }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No products found.</td>
                </tr>
            @endforelse
        </table>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <a href="{{ route('products.show', $product->product_id) }}" 
                       class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden hover:shadow-lg transition">
                        @if ($product->image)
    @if (str_starts_with($product->image, 'http'))
        <img src="{{ $product->image }}" class="w-full h-48 object-cover">
    @else
        <img src="{{ asset('storage/products/' . $product->image) }}" class="w-full h-48 object-cover">
    @endif
@else
    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
        <span class="text-gray-400">No Image</span>
    </div>
@endif
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                            <p class="text-green-600 font-bold mt-1">฿{{ number_format($product->price, 2) }}</p>
                            <p class="text-sm text-gray-500 mt-1">remaining: {{ $product->stock_number }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500">no products available</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
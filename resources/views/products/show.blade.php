<x-app-layout>

<div class="max-w-6xl mx-auto py-10 px-6 bg-white rounded-2xl shadow">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        {{-- picture --}}
<div>

@if ($product->image)

    @if (str_starts_with($product->image, 'http'))
        <img src="{{ $product->image }}" class="w-full h-[420px] object-cover rounded-xl">
    @else
        <img src="{{ asset('storage/products/'.$product->image) }}" class="w-full h-[420px] object-cover rounded-xl">
    @endif

@else

<div class="w-full h-[420px] bg-gray-200 flex items-center justify-center rounded-xl">
    <span class="text-gray-400">No Image</span>
</div>

@endif

</div>

        {{-- description --}}
        <div>

            {{-- tag --}}
            <div class="flex gap-2 text-sm mb-3">
    @foreach ($product->tags as $tag)
        <span class="bg-gray-200 px-3 py-1 rounded-full">#{{ $tag->name }}</span>
    @endforeach
</div>


            {{-- name --}}
            <h1 class="text-2xl font-bold">
                {{ $product->name }}
            </h1>

            {{-- rating --}}
            <div class="flex items-center mt-2 text-yellow-500">
                ⭐⭐⭐⭐☆
                <span class="text-gray-500 ml-2 text-sm">(45 รีวิว)</span>
            </div>

            {{-- price --}}
            <div class="mt-4">

                <span class="text-3xl font-bold text-brown-600">
                    ฿{{ number_format($product->price,2) }}
                </span>
                <span class="text-gray-500 line-through ml-4">
                    ฿{{ number_format($product->price * 1.2,2) }}
                </span> 
            </div>

            {{-- description --}}
            <p class="mt-4 text-gray-600">
                {{ $product->description }}
            </p>

            {{-- stock --}}
            <p class="mt-3 text-sm text-gray-500">
                In stock {{ $product->stock_number }} ชิ้น
            </p>

            {{-- total --}}
            <div class="flex items-center mt-6 gap-4">
    <span>Total:</span>
    <div class="flex border rounded-lg">
        <button onclick="changeQty(-1)" class="px-3 py-1">-</button>
        <input type="text" id="quantity" value="1" min="1" max="{{ $product->stock_number }}"
            class="w-12 text-center border-x">
        <button onclick="changeQty(1)" class="px-3 py-1">+</button>
    </div>
</div>

<script>
function changeQty(change) {
    const input = document.getElementById('quantity');
    const max = {{ $product->stock_number }};
    let value = parseInt(input.value) + change;
    
    if (value < 1) value = 1;
    if (value > max) value = max;
    
    input.value = value;

    // sync to hidden input
    document.getElementById('quantity-input').value = value;
}
</script>
            {{-- ปุ่ม --}}
            <div class="flex gap-4 mt-8">

                <button class="border px-6 py-3 rounded-lg hover:bg-gray-100">
                    ❤️
                </button>

                <form method="POST" action="{{ route('carts.store') }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
        <input type="hidden" name="quantity" id="quantity-input" value="1">
        <button type="submit" class="border px-6 py-3 rounded-lg hover:bg-gray-100">
            🛒 Add to cart
        </button>
    </form>

                <button class="bg-brown-600 text-white px-6 py-3 rounded-lg">
                    ⚡ ซื้อเลย
                </button>

            </div>

        </div>

    </div>

</div>

</x-app-layout>
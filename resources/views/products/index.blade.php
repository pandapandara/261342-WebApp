<x-app-layout class="{{ ($mode ?? 'online') === 'Secondhand/2nd hand' ? 'theme-brown' : '' }}">

    <div class="page-wrap">
        <div class="container">
            {{-- Hero Banner + Mode Toggle --}}
<div class="relative w-full rounded-2xl overflow-hidden mb-8" style="height: 220px;">
    {{-- Background --}}
<div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://i.ibb.co/238kHWcy/LINE-ALBUM-Banner-260323-1.jpg');
    {{ ($mode ?? 'online') === 'Secondhand/2nd hand' ? 'filter: sepia(0.8) saturate(0.8);' : '' }}">
</div>
<div class="absolute inset-0" style="background: {{ ($mode ?? 'online') === 'Secondhand/2nd hand' 
    ? 'linear-gradient(135deg, rgba(101,67,33,0.75), rgba(180,120,60,0.6))' 
    : 'linear-gradient(135deg, rgba(219,39,119,0.75), rgba(244,143,177,0.6))' }}"></div>

    {{-- Content --}}
    <div class="relative h-full flex flex-col items-center justify-center gap-3 px-6 text-center">
        <h2 class="text-white text-3xl font-bold tracking-wide">✨ Stellar Market</h2>
        <p class="text-white/90 text-sm max-w-md">
            {{ ($mode ?? 'online') === 'Secondhand/2nd hand' 
                ? 'Explore pre-loved treasures — from vintage finds to gently used gems.' 
                : 'Browse our collection of new products, carefully curated for quality and style.' }}
        </p>

        {{-- Mode Toggle inside banner --}}
        <div class="inline-flex rounded-full p-1 mt-1" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(8px);">
            <a href="{{ request()->fullUrlWithQuery(['mode' => 'Online']) }}"
               class="px-5 py-1.5 rounded-full text-sm font-bold transition"
               style="{{ ($mode ?? 'online') !== 'Secondhand/2nd hand' ? 'background: white; color: var(--primary);' : 'color: white; opacity: 0.8;' }}">
               💎 New
            </a>
            <a href="{{ request()->fullUrlWithQuery(['mode' => 'Secondhand/2nd hand']) }}"
               class="px-5 py-1.5 rounded-full text-sm font-bold transition"
               style="{{ ($mode ?? 'online') === 'Secondhand/2nd hand' ? 'background: white; color: var(--primary);' : 'color: white; opacity: 0.8;' }}">
                ♻️ 2nd Hand
            </a>
        </div>
    </div>
</div>
            <script>
            const swiper = new Swiper('.swiper', {
                loop: true,
                autoplay: { delay: 3000 },
                pagination: { el: '.swiper-pagination' },
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            });
            </script>

            {{-- Search Bar --}}
            <div class="mb-8 card card-pad" style="border-top: 3px solid var(--secondary);">
                <form action="{{ route('products.index') }}" method="GET" class="flex gap-3 flex-wrap items-center">
                    <div class="flex-1 min-w-64 relative">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search products..."
                            value="{{ $search ?? '' }}"
                            class="input w-full"
                        />
                        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
                    </div>

                    @if ($category ?? false)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif

                    <button type="submit" class="btn-primary">
                        Search
                    </button>

                    @if ($search ?? false)
                        @php
                            $clearUrl = route('products.index');
                            if ($category ?? false) {
                                $clearUrl .= '?category=' . urlencode($category) . '&mode=' . urlencode($mode ?? 'online');
                            } else {
                                $clearUrl .= '?mode=' . urlencode($mode ?? 'online');
                            }
                        @endphp

                        <a href="{{ $clearUrl }}" class="btn" style="background: var(--pinkPage-neutral-2); color: var(--text);">
                            ✕ Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Category Filter --}}
            @if ($categories->count() > 0)
                <div class="mb-10 card card-pad">
                    <h3 class="h2 mb-4 flex items-center gap-2">
                        <span>🏷️</span> Category
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <a
                            href="{{ route('products.index', ['mode' => $mode ?? 'online']) }}"
                            class="badge transition hover:shadow-md"
                            style="
                                background: {{ !($category ?? false) ? 'linear-gradient(135deg, var(--primary), var(--secondary))' : 'var(--pinkPage-neutral-2)' }};
                                color: {{ !($category ?? false) ? '#fff' : 'var(--text)' }};
                                padding: 0.5rem 1rem;
                                border-radius: 9999px;
                                border: 1px solid {{ !($category ?? false) ? 'transparent' : 'rgba(0,0,0,0.08)' }};
                            "
                        >
                            All
                        </a>

                        @foreach ($categories as $cat)
                            <a
                                href="{{ route('products.index', ['category' => $cat, 'mode' => $mode ?? 'online']) }}"
                                class="badge transition hover:shadow-md"
                                style="
                                    background: {{ (($category ?? false) === $cat) ? 'linear-gradient(135deg, var(--primary), var(--secondary))' : 'var(--pinkPage-neutral-2)' }};
                                    color: {{ (($category ?? false) === $cat) ? '#fff' : 'var(--text)' }};
                                    padding: 0.5rem 1rem;
                                    border-radius: 9999px;
                                    border: 1px solid {{ (($category ?? false) === $cat) ? 'transparent' : 'rgba(0,0,0,0.08)' }};
                                "
                            >
                                {{ $cat }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Product Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <div class="card group overflow-hidden transition-all hover:shadow-lg hover:scale-105 duration-300"
                         style="border: 1px solid rgba(0,0,0,0.05);">
                        {{-- Image Container --}}
                        <div class="relative h-56 w-full overflow-hidden bg-gradient-to-br" style="background: linear-gradient(135deg, var(--pinkPage-neutral-2), rgba(0,0,0,0.02));">
                            @if ($product->image)
                                @php
                                    $imageUrl = str_starts_with($product->image, 'http')
                                        ? $product->image
                                        : route('product.photo', ['filename' => basename($product->image)]);
                                @endphp
                                <img src="{{ $imageUrl }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center flex-col gap-2" style="background: var(--pinkPage-neutral-2);">
                                    <span class="text-2xl">📸</span>
                                    <span class="muted text-xs">No Image</span>
                                </div>
                            @endif
    
                        {{-- Stock Badge (ย้ายมาซ้ายบน) --}}
                        <div class="absolute top-3 left-3 text-xs px-3 py-1 rounded-full font-semibold backdrop-blur-sm shadow-sm"
                            style="background: rgba(255,255,255,0.9); color: var(--text);">
                            {{ $product->stock_number > 0 ? '✓ In Stock' : '✗ Out' }}
                        </div>

                        {{-- 🌟 Wishlist (Favorite) Button (ขวาบน) --}}
                        @auth
                            @php
                                $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                                                    ->where('product_id', $product->product_id)
                                                    ->exists();
                            @endphp
                            <button type="button" 
                                onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(this, {{ $product->product_id }})" 
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-md transition-transform hover:scale-110 z-20 cursor-pointer"
                                style="color: var(--favorite-star);">
                            
                            <svg class="w-5 h-5 wishlist-icon transition-colors duration-200" 
                                fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" 
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </button>
                        @endauth
                        </div>

                        <div class="card-pad flex flex-col gap-3 flex-1">
                            {{-- Product Name --}}
                            <h3 class="font-bold text-lg truncate" style="color: var(--text);">
                                {{ $product->name }}
                            </h3>

                            {{-- Price & Stock --}}
                            <div class="flex items-center justify-between gap-2 pb-3" style="border-bottom: 1px solid rgba(0,0,0,0.06);">
                                <span class="font-extrabold text-2xl" style="color: var(--secondary);">
                                    ฿{{ number_format($product->price, 2) }}
                                </span>
                                <span class="text-xs px-2 py-1 rounded" style="background: var(--pinkPage-neutral-2); color: var(--text);">
                                    {{ $product->stock_number }} left
                                </span>
                            </div>

                            {{-- Tags --}}
                            @if($product->tags->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($product->tags as $tag)
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold uppercase"
                                              style="background: var(--accent); color: var(--text); opacity: 0.9;">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Action Button --}}
                            <div class="mt-auto pt-3">
                                @auth
                                    <a href="{{ route('products.show', $product->product_id) }}" class="btn-primary w-full flex items-center justify-center gap-2">
                                        👁️ View Details
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn w-full flex items-center justify-center gap-2" style="background: var(--neutral); color: #fff;">
                                        🔐 Login to Order
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-5xl mb-4">😅</div>
                        <p class="muted text-lg">
                            {{ $search ? 'No products match your search.' : 'No products available' }}
                        </p>
                        @if($search)
                            <p class="muted text-sm mt-2">Try adjusting your search or filters</p>
                        @endif
                    </div>

                    {{-- Wishlist Toggle Script --}}
                    <script>
                    function toggleWishlist(btn, productId) {
                        btn.disabled = true;
                        const icon = btn.querySelector('.wishlist-icon');
                    
                        icon.style.opacity = '0.5';

                        // 2. ส่งข้อมูลไปที่ Controller
                        fetch('{{ route("wishlist.toggle") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ product_id: productId })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network Error');
                            return response.json();
                        })
                        .then(data => {
                            if (data.wishlisted) {
                                icon.setAttribute('fill', 'currentColor'); // เติมสีทึบ
                            } else {
                                icon.setAttribute('fill', 'none'); // เอาสีออก (โปร่งใส)
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating wishlist. Please try again.');
                        })
                        .finally(() => {
                            btn.disabled = false;
                            icon.style.opacity = '1';
                        });
                    }
                </script>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>

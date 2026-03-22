<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--neutral);">
            {{ __('✨ Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: var(--bg);">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-[var(--radius-2xl)] p-8 border border-pink-50" style="background-color: var(--surface);">
                
                <form method="POST" action="{{ route('seller.products.update', $product->product_id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Left hand-side: Basic Information --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name</label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" placeholder="What is the product name..."
                                    class="w-full border-gray-200 rounded-[var(--radius-lg)] p-3 text-sm focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors">
                                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1" style="color: var(--text);">Description</label>
                                <textarea name="description" rows="4" placeholder="Brief description..."
                                    class="w-full border-gray-200 rounded-[var(--radius-lg)] p-3 text-sm focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors">{{ old('description', $product->description) }}</textarea>
                                @error('description') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold mb-1" style="color: var(--text);">Price</label>
                                    <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                                        class="w-full border-gray-200 rounded-[var(--radius-lg)] p-3 text-sm focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors">
                                    @error('price') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold mb-1" style="color: var(--text);">Stock Amount</label>
                                    <input type="number" name="stock_number" value="{{ old('stock_number', $product->stock_number) }}" min="0"
                                        class="w-full border-gray-200 rounded-[var(--radius-lg)] p-3 text-sm focus:ring-[var(--primary)] focus:border-[var(--primary)] transition-colors">
                                    @error('stock_number') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Right hand-side: Image and Tag --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold mb-2" style="color: var(--text);">Change Image</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 rounded-2xl hover:border-pink-400 transition-colors" style="background-color: var(--bg);">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 00-4 4H12a4 4 0 00-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 005.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                            <label for="file-upload" class="relative cursor-pointer font-medium text-pink-600 hover:text-pink-500 focus-within:outline-none">
                                                <span>Click to upload</span>
                                                <input id="file-upload" name="image" type="file" class="sr-only" accept="image/*">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG up to 10MB</p>
                                    </div>
                                </div>
                                @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{--tag selector--}} 

                            @php
                        $vintageNames = ['Secondhand/2nd hand', 'vintage'];
                        
                        $vintageTags = $tags->filter(fn($tag) => in_array($tag->name, $vintageNames));
            
                        $categoryTags = $tags->reject(fn($tag) => in_array($tag->name, $vintageNames));
                        @endphp

                            {{-- Tag Selector 1: Vintage/2nd handed --}}
                            <div>
                                <label class="block text-sm font-bold mb-2" style="color: var(--text);">Vintage/2nd handed product?</label>
                                <div class="flex flex-wrap gap-2 p-4 border border-gray-200 rounded-2xl shadow-inner" style="background-color: var(--bg);">
                                    @foreach($vintageTags as $tag)
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer"
                                                {{ $product->tags->contains($tag->id) ? 'checked' : '' }}>
                                            <div class="px-4 py-1.5 text-xs font-bold rounded-full border border-gray-300 text-gray-500 bg-white
                                                peer-checked:bg-[var(--accent)] peer-checked:text-[var(--neutral)] peer-checked:border-[var(--secondary)] hover:border-[var(--primary)] transition-all shadow-sm">
                                                #{{ $tag->name }}
                                            </div>
                                        </label>
                                    @endforeach
                                    
                                    @if($vintageTags->isEmpty())
                                        <p class="text-xs text-gray-400 italic">no vintage/2nd hand tags available</p>
                                    @endif
                                </div>
                                @error('tags') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            {{-- Tag Selector 2: Product categories --}}
                            <div>
                                <label class="block text-sm font-bold mb-2" style="color: var(--text);">Product categories</label>
                                <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-4 border border-gray-200 rounded-2xl shadow-inner" style="background-color: var(--bg);">
                                    @foreach($categoryTags as $tag)
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer"
                                                {{ $product->tags->contains($tag->id) ? 'checked' : '' }}>
                                            <div class="px-3 py-1.5 text-xs font-semibold rounded-full border border-gray-200 text-gray-500 bg-white
                                                peer-checked:bg-[var(--accent)] peer-checked:text-[var(--neutral)] peer-checked:border-[var(--secondary)] hover:border-[var(--primary)] transition-all shadow-sm">
                                                #{{ $tag->name }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('tags') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 dark:border-gray-700">

                    {{-- Button --}}
                    <div class="flex justify-between items-center">
                        <a href="{{ route('seller.index') }}" class="text-sm text-gray-500 hover:text-pink-500 flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 text-white font-bold rounded-2xl shadow-lg transition-all" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); box-shadow: 0 4px 15px rgba(236,161,182,0.3);" onmouseover="this.style.transform = 'translateY(-2px)'; this.style.boxShadow = '0 6px 20px rgba(236,161,182,0.4)'" onmouseout="this.style.transform = 'translateY(0)'; this.style.boxShadow = '0 4px 15px rgba(236,161,182,0.3)'">
                            ✨ Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('file-upload').onchange = evt => {
        const [file] = document.getElementById('file-upload').files
        if (file) {
            // create preview element if it doesn't exist
            let preview = document.getElementById('preview-img');
            if(!preview) {
                preview = document.createElement('img');
                preview.id = 'preview-img';
                preview.className = 'mt-4 mx-auto h-40 w-full object-cover rounded-2xl shadow-md border-2 border-pink-100';
                document.querySelector('.border-dashed').after(preview);
            }
            preview.src = URL.createObjectURL(file)
        }
    }
</script>
</x-app-layout>
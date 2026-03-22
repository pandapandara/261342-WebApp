<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--neutral);">
            ✨ Create New Product
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: var(--bg);">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-md rounded-3xl p-8 border border-pink-100" style="background-color: var(--surface);">
                
                <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Left: Basic Info --}}
                        <div class="space-y-5">

                            <div>
                                <label class="block text-sm font-bold mb-1.5" style="color: var(--text);">Product Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="What is the product name..."
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-300 focus:border-transparent transition">
                                @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1.5" style="color: var(--text);">Description</label>
                                <textarea name="description" rows="5" placeholder="Brief description..."
                                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-300 focus:border-transparent transition resize-none">{{ old('description') }}</textarea>
                                @error('description') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold mb-1.5" style="color: var(--text);">Price (฿)</label>
                                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" placeholder="0.00"
                                        class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-300 focus:border-transparent transition">
                                    @error('price') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold mb-1.5" style="color: var(--text);">Stock Amount</label>
                                    <input type="number" name="stock_number" value="{{ old('stock_number') }}" min="0" placeholder="0"
                                        class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-300 focus:border-transparent transition">
                                    @error('stock_number') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Right: Image + Tags --}}
                        <div class="space-y-5">

                            {{-- Image Upload --}}
                            <div>
                                <label class="block text-sm font-bold mb-1.5" style="color: var(--text);">Product Image</label>
                                <div id="upload-area" class="relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-pink-200 rounded-2xl cursor-pointer hover:border-pink-400 hover:bg-pink-50/30 transition-all" onclick="document.getElementById('file-upload').click()">
                                    <img id="preview-img" src="" class="hidden w-full h-full object-cover rounded-2xl absolute inset-0">
                                    <div id="upload-placeholder" class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 text-pink-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 00-4 4H12a4 4 0 00-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <p class="text-sm font-semibold text-pink-400">Click to upload</p>
                                        <p class="text-xs text-gray-400">PNG, JPG up to 10MB</p>
                                    </div>
                                    <input id="file-upload" name="image" type="file" class="hidden" accept="image/*">
                                </div>
                                @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            @php
                                $vintageNames = ['Secondhand/2nd hand', 'vintage'];
                                $vintageTags = $tags->filter(fn($tag) => in_array($tag->name, $vintageNames));
                                $categoryTags = $tags->reject(fn($tag) => in_array($tag->name, $vintageNames));
                            @endphp

                            {{-- Vintage Tags --}}
                            <div>
                                <label class="block text-sm font-bold mb-1.5" style="color: var(--text);">Vintage / 2nd Hand?</label>
                                <div class="flex flex-wrap gap-2 p-3 border border-gray-100 rounded-2xl bg-gray-50/50">
                                    @foreach($vintageTags as $tag)
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer"
                                                {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                                            <div class="px-4 py-1.5 text-xs font-bold rounded-full border border-gray-200 text-gray-500 bg-white
                                                peer-checked:bg-pink-500 peer-checked:text-white peer-checked:border-pink-500 hover:border-pink-300 transition-all shadow-sm">
                                                #{{ $tag->name }}
                                            </div>
                                        </label>
                                    @endforeach
                                    @if($vintageTags->isEmpty())
                                        <p class="text-xs text-gray-400 italic">No tags available</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Category Tags --}}
                            <div>
                                <label class="block text-sm font-bold mb-1.5" style="color: var(--text);">Product Categories</label>
                                <div class="flex flex-wrap gap-2 max-h-36 overflow-y-auto p-3 border border-gray-100 rounded-2xl bg-gray-50/50">
                                    @foreach($categoryTags as $tag)
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer"
                                                {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                                            <div class="px-3 py-1.5 text-xs font-semibold rounded-full border border-gray-200 text-gray-500 bg-white
                                                peer-checked:bg-pink-500 peer-checked:text-white peer-checked:border-pink-500 hover:border-pink-300 transition-all shadow-sm">
                                                #{{ $tag->name }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('tags') <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Buttons --}}
                    <div class="flex justify-between items-center pt-2">
                        <a href="{{ route('seller.index') }}" class="text-sm text-gray-400 hover:text-pink-500 flex items-center gap-1 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 bg-pink-500 text-white font-bold rounded-2xl shadow-lg shadow-pink-200 hover:bg-pink-600 hover:shadow-pink-300 transform hover:-translate-y-0.5 transition-all">
                            ✨ Save new product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('file-upload').onchange = evt => {
        const [file] = evt.target.files;
        if (file) {
            const preview = document.getElementById('preview-img');
            const placeholder = document.getElementById('upload-placeholder');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
    }
    </script>
</x-app-layout>
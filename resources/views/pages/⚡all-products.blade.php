<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Url]
    public string $category = '';

    public function getAllCategories()
    {
        return Category::query()->get();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    #[Computed(persist: true, seconds: 2592000, cache: true)]
    public function products()
    {
        return Product::query()
            ->with('category')
            ->when($this->category !== '', function ($query) {
                $query->whereHas('category', function ($query) {
                    $query->where('slug', $this->category);
                });
            })
            ->paginate(20);
    }

    public function orderRequest(string $productURL)
    {
        $this->redirectRoute('order-request', [
            'product_link' => $productURL,
        ]);
    }

    public function viewProduct(string $slug)
    {
        redirect("/product/{$slug}");
    }
};
?>

<div class="bg-gray-50 min-h-screen">

    <div class="max-w-7xl mx-auto px-4 md:px-8 py-8">

        <div class="flex gap-8">

            {{-- Sidebar (Desktop) --}}
            <aside class="hidden md:flex flex-col gap-1 w-44 shrink-0">

                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                    Categories
                </p>

                {{-- All --}}
                <button wire:click="$set('category', '')" class="{{ $category === '' ? 'bg-primary-50 text-primary-600 font-semibold border-primary-200' : 'text-gray-500 hover:bg-gray-100 border-transparent' }}
                    text-left text-sm px-3 py-2 rounded-xl border transition-colors duration-150">
                    All
                </button>

                {{-- Categories --}}
                @foreach ($this->getAllCategories() as $cat)

                <button wire:click="$set('category', '{{ $cat->slug }}')" class="{{ $category === $cat->slug ? 'bg-primary-50 text-primary-600 font-semibold border-primary-200' : 'text-gray-500 hover:bg-gray-100 border-transparent' }}
                        text-left text-sm px-3 py-2 rounded-xl border transition-colors duration-150">
                    {{ $cat->name }}
                </button>

                @endforeach

            </aside>

            {{-- Main --}}
            <div class="flex-1 min-w-0">

                {{-- MOBILE CATEGORIES --}}
                <div class="flex md:hidden gap-2 overflow-x-auto pb-3 mb-4">

                    <button wire:click="$set('category', '')" class="{{ $category === '' ? 'bg-primary-500 text-white' : 'bg-white text-gray-500 border' }}
                        text-xs font-semibold px-4 py-1.5 rounded-full whitespace-nowrap">
                        All
                    </button>

                    @foreach ($this->getAllCategories() as $cat)

                    <button wire:click="$set('category', '{{ $cat->slug }}')" class="{{ $category === $cat->slug ? 'bg-primary-500 text-white' : 'bg-white text-gray-500 border' }}
                            text-xs font-semibold px-4 py-1.5 rounded-full whitespace-nowrap">
                        {{ $cat->name }}
                    </button>

                    @endforeach

                </div>

                {{-- PRODUCTS --}}
                @if ($this->products()->count())

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">

                    @foreach ($this->products() as $product)

                    <div
                        wire:click='viewProduct("{{$product->slug}}")'
                        class="cursor-pointer bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-md hover:border-gray-300 transition-all flex flex-col">

                        {{-- Image --}}
                        @if ($product->product_images[0] ?? null)
                            <div class="aspect-square w-full overflow-hidden">
                                <img src="{{ Storage::url($product->product_images[0]) }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="p-2.5 flex flex-col flex-1">

                            <p class="text-xs font-medium text-gray-800 truncate mb-1">
                                {{ $product->name }}
                            </p>

                            <p class="text-[10px] text-gray-400 mb-2">
                                {{ $product->category?->name }}
                            </p>

                            <div class="mt-auto">

                                <span class="text-sm font-bold text-primary-500">
                                    ৳{{ $product->price }}
                                </span>

                                @if ($product->old_price)
                                <span class="text-[10px] text-gray-400 line-through ml-1">
                                    ৳{{ $product->old_price }}
                                </span>
                                @endif

                            </div>

                            <div class="flex gap-1.5 mt-3">

                                <button wire:click="orderRequest('{{ route('product', $product->slug) }}')"
                                    class="flex-1 py-1.5 text-xs font-semibold text-white bg-primary-500 rounded-lg">
                                    Request Order
                                </button>

                                <a href="/product/{{ $product->slug }}"
                                    class="flex-1 py-1.5 text-xs text-center text-primary-500 bg-primary-50 rounded-lg">
                                    Details
                                </a>

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

                {{-- PAGINATION --}}
                {{-- PAGINATION --}}
                <div class="mt-8 **:text-gray-700! **:bg-transparent! [&_[aria-current=page]_span]:bg-primary-500! [&_[aria-current=page]_span]:text-white! [&_[aria-current=page]_span]:border-primary-500!"
                    style="color-scheme: light;">
                    {{ $this->products()->links() }}
                </div>
                @else

                {{-- EMPTY --}}
                <div class="text-center py-20 text-gray-400">
                    No products found
                </div>

                @endif

            </div>

        </div>

    </div>

</div>
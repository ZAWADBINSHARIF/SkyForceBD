<?php

use App\Traits\WithSslCommerz;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    use WithSslCommerz;

    public float $amount = 65;

    public int $step = 1;
    public int $totalSteps = 3;

    public bool $showPaymentModal = false;
    public string $selectedMethod = '';

    #[Url]
    public ?string $product_link = null;

    // // Step 1
    public array $products = [
        //
    ];

    public string $customerName = '';
    public string $phoneNumber = '';
    public string $additionalNote = '';
    public string $email = 'info@skyforcebd.com';

    public ?string $fullAddress = null;
    public string $product_name = 'Order Request';
    public string $product_category = 'inquiry';
    public string $product_profile = 'non-physical-goods';

    public bool   $shipping_method = false;

    // Step 2
    public bool $agreedToTerms = false;

    public string $youtubeUrl = 'https://www.youtube.com/watch?v=txVwnBV4DEY';

    public function mount()
    {
        $product_link = $this->product_link ? $this->product_link : null;

        if ($product_link != null) {
            $this->products[] = [
                'link' => $product_link,
                'quantity' => 1,
            ];
        } else {
            $this->products[] = [
                'link' => "",
                'quantity' => 0,
            ];
        }

        $this->setPostData();
    }

    protected function rules(): array
    {
        return [
            'products'             => 'required|array|min:1',
            'products.*.link'      => 'required|url',
            'products.*.quantity'  => 'required|integer|min:1',
            'customerName'         => 'required|string|min:3',
            'phoneNumber'          => 'required|string|regex:/^[0-9+\-\s]{7,15}$/',
            'additionalNote'       => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'products.*.link.required' => 'Product link is required.',
            'products.*.link.url'      => 'Please enter a valid URL.',
            'products.*.quantity.required' => 'Quantity is required.',
            'products.*.quantity.min'  => 'Quantity must be at least 1.',
            'phoneNumber.required'     => 'Phone number is required.',
            'phoneNumber.regex'        => 'Please enter a valid phone number.',
        ];
    }

    public function getVideoIdProperty(): ?string
    {
        preg_match(
            '/(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $this->youtubeUrl,
            $matches
        );
        return $matches[1] ?? null;
    }

    public function addProduct(): void
    {
        $this->products[] = ['link' => '', 'quantity' => 1];
    }

    public function removeProduct(int $index): void
    {
        if (count($this->products) > 1) {
            array_splice($this->products, $index, 1);
            $this->products = array_values($this->products);
        }
    }

    public function goToStep2(): void
    {
        $this->validate($this->rules(), $this->messages());

        $this->step = 2;
    }

    public function goToStep3(): void
    {
        if (! $this->agreedToTerms) {
            $this->addError('agreedToTerms', 'You must agree to the terms and conditions.');
            return;
        }

        $this->step = 3;
    }

    public function goBack(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function handlePaymentSslCommerz(): void
    {
        $this->setPostData();
        $this->paymentForOrderRequest();
    }


    public function openModal(): void
    {
        $this->showPaymentModal = true;
        $this->selectedMethod   = '';
    }

    public function closeModal(): void
    {
        $this->showPaymentModal = false;
        $this->selectedMethod   = '';
    }

    public function selectMethod(string $method): void
    {
        $this->selectedMethod = $method;
    }

    public function proceedPayment(): void
    {
        if (empty($this->selectedMethod)) {
            return;
        }

        // TODO: handle each method
        match ($this->selectedMethod) {
            'bkash'       => $this->redirect(route('payment.bkash')),
            'sslcommerz'  => $this->handlePaymentSslCommerz(),
            'bank'        => $this->redirect(route('payment.bank')),
            default       => null,
        };
    }
};
?>

<div class="min-h-screen bg-gray-50 py-8 px-4">

    {{-- Header --}}
    <div class="max-w-lg mx-auto mb-6">
        <h1 class="text-xl font-bold text-gray-900">Request an Order</h1>
        <p class="text-sm text-gray-500 mt-0.5">Fill in the details below to place your request</p>
    </div>

    {{-- Step Indicator --}}
    <div class="max-w-lg mx-auto mb-6">
        <div class="flex items-center gap-0">
            @foreach ([1 => 'Details', 2 => 'Terms', 3 => 'Payment'] as $s => $label)
            <div class="flex items-center {{ $s < $totalSteps ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div @class([ 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300'
                        , 'bg-primary-500 text-white shadow-sm shadow-primary-200'=> $step >= $s,
                        'bg-white border-2 border-gray-200 text-gray-400' => $step < $s, ])>
                            @if ($step > $s)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            @else
                            {{ $s }}
                            @endif
                    </div>
                    <span @class([ 'text-[10px] font-medium mt-1' , 'text-primary-500'=> $step >= $s,
                        'text-gray-400' => $step < $s, ])>{{ $label }}</span>
                </div>
                @if ($s < $totalSteps) <div @class([ 'flex-1 h-0.5 mx-2 mb-4 transition-all duration-300'
                    , 'bg-primary-400'=> $step > $s,
                    'bg-gray-200' => $step <= $s, ])>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- Flash Success --}}
@if (session('success'))
<div
    class="max-w-lg mx-auto mb-4 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-sm font-medium">
    ✓ {{ session('success') }}
</div>
@endif

{{-- Card --}}
<div class="max-w-lg mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    {{-- ───────────── STEP 1 ───────────── --}}
    @if ($step === 1)
    <div class="px-5 py-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-6 h-6 rounded-full bg-primary-50 flex items-center justify-center">
                <span class="text-xs font-bold text-primary-500">1</span>
            </div>
            <h2 class="text-base font-bold text-gray-900">Order Details</h2>
        </div>

        {{-- Product Items --}}
        <div class="space-y-3 mb-4">
            @foreach ($products as $i => $product)
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Product {{ $i + 1
                        }}</span>
                    @if (count($products) > 1)
                    <button wire:click="removeProduct({{ $i }})" type="button"
                        class="w-5 h-5 rounded-full bg-red-50 hover:bg-red-100 flex items-center justify-center transition-colors">
                        <svg class="w-3 h-3 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endif
                </div>

                <div class="space-y-2">
                    <div>
                        <input type="url" wire:model.blur="products.{{ $i }}.link"
                            placeholder="https://example.com/product"
                            class="w-full px-3 py-2 text-sm bg-white border rounded-lg outline-none transition-colors
                                            placeholder:text-gray-300
                                            {{ $errors->has('products.'.$i.'.link') ? 'border-red-300 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
                        @error('products.'.$i.'.link')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 shrink-0">Qty</span>
                        <input type="number" wire:model.blur="products.{{ $i }}.quantity" min="1" placeholder="1"
                            class="w-24 px-3 py-2 text-sm bg-white border rounded-lg outline-none transition-colors
                                            {{ $errors->has('products.'.$i.'.quantity') ? 'border-red-300 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
                        @error('products.'.$i.'.quantity')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button wire:click="addProduct" type="button" class="w-full py-2 rounded-xl border border-dashed border-primary-300 text-primary-500 text-sm font-medium
                        hover:bg-primary-50 transition-colors flex items-center justify-center gap-1.5 mb-5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Add Another Product
        </button>

        <div class="border-t border-gray-100 pt-4 space-y-3">
            {{-- Customer Name --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Customer Name <span class="text-red-400">*</span>
                </label>
                <input type="text" wire:model.blur="customerName" placeholder="Your full name"
                    class="w-full px-3 py-2 text-sm border rounded-lg outline-none transition-colors placeholder:text-gray-300
                                {{ $errors->has('customerName') ? 'border-red-300 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
                @error('customerName')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Phone Number <span class="text-red-400">*</span>
                </label>
                <input type="tel" wire:model.blur="phoneNumber" placeholder="+880 1XXXXXXXXX"
                    class="w-full px-3 py-2 text-sm border rounded-lg outline-none transition-colors placeholder:text-gray-300
                                {{ $errors->has('phoneNumber') ? 'border-red-300 focus:border-red-400' : 'border-gray-200 focus:border-primary-400' }}" />
                @error('phoneNumber')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Note --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Additional Note <span
                        class="text-gray-300">(optional)</span></label>
                <textarea wire:model.blur="additionalNote" rows="3"
                    placeholder="Any special instructions, size, color, variant..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg outline-none transition-colors
                                focus:border-primary-400 placeholder:text-gray-300 resize-none"></textarea>
            </div>
        </div>
    </div>

    <div class="px-5 pb-5">
        <button wire:click="goToStep2" type="button" class="btn-primary w-full py-2.5 rounded-xl font-semibold">
            Continue to Terms
            <svg class="w-4 h-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
    @endif

    {{-- ───────────── STEP 2 ───────────── --}}
    @if ($step === 2)
    <div class="px-5 py-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-6 h-6 rounded-full bg-primary-50 flex items-center justify-center">
                <span class="text-xs font-bold text-primary-500">2</span>
            </div>
            <h2 class="text-base font-bold text-gray-900">Terms & Conditions</h2>
        </div>

        <div
            class="bg-gray-50 rounded-xl border border-gray-100 p-4 max-h-72 overflow-y-auto text-sm text-gray-600 space-y-3 leading-relaxed mb-4">
            <div class="prose text-sm text-gray-700">

                <h3>অর্ডার রিকোয়েস্ট করার পরও কেন কোন রেসপন্স পাচ্ছি না?</h3>

                <ul>
                    <li>১৮+ কোন প্রোডাক্ট হলে।</li>
                    <li>কোন পণ্যের মূল্য ১০০০ টাকার এর নিচে এমন কোন প্রোডাক্ট দিয়ে অর্ডার রিকোয়েস্ট করলে।</li>
                    <li>অ্যালকোহল জাতীয় কোন পণ্য হলে।</li>
                    <li>৩০০ গ্রামের নিচের সাইজের ড্রোন।</li>
                    <li>
                        গোল্ড, ওয়াকিটকি, মিনি হিডেন ক্যামেরা, হাতকড়া, বন্দুক, অথবা কোন সামরিক ফোর্সের পোশাক,
                        এ ধরনের কোন পণ্য অর্ডার রিকোয়েস্ট করলে কোন সময় রেসপন্স করা হবে না।
                    </li>
                </ul>

                <p>
                    এবং উল্লেখিত নিয়মাবলী না মেনে আপনি পরপর তিনবারের বেশি অর্ডার রিকোয়েস্ট করলে
                    আপনার নাম্বার অটোমেটিক আমাদের ওয়েবসাইট থেকে ডিলিটেড হয়ে যাবে স্প্যাম অর্ডার
                    রিকোয়েস্ট নাম্বার হিসেবে।
                </p>

                <p>
                    সম্মানিত গ্রাহক আপনি যদি মনে করেন আপনার অর্ডার রিকোয়েস্ট করা পণ্যটি উল্লেখিত
                    সকল নিয়মাবলী মেনে চলছে, তাহলে অর্ডার রিকোয়েস্ট সাবমিট বাটনে ক্লিক করুন।
                </p>

                <h3>
                    সম্মানিত গ্রাহক আপনি অর্ডার রিকোয়েস্ট দেয়ার পর উল্লেখিত জিনিসগুলো আমাদের
                    পারচেজ ম্যানেজার চেক করে দেখে আপনাকে ফোন কল এর রিপোর্ট দিবেন।
                </h3>

                <ol>
                    <li>পণ্যটি কোন স্ক্যাম স্টোর থেকে অর্ডার করা হয়েছে কিনা।</li>

                    <li>
                        প্ল্যাটফর্মে পোস্টকৃত পণ্য এবং সাপ্লায়ারের পণ্য একই কিনা।
                    </li>

                    <li>
                        সাপ্লায়ার চায়নার ভিতর ডেলিভারি করবে কিনা। যদি ডেলিভারি না করে থাকে তাহলে
                        একই প্রোডাক্টের ভিন্ন একজন সাপ্লায়ার এর কন্টাক্ট এড্রেস আপনাকে দিয়ে দেয়া হবে।
                    </li>

                    <li>
                        প্রোডাক্টের নির্দিষ্ট খরচ কত পড়বে –
                        <ul>
                            <li>চায়নার ভিতরে ডেলিভারি</li>
                            <li>রিকোয়েস্ট ফি</li>
                            <li>শিপিং চার্জ</li>
                        </ul>
                    </li>
                </ol>

                <p>
                    কোন পণ্য যদি আপনার নির্দিষ্ট লিংক থেকে না পাওয়া যায় অথবা সাপ্লায়ারের কাছে
                    স্টক আউট হয়ে যায়, অথবা সাপ্লায়ার অতি ধীর গতির রেসপন্স দেয়, তাহলে আমাদের
                    পারচেজ ম্যানেজার একই পণ্য অন্য সাপ্লায়ারের কাছ থেকে পারচেজ করতে বাধ্য থাকবেন।
                </p>

            </div>
        </div>

        {{-- Agreement checkbox --}}
        <label
            class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border transition-colors
                    {{ $errors->has('agreedToTerms') ? 'border-red-200 bg-red-50' : 'border-gray-100 hover:bg-gray-50' }}">
            <input type="checkbox" wire:model="agreedToTerms" class="mt-0.5 w-4 h-4 accent-primary-500 shrink-0" />
            <span class="text-sm text-gray-700">
                আমি <span class="text-primary-600 font-medium">শর্তাবলী</span> পড়েছি এবং তাতে সম্মত আছি। আমি বুঝতে পারছি যে যাচাইকরণ ফি ফেরতযোগ্য নয়।
            </span>
        </label>
        @error('agreedToTerms')
        <p class="text-xs text-red-500 mt-1.5 ml-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="px-5 pb-5 flex gap-3">
        <button wire:click="goBack" type="button" class="btn flex-1 py-2.5 rounded-xl font-semibold">
            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </button>
        <button wire:click="goToStep3" type="button" class="btn-primary flex-1 py-2.5 rounded-xl font-semibold">
            Agree & Continue
            <svg class="w-4 h-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
    @endif

    {{-- ───────────── STEP 3 ───────────── --}}
    @if ($step === 3)
    <div class="px-5 py-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-6 h-6 rounded-full bg-primary-50 flex items-center justify-center">
                <span class="text-xs font-bold text-primary-500">3</span>
            </div>
            <h2 class="text-base font-bold text-gray-900">Verification Payment</h2>
        </div>

        {{-- Amount card --}}
        <div class="bg-primary-50 border border-primary-100 rounded-xl p-4 mb-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-primary-600 font-medium mb-0.5">Order Verification Fee</p>
                <p class="text-2xl font-bold text-primary-600">৳ 65</p>
                <p class="text-xs text-primary-400 mt-0.5">One-time, non-refundable</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>

        {{-- How to pay --}}
        <div class="rounded-xl border border-gray-100 divide-y divide-gray-100 mb-4">
            <div class="px-4 py-3">
                <p class="text-xs text-gray-500 leading-relaxed">
                    Send <strong class="text-gray-700">৳65</strong> to any number above using
                    <strong class="text-gray-700">Send Money</strong>, then tap the button below.
                </p>
            </div>
        </div>

        {{-- Order summary mini --}}
        <div class="bg-gray-50 rounded-xl border border-gray-100 px-4 py-3 mb-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Order Summary</p>
            <div class="space-y-1">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Customer</span>
                    <span class="text-gray-800 font-medium">{{ $customerName }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Phone</span>
                    <span class="text-gray-800 font-medium">{{ $phoneNumber }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Products</span>
                    <span class="text-gray-800 font-medium">{{ count($products) }} item(s)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="px-5 pb-5 flex gap-3">
        <button wire:click="goBack" type="button" class="btn flex-1 py-2.5 rounded-xl font-semibold">
            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </button>
        <button wire:click="openModal" type="button"
            class="btn-primary flex-1 py-2.5 rounded-xl font-semibold flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Pay Now ৳65
        </button>
    </div>

    {{-- YouTube explainer --}}
    <div class="border-t border-gray-100 px-5 py-5">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-5 h-5 rounded bg-red-500 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 15.5l6-3.5-6-3.5v7z" />
                    <path fill-rule="evenodd"
                        d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.95C5.12 20 12 20 12 20s6.88 0 8.59-.47a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-gray-700">Why do we charge ৳65?</p>
        </div>
        <p class="text-xs text-gray-500 mb-3 leading-relaxed">
            Watch this short video to understand why we collect a verification fee and how it protects both you and us.
        </p>
        <div class="rounded-xl overflow-hidden border border-gray-100 aspect-video">
            <iframe src="https://www.youtube.com/embed/{{ $this->videoId }}?rel=0&modestbranding=1"
                title="Why we charge a verification fee" class="w-full h-full" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>
    </div>
    @endif

</div>

<p class="text-center text-xs text-gray-400 mt-4 mb-8">
    Need help? Contact us on WhatsApp
</p>


{{-- Modal --}}
@if ($showPaymentModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:click.self="closeModal">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeModal"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl z-10 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-sm font-bold text-gray-900">Select Payment Method</h2>
                <p class="text-xs text-gray-400 mt-0.5">Total: <span class="font-semibold text-gray-700">৳65</span></p>
            </div>
            <button wire:click="closeModal" type="button"
                class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Methods --}}
        <div class="px-5 py-4 space-y-3">

            {{-- bKash --}}
            <button wire:click="selectMethod('bkash')" type="button" class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl border-2 transition-all duration-150
                    {{ $selectedMethod === 'bkash'
                        ? 'border-pink-500 bg-pink-50'
                        : 'border-gray-100 bg-gray-50 hover:border-gray-200 hover:bg-white' }}">

                {{-- bKash logo mark --}}
                <div class="w-14 h-14 rounded-xl bg-[#E2136E] flex items-center justify-center shrink-0">
                    <span class="text-white font-black text-xs tracking-tight">bKash</span>
                </div>

                <div class="text-left flex-1">
                    <p class="text-sm font-semibold text-gray-800">bKash</p>
                    <p class="text-xs text-gray-400">Mobile banking · instant</p>
                </div>

                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors
                    {{ $selectedMethod === 'bkash' ? 'border-pink-500 bg-pink-500' : 'border-gray-300' }}">
                    @if ($selectedMethod === 'bkash')
                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    @endif
                </div>
            </button>

            {{-- SSLCommerz --}}
            <button wire:click="selectMethod('sslcommerz')" type="button" class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl border-2 transition-all duration-150
                    {{ $selectedMethod === 'sslcommerz'
                        ? 'border-primary-500 bg-primary-50'
                        : 'border-gray-100 bg-gray-50 hover:border-gray-200 hover:bg-white' }}">

                {{-- SSLCommerz logo mark --}}
                <div class="w-14 h-14 rounded-xl bg-[#0B5EA8] flex items-center justify-center shrink-0">
                    <span class="text-white font-black text-[9px] leading-tight text-center">SSL<br>Commerz</span>
                </div>

                <div class="text-left flex-1">
                    <p class="text-sm font-semibold text-gray-800">SSLCommerz</p>
                    <p class="text-xs text-gray-400">Card · online bank · mobile bank</p>
                    <div>
                        <img src="{{asset('images/sslcommerz-we-accept.png')}}" />
                    </div>
                </div>

                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-colors
                    {{ $selectedMethod === 'sslcommerz' ? 'border-primary-500 bg-primary-500' : 'border-gray-300' }}">
                    @if ($selectedMethod === 'sslcommerz')
                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    @endif
                </div>

            </button>
        </div>

        {{-- Footer --}}
        <div class="px-5 pb-5">
            <button wire:click="proceedPayment" type="button" @disabled(empty($selectedMethod)) class="w-full py-3 rounded-xl font-semibold text-sm transition-all duration-150 flex items-center justify-center gap-2
                    {{ !empty($selectedMethod)
                        ? 'btn-primary'
                        : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                <span wire:loading.remove wire:target="proceedPayment">
                    @if (!empty($selectedMethod))
                    Continue with
                    {{ match($selectedMethod) {
                    'bkash' => 'bKash',
                    'sslcommerz' => 'SSLCommerz',
                    'bank' => 'Bank Transfer',
                    default => ''
                    } }}
                    @else
                    Choose a method
                    @endif
                </span>
                <span wire:loading wire:target="proceedPayment" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Redirecting...
                </span>
            </button>

            <p class="text-center text-xs text-gray-400 mt-3">
                <svg class="w-3 h-3 inline-block mb-0.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Secured & encrypted payment
            </p>
        </div>

    </div>
</div>
@endif

</div>
@extends('layouts.app')

@section('title', 'Kasir (POS)')

@section('content')
    <div
        x-data="cashierPage()"
        x-cloak
        @keydown.window="handleGlobalKey($event)"
        class="space-y-5"
    >
        <!-- Floating Non-blocking Toast Notification -->
        <div
            x-show="toast.show"
            role="status"
            aria-live="polite"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-3"
            class="fixed top-5 right-5 z-50 max-w-sm rounded-xl p-3.5 shadow-2xl border flex items-start gap-3 bg-surface-1"
            :class="toast.type === 'error' ? 'border-danger/40' : 'border-primary/40'"
        >
            <div
                class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
                :class="toast.type === 'error' ? 'bg-danger/15 text-danger' : 'bg-primary/15 text-primary'"
            >
                <template x-if="toast.type === 'error'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </template>
                <template x-if="toast.type !== 'error'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </template>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-ink" x-text="toast.title || (toast.type === 'error' ? 'Pemberitahuan Kasir' : 'Berhasil')"></p>
                <p class="text-xs text-ink-subtle mt-0.5" x-text="toast.message"></p>
            </div>
            <button
                type="button"
                @click="toast.show = false"
                class="w-9 h-9 flex items-center justify-center text-ink-subtle hover:text-ink text-lg leading-none rounded-md focus-ring cursor-pointer"
                aria-label="Tutup notifikasi"
            >×</button>
        </div>

        <!-- Top Cashier Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink">Kasir Penjualan</h1>
                <p class="text-xs sm:text-sm text-ink-subtle mt-0.5">Workstation kasir cepat — pilih produk, kelola keranjang, dan proses pembayaran.</p>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('transaksi.index') }}"
                    class="inline-flex items-center gap-2 bg-surface-1 hover:bg-surface-2 border border-hairline text-ink font-semibold text-xs sm:text-sm rounded-xl px-4 py-2.5 min-h-[42px] transition-colors shadow-xs focus-ring cursor-pointer"
                >
                    <svg class="w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                        <line x1="2" y1="10" x2="22" y2="10"></line>
                    </svg>
                    <span>Riwayat Transaksi</span>
                </a>
            </div>
        </div>

        <!-- Main Layout (Left: Categories + Products, Right: Current Orders Cart) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Main Area (Products & Categories) -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-4">
                <!-- 1. Product Category Navigation -->
                <div class="bg-surface-1 border border-hairline rounded-2xl p-4 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm sm:text-base font-bold text-ink">Kategori Produk</h2>
                            <span class="text-xs text-ink-subtle bg-surface-2 px-2.5 py-0.5 rounded-full border border-hairline" x-text="categories.length + ' Kategori'"></span>
                        </div>
                        <!-- Carousel Arrow Controls -->
                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                @click="scrollCategories(-1)"
                                class="w-9 h-9 rounded-xl border border-hairline bg-surface-2 hover:bg-surface-3 flex items-center justify-center text-ink-muted hover:text-ink transition-colors focus-ring cursor-pointer"
                                title="Sebelumnya"
                                aria-label="Geser kategori ke kiri"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click="scrollCategories(1)"
                                class="w-9 h-9 rounded-xl border border-hairline bg-surface-2 hover:bg-surface-3 flex items-center justify-center text-ink-muted hover:text-ink transition-colors focus-ring cursor-pointer"
                                title="Berikutnya"
                                aria-label="Geser kategori ke kanan"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Category Pills List (Derived from actual catalog, Horizontal Scrollable) -->
                    <div
                        x-ref="categoryTrack"
                        class="flex items-center gap-2.5 overflow-x-auto px-1 py-1 no-scrollbar scroll-smooth"
                    >
                        <template x-for="cat in categories" :key="cat.id">
                            <button
                                type="button"
                                @click="selectedCategory = cat.id"
                                :aria-pressed="selectedCategory === cat.id"
                                :class="selectedCategory === cat.id
                                    ? 'bg-primary/10 border-primary shadow-xs ring-1 ring-primary'
                                    : 'bg-surface-2/80 hover:bg-surface-2 border-hairline hover:border-hairline-strong'"
                                class="shrink-0 flex items-center gap-2.5 px-3 py-2 rounded-xl border transition-all duration-150 focus-ring cursor-pointer text-left min-w-[130px] min-h-[44px]"
                            >
                                <div
                                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                    :class="selectedCategory === cat.id ? 'bg-primary/20 text-primary' : 'bg-surface-1 text-ink-subtle border border-hairline'"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span
                                        class="text-xs font-bold block truncate"
                                        :class="selectedCategory === cat.id ? 'text-primary' : 'text-ink'"
                                        x-text="cat.name"
                                    ></span>
                                    <span class="text-xs text-ink-subtle block" x-text="cat.count + ' item'"></span>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- 2. Product Catalog (POS Dense Grid) -->
                <div class="bg-surface-1 border border-hairline rounded-2xl p-4 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3.5">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base sm:text-lg font-bold text-ink">Katalog Produk</h2>
                            <span class="text-xs text-ink-subtle" x-text="filteredProducts.length + ' Produk'"></span>
                        </div>

                        <!-- Product Search Bar -->
                        <div class="relative w-full sm:w-64">
                            <svg class="w-4 h-4 text-ink-subtle absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                            <input
                                type="text"
                                x-ref="searchInput"
                                x-model="search"
                                placeholder="Cari nama produk... (/)"
                                aria-label="Cari produk"
                                class="w-full min-h-[40px] bg-surface-2 border border-hairline rounded-xl pl-9 pr-8 py-2.5 text-xs text-ink placeholder:text-ink-subtle focus:outline-none focus:border-primary transition-all"
                            >
                            <button
                                x-show="search"
                                @click="search = ''; $refs.searchInput.focus()"
                                type="button"
                                aria-label="Hapus pencarian"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center text-ink-subtle hover:text-ink text-sm focus-ring rounded-md cursor-pointer"
                            >×</button>
                        </div>
                    </div>

                    <!-- POS Dense Grid: 2 cols (mobile), 3 cols (tablet), 4 cols (desktop) -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <div
                                @click="addProduct(product)"
                                :class="[
                                    isProductInCart(product.id) ? 'border-primary ring-1 ring-primary bg-primary/5 shadow-xs' : 'border-hairline bg-surface-2/40 hover:bg-surface-2 hover:border-hairline-strong',
                                    product.track_stock && product.stock <= 0 ? 'opacity-50 cursor-not-allowed hover:bg-surface-2/40' : 'cursor-pointer'
                                ]"
                                class="border rounded-xl p-2.5 flex flex-col justify-between transition-all duration-150 focus-ring group relative overflow-hidden"
                            >
                                <!-- Top Badges & Image Preview -->
                                <div class="relative h-24 sm:h-28 w-full rounded-lg bg-surface-1 overflow-hidden flex items-center justify-center p-2 border border-hairline/60">
                                    <img
                                        :src="product.image_url"
                                        :alt="product.name"
                                        class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-200 drop-shadow-xs"
                                        onerror="this.src='/storage/products/isi-ulang-air-galon.svg'"
                                    >

                                    <!-- Unit Badge -->
                                    <span class="absolute top-1.5 left-1.5 text-xs font-semibold uppercase bg-surface-2/95 text-ink-subtle px-1.5 py-0.5 rounded border border-hairline/60" x-text="product.unit"></span>

                                    <!-- Selected Overlay in Cart -->
                                    <template x-if="isProductInCart(product.id)">
                                        <div class="absolute inset-0 bg-primary/20 backdrop-blur-[0.5px] flex items-center justify-center">
                                            <span class="bg-surface-1 border border-primary/40 text-primary font-bold text-xs px-2.5 py-1 rounded-full shadow-md flex items-center gap-1 font-mono">
                                                <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                <span x-text="cartQty(product.id)"></span>
                                            </span>
                                        </div>
                                    </template>

                                    <!-- Out of Stock Badge -->
                                    <template x-if="product.track_stock && product.stock <= 0">
                                        <div class="absolute inset-0 bg-black/60 backdrop-blur-[1px] flex items-center justify-center">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-danger text-white shadow-xs">
                                                Habis
                                            </span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Product Info (Dense layout, no marketing fluff) -->
                                <div class="mt-2.5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-xs sm:text-sm font-bold text-ink group-hover:text-primary transition-colors line-clamp-2 leading-tight min-h-[2rem]" x-text="product.name"></h3>
                                    </div>

                                    <div class="mt-2 pt-2 border-t border-hairline/60 flex items-center justify-between">
                                        <div>
                                            <span class="text-xs sm:text-sm font-mono font-black text-ink block tracking-tight" x-text="formatMoney(product.price)"></span>
                                            <template x-if="product.track_stock">
                                                <span class="text-xs text-ink-subtle block" x-text="'Stok: ' + product.stock"></span>
                                            </template>
                                            <template x-if="!product.track_stock">
                                                <span class="text-xs text-ink-subtle flex items-center gap-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-success shrink-0"></span>
                                                    Tersedia
                                                </span>
                                            </template>
                                        </div>

                                        <!-- Interactive Add Button with Min 40px Touch Target -->
                                        <button
                                            type="button"
                                            :disabled="product.track_stock && product.stock <= 0"
                                            :aria-label="'Tambah ' + product.name + ' ke keranjang'"
                                            class="min-w-[40px] min-h-[40px] w-10 h-10 rounded-lg bg-surface-1 group-hover:bg-primary group-hover:text-on-primary border border-hairline group-hover:border-primary text-ink-muted flex items-center justify-center transition-all duration-150 shrink-0 focus-ring cursor-pointer shadow-2xs"
                                            title="Tambah ke Keranjang"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <template x-if="filteredProducts.length === 0">
                            <div class="col-span-full py-12 text-center text-ink-subtle text-xs sm:text-sm">
                                <svg class="w-10 h-10 mx-auto text-ink-subtle/50 mb-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </svg>
                                <p class="font-medium text-ink">Tidak ada produk yang cocok</p>
                                <p class="text-xs text-ink-subtle mt-1">Coba gunakan kata kunci lain atau pilih kategori Semua.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Current Orders Cart & Natural Tender Pipeline -->
            <div class="lg:col-span-5 xl:col-span-4 sticky top-6">
                <div class="bg-surface-1 border border-hairline rounded-2xl p-4 sm:p-5 lg:pb-0 shadow-xs flex flex-col lg:max-h-[calc(100vh_-_176px)] lg:overflow-y-auto scrollbar-thin">
                    <!-- Cart Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-hairline shrink-0">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-ink">Pesanan Saat Ini</h2>
                            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20" x-text="cartTotalItems + ' item'"></span>
                        </div>

                        <!-- Kosongkan Keranjang -->
                        <button
                            x-show="cartItems.length > 0"
                            @click="clearCart()"
                            type="button"
                            class="text-xs text-ink-subtle hover:text-danger flex items-center gap-1.5 px-2.5 min-h-[36px] rounded-lg hover:bg-danger/10 transition-colors focus-ring cursor-pointer"
                            title="Kosongkan Keranjang"
                            aria-label="Kosongkan keranjang"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            <span>Kosongkan</span>
                        </button>
                    </div>

                    <!-- Cart Items List -->
                    <div class="overflow-y-auto space-y-2 py-3 max-h-[280px] pr-1 scrollbar-thin shrink-0">
                        <template x-for="(item, index) in cartItems" :key="item.product_id">
                            <div class="p-2 rounded-xl bg-surface-2/70 border border-hairline hover:border-hairline-strong flex items-center gap-2.5 transition-colors">
                                <!-- Thumbnail Image -->
                                <div class="w-10 h-10 rounded-lg bg-surface-1 border border-hairline p-1 shrink-0 flex items-center justify-center overflow-hidden">
                                    <img
                                        :src="item.image_url"
                                        :alt="item.name"
                                        class="w-full h-full object-contain"
                                        onerror="this.src='/storage/products/isi-ulang-air-galon.svg'"
                                    >
                                </div>

                                <!-- Item Info -->
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-bold text-ink truncate" x-text="item.name"></h4>
                                    <p class="text-xs font-mono text-ink-subtle" x-text="formatMoney(item.price)"></p>
                                </div>

                                <!-- Stepper Buttons with Min 40px Touch Target -->
                                <div class="flex items-center gap-1 shrink-0 bg-surface-1 border border-hairline rounded-lg p-0.5">
                                    <button
                                        type="button"
                                        @click="updateQty(index, item.qty - 1)"
                                        class="min-w-[40px] min-h-[40px] w-10 h-10 rounded-md hover:bg-surface-3 text-ink flex items-center justify-center text-base font-bold transition-colors focus-ring cursor-pointer"
                                        title="Kurangi"
                                        aria-label="Kurangi jumlah"
                                    >−</button>
                                    <span class="w-7 text-center text-xs font-mono font-bold text-ink" x-text="item.qty"></span>
                                    <button
                                        type="button"
                                        @click="updateQty(index, item.qty + 1)"
                                        class="min-w-[40px] min-h-[40px] w-10 h-10 rounded-md hover:bg-surface-3 text-ink flex items-center justify-center text-base font-bold transition-colors focus-ring cursor-pointer"
                                        title="Tambah"
                                        aria-label="Tambah jumlah"
                                    >+</button>
                                </div>

                                <!-- Subtotal & Remove -->
                                <div class="text-right shrink-0 min-w-[70px]">
                                    <span class="text-xs font-mono font-bold text-ink block" x-text="formatMoney(item.price * item.qty)"></span>
                                    <button
                                        type="button"
                                        @click="removeItem(index)"
                                        class="text-xs text-danger hover:underline p-1.5 -m-1 rounded focus-ring cursor-pointer"
                                    >Hapus</button>
                                </div>
                            </div>
                        </template>

                        <!-- Empty Cart State -->
                        <template x-if="cartItems.length === 0">
                            <div class="flex flex-col items-center justify-center py-8 text-center text-ink-subtle">
                                <div class="w-12 h-12 rounded-xl bg-surface-2 border border-hairline flex items-center justify-center text-ink-subtle mb-2.5">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <circle cx="9" cy="21" r="1"></circle>
                                        <circle cx="20" cy="21" r="1"></circle>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-ink">Keranjang Masih Kosong</p>
                                <p class="text-xs text-ink-subtle mt-0.5 max-w-[200px]">Pilih produk dari katalog untuk menambah pesanan.</p>
                            </div>
                        </template>
                    </div>

                    <!-- NATURAL TENDER & CHECKOUT SEQUENCE -->
                    <div class="border-t border-hairline pt-3 space-y-3 mt-1 shrink-0">
                        <!-- STEP 1: BILL SUMMARY (Single Prominent Total) -->
                        <div class="px-3.5 py-2.5 rounded-xl bg-surface-2/80 border border-hairline flex items-end justify-between gap-3">
                            <div class="pb-0.5">
                                <span class="text-xs uppercase tracking-wider font-bold text-ink-subtle block">Total Tagihan</span>
                                <span class="text-xs text-ink-subtle mt-0.5 block" x-text="cartTotalItems + ' barang'"></span>
                            </div>
                            <span class="text-2xl sm:text-3xl font-mono font-black text-ink tracking-tight leading-none" x-text="formatMoney(total)"></span>
                        </div>

                        <!-- STEP 2: PAYMENT METHOD -->
                        <div>
                            <label class="text-xs font-bold uppercase tracking-wider text-ink-subtle block mb-1.5">Metode Pembayaran</label>
                            <div class="grid grid-cols-2 gap-1.5 bg-surface-2 p-1 rounded-xl border border-hairline">
                                <button
                                    type="button"
                                    @click="setPaymentMethod('tunai')"
                                    :aria-pressed="paymentMethod === 'tunai'"
                                    :class="paymentMethod === 'tunai' ? 'bg-surface-1 text-primary shadow-xs font-bold' : 'text-ink-subtle hover:text-ink font-medium'"
                                    class="py-2 text-xs rounded-lg transition-all text-center focus-ring cursor-pointer min-h-[40px] flex items-center justify-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                                        <circle cx="12" cy="12" r="2"></circle>
                                    </svg>
                                    <span>Tunai</span>
                                </button>
                                <button
                                    type="button"
                                    @click="setPaymentMethod('qris')"
                                    :aria-pressed="paymentMethod === 'qris'"
                                    :class="paymentMethod === 'qris' ? 'bg-surface-1 text-primary shadow-xs font-bold' : 'text-ink-subtle hover:text-ink font-medium'"
                                    class="py-2 text-xs rounded-lg transition-all text-center focus-ring cursor-pointer min-h-[40px] flex items-center justify-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                    </svg>
                                    <span>QRIS</span>
                                </button>
                            </div>
                        </div>

                        <!-- STEP 3: CASH TENDER & HIGH-CONTRAST KEMBALIAN (When Tunai) -->
                        <template x-if="paymentMethod === 'tunai'">
                            <div class="space-y-3 bg-surface-2/60 p-3 rounded-xl border border-hairline">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-xs font-bold text-ink-subtle" for="uang-diterima">Uang Diterima</label>
                                    <button
                                        type="button"
                                        @click="paidAmount = total"
                                        class="bg-surface-1 hover:bg-surface-3 border border-hairline text-primary hover:text-primary-hover text-xs font-bold px-3 min-h-[36px] rounded-lg transition-colors focus-ring cursor-pointer"
                                    >Uang Pas</button>
                                </div>

                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-mono font-bold text-ink-subtle">Rp</span>
                                    <input
                                        id="uang-diterima"
                                        type="number"
                                        inputmode="numeric"
                                        x-ref="cashInput"
                                        x-model.number="paidAmount"
                                        min="0"
                                        placeholder="0"
                                        aria-label="Nominal uang diterima"
                                        class="w-full min-h-[44px] bg-surface-1 border border-hairline rounded-xl pl-9 pr-3 py-2.5 text-base font-mono font-bold text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none [-moz-appearance:textfield]"
                                    >
                                </div>

                                <!-- Smart Denomination Pills -->
                                <div class="grid grid-cols-4 gap-1.5">
                                    <template x-for="amt in quickCashOptions" :key="amt">
                                        <button
                                            type="button"
                                            @click="paidAmount = amt"
                                            :class="paidAmount === amt ? 'bg-primary text-on-primary font-bold' : 'bg-surface-1 hover:bg-surface-3 text-ink font-medium'"
                                            class="py-2 min-h-[40px] text-xs rounded-lg border border-hairline transition-all focus-ring cursor-pointer font-mono text-center"
                                            x-text="formatShortMoney(amt)"
                                        ></button>
                                    </template>
                                </div>

                                <!-- Prominent High-Contrast Kembalian Callout -->
                                <div
                                    class="p-2.5 rounded-xl border transition-all"
                                    :class="paidAmount >= total ? 'bg-success/10 border-success/40' : 'bg-danger/10 border-danger/40'"
                                    role="status"
                                    aria-live="polite"
                                >
                                    <div class="flex items-center justify-between text-xs font-semibold mb-1 text-ink-subtle">
                                        <span x-text="paidAmount >= total ? 'UANG KEMBALIAN' : 'SISA KURANG BAYAR'"></span>
                                        <span x-text="paidAmount >= total ? 'Kembalikan ke pelanggan' : 'Belum lunas'"></span>
                                    </div>
                                    <div
                                        class="text-2xl sm:text-3xl font-mono font-black tracking-tight leading-none"
                                        :class="paidAmount >= total ? 'text-success' : 'text-danger'"
                                        x-text="paidAmount >= total ? formatMoney(change) : '- ' + formatMoney(total - paidAmount)"
                                    ></div>
                                </div>
                            </div>
                        </template>

                        <!-- STEP 3B: QRIS GUIDANCE CARD -->
                        <template x-if="paymentMethod === 'qris'">
                            <div class="space-y-2 bg-surface-2/60 p-3.5 rounded-xl border border-hairline">
                                <div class="flex items-center gap-2 text-xs font-bold text-ink">
                                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                    </svg>
                                    <span>Pembayaran QRIS Statis</span>
                                </div>
                                <p class="text-xs text-ink-subtle leading-relaxed">
                                    Minta pelanggan memindai kode QRIS toko senilai <span class="font-mono font-bold text-ink" x-text="formatMoney(total)"></span>. Pastikan notifikasi penerimaan dana muncul sebelum klik bayar.
                                </p>
                                <button
                                    type="button"
                                    @click="qrisVisible = true"
                                    class="w-full bg-surface-1 hover:bg-surface-3 border border-primary text-ink font-bold text-xs rounded-lg min-h-[40px] flex items-center justify-center gap-2 transition-colors focus-ring cursor-pointer"
                                >
                                    <span class="text-secondary">Tampilkan Kode QRIS</span>
                                </button>
                            </div>
                        </template>

                        <!-- STEP 4: NOTES (Optional) -->
                        <div>
                            <label for="catatan-transaksi" class="sr-only">Catatan transaksi</label>
                            <input
                                id="catatan-transaksi"
                                type="text"
                                x-model="note"
                                placeholder="Catatan transaksi (opsional, nama pembeli / alamat)..."
                                class="w-full min-h-[40px] bg-surface-2 border border-hairline rounded-xl px-3 py-2.5 text-xs text-ink placeholder:text-ink-subtle focus:outline-none focus:border-primary"
                            >
                        </div>

                        <!-- STEP 5: FINAL CHECKOUT ACTION (pinned to panel bottom on desktop) -->
                        <div class="mt-3 lg:sticky lg:bottom-0 lg:z-10 lg:bg-surface-1 lg:pt-2 lg:pb-4 sm:lg:pb-5 lg:shadow-[0_-10px_16px_-12px_rgba(0,0,0,0.55)]">
                            <button
                                type="button"
                                @click="saveTransaction()"
                                :disabled="cartItems.length === 0 || loading || (paymentMethod === 'tunai' && paidAmount < total)"
                                class="w-full bg-primary hover:bg-primary-hover active:bg-primary-focus disabled:bg-surface-2 disabled:text-ink-subtle disabled:cursor-not-allowed text-on-primary font-bold text-sm rounded-xl py-3.5 min-h-[48px] shadow-sm transition-all duration-150 focus-ring flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <template x-if="loading">
                                    <svg class="animate-spin h-4 w-4 text-on-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </template>
                                <template x-if="!loading">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </template>
                                <span x-text="loading ? 'Memproses Transaksi...' : (cartItems.length === 0 ? 'Keranjang Kosong' : 'Selesaikan Pembayaran (' + formatMoney(total) + ')')"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Receipt Modal (Printable) -->
        <div
            x-show="receiptVisible"
            x-cloak
            class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
            @keydown.escape.window="receiptVisible = false"
            x-effect="if (receiptVisible) $nextTick(() => $refs.receiptCard?.focus())"
        >
            <div class="absolute inset-0" @click="receiptVisible = false" aria-hidden="true"></div>
            <div
                x-ref="receiptCard"
                tabindex="-1"
                role="dialog"
                aria-modal="true"
                aria-label="Struk pembayaran"
                class="relative bg-white text-black rounded-2xl p-6 w-full max-w-sm shadow-2xl max-h-[90vh] overflow-y-auto print-area focus:outline-none"
            >
                <div class="flex items-center justify-between border-b border-black/15 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-black text-white flex items-center justify-center font-bold text-xs">
                            SU
                        </div>
                        <div>
                            <span class="font-bold text-base tracking-tight block">Sumber Urip</span>
                            <span class="text-xs text-black/60 block -mt-0.5">Depo Air & POS Hub</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-black/70 bg-black/5 px-2 py-0.5 rounded">STRUK RESMI</span>
                        <button
                            type="button"
                            @click="receiptVisible = false"
                            class="no-print w-9 h-9 rounded-lg hover:bg-black/10 flex items-center justify-center text-black/60 hover:text-black cursor-pointer text-base leading-none"
                            title="Tutup (Esc)"
                            aria-label="Tutup struk"
                        >×</button>
                    </div>
                </div>

                <div class="text-xs text-black/70 mb-3 space-y-0.5">
                    <p>No: <span class="font-mono font-bold text-black" x-text="receipt.invoice_number"></span></p>
                    <p>Tanggal: <span x-text="formatDate(receipt.created_at)"></span></p>
                    <p>Kasir: <span class="font-medium text-black">{{ auth()->user()->name ?? 'Administrator' }}</span></p>
                </div>

                <table class="w-full text-left mb-3 text-xs">
                    <thead class="text-black/60 border-b border-black/10">
                        <tr>
                            <th class="pb-1 font-semibold">Item</th>
                            <th class="pb-1 text-right font-semibold">Qty</th>
                            <th class="pb-1 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in receipt.items" :key="item.id">
                            <tr class="border-b border-black/5">
                                <td class="py-1 font-medium" x-text="item.product_name"></td>
                                <td class="py-1 text-right" x-text="item.quantity"></td>
                                <td class="py-1 text-right font-semibold" x-text="formatMoney(item.subtotal)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="border-t border-black/15 pt-2 space-y-1 text-xs">
                    <div class="flex justify-between">
                        <span>Total Tagihan</span>
                        <span class="font-bold text-sm" x-text="formatMoney(receipt.total)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Nominal Bayar</span>
                        <span class="font-bold" x-text="formatMoney(receipt.paid_amount)"></span>
                    </div>
                    <div class="flex justify-between text-black/70">
                        <span>Kembalian</span>
                        <span class="font-bold text-black" x-text="formatMoney(receipt.change_amount)"></span>
                    </div>
                </div>

                <template x-if="receipt.note">
                    <div class="text-xs mt-3 p-2 rounded-lg border border-black/10 bg-black/5">
                        <span class="font-medium">Catatan:</span> <span x-text="receipt.note"></span>
                    </div>
                </template>

                <p class="text-center text-xs text-black/50 mt-4 mb-4">Terima kasih telah berbelanja di Sumber Urip</p>

                <div class="flex gap-2 no-print">
                    <button
                        type="button"
                        @click="printReceipt()"
                        class="flex-1 bg-black hover:bg-black/80 text-white font-bold text-xs rounded-xl py-3 min-h-[44px] transition-colors cursor-pointer"
                    >Cetak Struk</button>
                    <a
                        :href="receipt.url"
                        class="flex-1 bg-white hover:bg-black/5 border border-black/20 text-black font-bold text-xs rounded-xl py-3 min-h-[44px] flex items-center justify-center transition-colors cursor-pointer"
                    >Detail Transaksi</a>
                </div>
            </div>
            </div>
        </div>

        <!-- QRIS QR Code Modal -->
        <div
            x-show="qrisVisible"
            x-cloak
            class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
            @keydown.escape.window="qrisVisible = false"
            x-effect="if (qrisVisible) $nextTick(() => $refs.qrisCard?.focus())"
        >
            <div class="absolute inset-0" @click="qrisVisible = false" aria-hidden="true"></div>
            <div
                x-ref="qrisCard"
                tabindex="-1"
                role="dialog"
                aria-modal="true"
                aria-label="Kode QRIS pembayaran"
                class="relative bg-surface-1 text-ink rounded-2xl p-5 w-full max-w-sm shadow-2xl focus:outline-none"
            >
                <div class="flex items-center justify-between pb-3 border-b border-hairline">
                    <div>
                        <span class="font-bold text-base block">Pindai Kode QRIS</span>
                        <span class="text-xs text-ink-subtle font-mono" x-text="formatMoney(total)"></span>
                    </div>
                    <button
                        type="button"
                        @click="qrisVisible = false"
                        class="w-9 h-9 rounded-lg hover:bg-surface-3 flex items-center justify-center text-ink-subtle hover:text-ink cursor-pointer text-xl leading-none focus-ring"
                        title="Tutup"
                        aria-label="Tutup kode QRIS"
                    >×</button>
                </div>
                <div class="mt-4 rounded-xl bg-white p-3 flex items-center justify-center min-h-[240px]">
                    <img
                        src="{{ asset('storage/qris/qris-code.png') }}"
                        alt="Kode QRIS Sumber Urip"
                        class="max-h-[60vh] w-full object-contain"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                    >
                    <p class="hidden text-xs text-black/60 text-center px-4">
                        Gambar QRIS belum tersedia. Unggah kode QRIS toko ke <span class="font-mono">storage/app/public/qris/qris-code.png</span>.
                    </p>
                </div>
                <p class="text-xs text-ink-subtle text-center mt-3">Minta pelanggan memindai kode ini, lalu pastikan dana masuk sebelum menyelesaikan pembayaran.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cashierPage', () => ({
                products: @json($products),
                cartItems: [],
                search: '',
                selectedCategory: 'all',
                paymentMethod: 'tunai',
                paidAmount: 0,
                note: '',
                loading: false,
                receiptVisible: false,
                qrisVisible: false,
                receipt: { items: [] },
                toast: { show: false, title: '', message: '', type: 'error' },

                // Category pills derived from the live catalog so counts and
                // filters always match the products the cashier can actually sell
                get categories() {
                    const counts = new Map();
                    for (const p of this.products) {
                        const cat = p.category || 'Produk Depo';
                        counts.set(cat, (counts.get(cat) || 0) + 1);
                    }
                    return [
                        { id: 'all', name: 'Semua', count: this.products.length },
                        ...[...counts.entries()].map(([name, count]) => ({ id: name, name, count })),
                    ];
                },

                showToast(message, type = 'error', title = null) {
                    this.toast = {
                        show: true,
                        title: title || (type === 'error' ? 'Pemberitahuan Kasir' : 'Berhasil'),
                        message,
                        type,
                    };
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 4000);
                },

                scrollCategories(direction) {
                    const track = this.$refs.categoryTrack;
                    if (track) {
                        track.scrollBy({ left: direction * 180, behavior: 'smooth' });
                    }
                },

                get filteredProducts() {
                    const term = this.search.toLowerCase().trim();
                    return this.products.filter(p => {
                        const matchesCategory = this.selectedCategory === 'all' ||
                            (p.category || '').toLowerCase() === this.selectedCategory.toLowerCase();
                        const matchesSearch = !term || p.name.toLowerCase().includes(term);
                        return matchesCategory && matchesSearch;
                    });
                },

                get total() {
                    return this.cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
                },

                get cartTotalItems() {
                    return this.cartItems.reduce((acc, i) => acc + i.qty, 0);
                },

                get change() {
                    return Math.max(0, this.paidAmount - this.total);
                },

                get quickCashOptions() {
                    const t = this.total;
                    if (t <= 0) return [10000, 20000, 50000, 100000];

                    const base = [10000, 20000, 50000, 100000];
                    if (t > 50000) base.push(150000, 200000);

                    const filtered = base.filter(amt => amt >= t);
                    if (filtered.length >= 4) return filtered.slice(0, 4);

                    // If total is large, dynamically offer rounded bills
                    const roundToNearest = (val, step) => Math.ceil(val / step) * step;
                    const dynamic = [
                        t,
                        roundToNearest(t, 10000) === t ? t + 10000 : roundToNearest(t, 10000),
                        roundToNearest(t, 50000) === t ? t + 50000 : roundToNearest(t, 50000),
                        roundToNearest(t, 100000) === t ? t + 100000 : roundToNearest(t, 100000)
                    ];
                    return Array.from(new Set(dynamic)).slice(0, 4);
                },

                setPaymentMethod(method) {
                    this.paymentMethod = method;
                    if (method === 'tunai' && (this.paidAmount === 0 || this.paidAmount < this.total)) {
                        this.paidAmount = this.total;
                    }
                },

                isProductInCart(productId) {
                    return this.cartItems.some(i => i.product_id === productId);
                },

                cartQty(productId) {
                    const item = this.cartItems.find(i => i.product_id === productId);
                    return item ? item.qty : 0;
                },

                formatMoney(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0);
                },

                formatShortMoney(value) {
                    if (value >= 1000000) return (value / 1000000) + 'jt';
                    if (value >= 1000) return (value / 1000) + 'k';
                    return value;
                },

                formatDate(value) {
                    if (!value) return '-';
                    return new Date(value).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                },

                handleGlobalKey(e) {
                    // Ignore when inside inputs
                    const tag = e.target.tagName.toLowerCase();
                    const isInput = tag === 'input' || tag === 'textarea' || tag === 'select';

                    if (this.qrisVisible) {
                        if (e.key === 'Escape') {
                            this.qrisVisible = false;
                        }
                        return;
                    }

                    if (e.key === '/' && !isInput) {
                        e.preventDefault();
                        if (this.$refs.searchInput) {
                            this.$refs.searchInput.focus();
                            this.$refs.searchInput.select();
                        }
                    } else if (e.key === 'Escape') {
                        if (this.receiptVisible) {
                            this.receiptVisible = false;
                        } else if (isInput) {
                            e.target.blur();
                        }
                    } else if (e.key === 'Enter' && !isInput && this.cartItems.length > 0 && !this.loading) {
                        if (this.paymentMethod !== 'tunai' || this.paidAmount >= this.total) {
                            this.saveTransaction();
                        }
                    } else if (e.key === 'Enter' && e.target === this.$refs.cashInput && !this.loading) {
                        // Enter di kolom nominal tunai = konfirmasi pembayaran
                        e.preventDefault();
                        this.saveTransaction();
                    }
                },

                addProduct(product) {
                    if (product.track_stock && product.stock <= 0) {
                        this.showToast('Stok produk ' + product.name + ' sudah habis.', 'error');
                        return;
                    }

                    const existing = this.cartItems.find(i => i.product_id === product.id);
                    const limit = product.track_stock ? product.stock : Infinity;

                    if (existing) {
                        if (existing.qty >= limit) {
                            this.showToast('Stok produk ' + product.name + ' tidak mencukupi.', 'error');
                            return;
                        }
                        existing.qty += 1;
                    } else {
                        this.cartItems.push({
                            product_id: product.id,
                            name: product.name,
                            price: product.price,
                            unit: product.unit,
                            image_url: product.image_url || '/storage/products/isi-ulang-air-galon.svg',
                            qty: 1,
                        });
                    }

                    if (this.paymentMethod === 'tunai' && (this.paidAmount === 0 || this.paidAmount < this.total)) {
                        this.paidAmount = this.total;
                    }
                },

                updateQty(index, qty) {
                    const item = this.cartItems[index];
                    const product = this.products.find(p => p.id === item.product_id);
                    const limit = product && product.track_stock ? product.stock : Infinity;

                    if (qty < 1) {
                        this.cartItems.splice(index, 1);
                    } else if (qty > limit) {
                        this.showToast(isFinite(limit) ? 'Stok tidak cukup — maksimal ' + limit + ' ' + (item.unit || 'pcs') + '.' : 'Stok produk tidak mencukupi.', 'error');
                        item.qty = limit;
                    } else {
                        item.qty = qty;
                    }

                    if (this.paymentMethod === 'tunai' && this.paidAmount < this.total) {
                        this.paidAmount = this.total;
                    }
                },

                removeItem(index) {
                    this.cartItems.splice(index, 1);
                    if (this.paymentMethod === 'tunai' && this.paidAmount < this.total) {
                        this.paidAmount = this.total;
                    }
                },

                clearCart() {
                    this.cartItems = [];
                    this.paidAmount = 0;
                    this.note = '';
                    this.showToast('Keranjang belanja berhasil dikosongkan.', 'info', 'Keranjang Bersih');
                },

                async saveTransaction() {
                    if (this.cartItems.length === 0) return;

                    if (this.paymentMethod === 'tunai' && this.paidAmount < this.total) {
                        this.showToast('Nominal uang diterima kurang dari total tagihan.', 'error');
                        if (this.$refs.cashInput) this.$refs.cashInput.focus();
                        return;
                    }

                    this.loading = true;

                    try {
                        const response = await fetch('{{ route('kasir.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                items: this.cartItems.map(i => ({ product_id: i.product_id, quantity: i.qty })),
                                payment_method: this.paymentMethod,
                                paid_amount: this.paymentMethod === 'tunai' ? this.paidAmount : this.total,
                                note: this.note || null,
                            }),
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            const message = result.message || (result.errors ? Object.values(result.errors).flat()[0] : 'Transaksi gagal diproses.');
                            this.showToast(message, 'error');
                            return;
                        }

                        const tx = result.transaction;
                        this.receipt = {
                            invoice_number: tx.invoice_number,
                            created_at: tx.created_at,
                            items: tx.items,
                            total: tx.total,
                            paid_amount: tx.paid_amount,
                            change_amount: tx.change_amount,
                            note: tx.note,
                            url: result.receipt_url,
                        };

                        this.receiptVisible = true;

                        // Deduct local stock
                        this.products = this.products.map(p => {
                            const sold = this.cartItems.find(i => i.product_id === p.id);
                            if (p.track_stock && sold) {
                                return { ...p, stock: p.stock - sold.qty };
                            }
                            return p;
                        });

                        this.clearCart();
                        this.paymentMethod = 'tunai';
                    } catch (e) {
                        this.showToast('Terjadi kesalahan jaringan saat memproses transaksi.', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                printReceipt() {
                    window.print();
                    this.receiptVisible = false;
                },
            }));
        });
    </script>
@endsection

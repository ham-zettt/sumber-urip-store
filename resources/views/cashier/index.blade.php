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
                                        onerror="this.onerror=null;this.src='/storage/products/isi-ulang-air-galon.svg'"
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
                <div class="bg-surface-1 border border-hairline rounded-3xl p-4 sm:p-5 lg:pb-0 shadow-xs flex flex-col lg:max-h-[calc(100vh_-_176px)] lg:overflow-y-auto scrollbar-thin">
                    <!-- Cart Header -->
                    <div class="flex items-center justify-between pb-3.5 border-b border-hairline shrink-0">
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-base sm:text-lg font-bold text-ink">Detail Transaksi</h2>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-surface-2 text-ink-subtle border border-hairline" x-text="cartTotalItems + ' item'"></span>
                        </div>

                        <!-- Reset Order Button (Layout sesuai gambar referensi) -->
                        <button
                            x-show="cartItems.length > 0"
                            @click="clearCart()"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-hairline bg-surface-1 hover:bg-surface-2 text-xs font-semibold text-ink transition-colors focus-ring cursor-pointer shadow-2xs"
                            title="Reset Order"
                            aria-label="Reset order keranjang"
                        >
                            <svg class="w-3.5 h-3.5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            <span>Reset Order</span>
                        </button>
                    </div>

                    <!-- Cart Items List (Layout item sesuai gambar referensi) -->
                    <div class="overflow-y-auto space-y-3 py-3 max-h-[340px] pr-1 scrollbar-thin shrink-0">
                        <template x-for="(item, index) in cartItems" :key="item.product_id">
                            <div class="bg-surface-2/60 border border-hairline rounded-2xl p-3 sm:p-3.5 flex items-center gap-3 transition-colors hover:border-hairline-strong">
                                <!-- Thumbnail Image -->
                                <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-2xl bg-surface-1 border border-hairline/60 p-2 shrink-0 flex items-center justify-center overflow-hidden">
                                    <img
                                        :src="item.image_url"
                                        :alt="item.name"
                                        class="w-full h-full object-contain"
                                        onerror="this.onerror=null;this.src='/storage/products/isi-ulang-air-galon.svg'"
                                    >
                                </div>

                                <!-- Item Details -->
                                <div class="flex-1 min-w-0 flex flex-col justify-between self-stretch py-0.5">
                                    <!-- Row 1: Title and Delete Button -->
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="text-xs sm:text-sm font-bold text-ink truncate pr-1" x-text="item.name"></h4>
                                        <button
                                            type="button"
                                            @click="removeItem(index)"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-danger text-white hover:bg-danger/90 active:scale-95 flex items-center justify-center shrink-0 transition-transform cursor-pointer shadow-xs"
                                            title="Hapus produk"
                                            aria-label="Hapus produk dari pesanan"
                                        >
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Row 2: Attributes Pills -->
                                    <div class="flex items-center gap-2 my-1">
                                        <span class="text-[11px] text-ink-subtle bg-surface-1 px-2.5 py-0.5 rounded-full border border-hairline" x-text="item.unit || 'pcs'"></span>
                                        <span class="text-[11px] text-ink-subtle flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-ink-subtle"></span>
                                            <span x-text="formatMoney(item.price)"></span>
                                        </span>
                                    </div>

                                    <!-- Row 3: Stepper & Total -->
                                    <div class="flex items-center justify-between mt-auto pt-1">
                                        <!-- Stepper -->
                                        <div class="inline-flex items-center gap-2 bg-surface-1 border border-hairline rounded-full px-2 py-1">
                                            <button
                                                type="button"
                                                @click="updateQty(index, item.qty - 1)"
                                                class="w-6 h-6 rounded-full hover:bg-surface-3 text-ink flex items-center justify-center text-sm font-bold transition-colors cursor-pointer"
                                                title="Kurangi"
                                                aria-label="Kurangi jumlah"
                                            >−</button>
                                            <span class="w-6 text-center text-xs font-bold text-ink" x-text="String(item.qty).padStart(2, '0')"></span>
                                            <button
                                                type="button"
                                                @click="updateQty(index, item.qty + 1)"
                                                class="w-6 h-6 rounded-full bg-primary text-white hover:brightness-105 flex items-center justify-center text-sm font-bold transition-colors cursor-pointer shadow-2xs"
                                                title="Tambah"
                                                aria-label="Tambah jumlah"
                                            >+</button>
                                        </div>

                                        <!-- Item Total (Font Global - Bukan Mono) -->
                                        <div class="text-right">
                                            <span class="text-xs text-ink-subtle mr-1.5">Total</span>
                                            <span class="text-xs sm:text-sm font-bold text-ink" x-text="formatMoney(item.price * item.qty)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty Cart State -->
                        <template x-if="cartItems.length === 0">
                            <div class="flex flex-col items-center justify-center py-10 text-center text-ink-subtle">
                                <div class="w-14 h-14 rounded-2xl bg-surface-2 border border-hairline flex items-center justify-center text-ink-subtle mb-3">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
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
                    <div class="border-t border-hairline pt-3.5 space-y-3 mt-1 shrink-0">
                        <!-- FITUR TRANSAKSI LAWAS (TOGGLE SWITCH) -->
                        <div class="bg-surface-2/60 border border-hairline rounded-2xl p-3 sm:p-3.5 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-surface-1 border border-hairline flex items-center justify-center text-ink-subtle">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-ink block">Transaksi Lawas</span>
                                        <span class="text-[11px] text-ink-subtle block">Input data untuk transaksi tanggal lampau</span>
                                    </div>
                                </div>
                                <!-- Toggle Switch -->
                                <button
                                    type="button"
                                    @click="toggleCustomDate()"
                                    role="switch"
                                    :aria-checked="isCustomDate"
                                    :class="isCustomDate ? 'bg-primary' : 'bg-surface-3'"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus-ring"
                                    title="Aktifkan transaksi lawas"
                                    aria-label="Toggle transaksi lawas"
                                >
                                    <span
                                        :class="isCustomDate ? 'translate-x-5 bg-white' : 'translate-x-0 bg-ink-subtle'"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full shadow-md ring-0 transition duration-200 ease-in-out"
                                    ></span>
                                </button>
                            </div>

                            <!-- Date selector when toggle is active -->
                            <div x-show="isCustomDate" x-transition.opacity.duration.200ms class="pt-2 border-t border-hairline space-y-2">
                                <div class="flex items-center justify-between">
                                    <label for="custom-tx-date" class="block text-xs font-semibold text-ink">
                                        Pilih Tanggal Transaksi
                                    </label>
                                    <span class="text-[11px] text-primary font-medium" x-text="formatSelectedDate(customDate)"></span>
                                </div>
                                <input
                                    id="custom-tx-date"
                                    type="date"
                                    x-model="customDate"
                                    :max="todayDate"
                                    class="w-full bg-surface-1 border border-hairline rounded-xl px-3 py-2 text-xs text-ink focus:outline-none focus:border-primary cursor-pointer font-sans"
                                >
                                <p class="text-[11px] text-ink-subtle leading-tight">
                                    * Transaksi dan pergerakan stok akan tercatat sesuai tanggal yang dipilih.
                                </p>
                            </div>
                        </div>

                        <!-- METODE PEMBAYARAN -->
                        <div class="bg-surface-2/60 border border-hairline rounded-2xl p-3 sm:p-3.5 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-ink">Metode Pembayaran</span>
                                <div class="flex items-center gap-1 bg-surface-1 p-1 rounded-xl border border-hairline">
                                    <button
                                        type="button"
                                        @click="setPaymentMethod('tunai')"
                                        :aria-pressed="paymentMethod === 'tunai'"
                                        :class="paymentMethod === 'tunai' ? 'bg-primary text-on-primary font-bold shadow-2xs' : 'text-ink-subtle hover:text-ink font-medium'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition-all cursor-pointer flex items-center gap-1.5"
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
                                        :class="paymentMethod === 'qris' ? 'bg-primary text-on-primary font-bold shadow-2xs' : 'text-ink-subtle hover:text-ink font-medium'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition-all cursor-pointer flex items-center gap-1.5"
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

                            <!-- Tunai Cash Input & Smart Denominations -->
                            <template x-if="paymentMethod === 'tunai'">
                                <div class="space-y-2.5 pt-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <label class="text-xs font-medium text-ink-subtle" for="uang-diterima">Uang Diterima</label>
                                        <button
                                            type="button"
                                            @click="paidAmount = total"
                                            class="bg-surface-1 hover:bg-surface-3 border border-hairline text-primary hover:text-primary-hover text-xs font-bold px-2.5 py-1 rounded-lg transition-colors cursor-pointer"
                                        >Uang Pas</button>
                                    </div>

                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-ink-subtle">Rp</span>
                                        <input
                                            id="uang-diterima"
                                            type="number"
                                            inputmode="numeric"
                                            x-ref="cashInput"
                                            x-model.number="paidAmount"
                                            min="0"
                                            placeholder="0"
                                            class="w-full min-h-[42px] bg-surface-1 border border-hairline rounded-xl pl-9 pr-3 py-2 text-sm sm:text-base font-bold text-ink focus:outline-none focus:border-primary transition-all [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none [-moz-appearance:textfield]"
                                        >
                                    </div>

                                    <!-- Quick Cash Pills (Font Global - Bukan Mono) -->
                                    <div class="grid grid-cols-4 gap-1.5">
                                        <template x-for="amt in quickCashOptions" :key="amt">
                                            <button
                                                type="button"
                                                @click="paidAmount = amt"
                                                :class="paidAmount === amt ? 'bg-primary text-on-primary font-bold' : 'bg-surface-1 hover:bg-surface-3 text-ink font-medium'"
                                                class="py-1.5 text-xs rounded-lg border border-hairline transition-all cursor-pointer text-center font-sans"
                                                x-text="formatShortMoney(amt)"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- QRIS Guidance -->
                            <template x-if="paymentMethod === 'qris'">
                                <div class="pt-1 flex items-center justify-between gap-2">
                                    <p class="text-xs text-ink-subtle">Pindai QRIS senilai <span class="font-bold text-ink" x-text="formatMoney(total)"></span></p>
                                    <button
                                        type="button"
                                        @click="qrisVisible = true"
                                        class="shrink-0 bg-surface-1 hover:bg-surface-3 border border-primary/40 text-primary font-bold text-xs px-3 py-1.5 rounded-lg transition-colors cursor-pointer"
                                    >Lihat QRIS</button>
                                </div>
                            </template>
                        </div>

                        <!-- CATATAN TRANSAKSI -->
                        <div>
                            <label for="catatan-transaksi" class="sr-only">Catatan transaksi</label>
                            <input
                                id="catatan-transaksi"
                                type="text"
                                x-model="note"
                                placeholder="Catatan transaksi (opsional, nama pembeli / alamat)..."
                                class="w-full min-h-[40px] bg-surface-2/60 border border-hairline rounded-xl px-3 py-2.5 text-xs text-ink placeholder:text-ink-subtle focus:outline-none focus:border-primary"
                            >
                        </div>

                        <!-- SUMMARY CARD: TOTAL HARGA & UANG KEMBALIAN (LAYOUT SESUAI GAMBAR REFERENSI) -->
                        <div class="rounded-2xl border border-hairline bg-surface-2/60 p-4 space-y-2.5">
                            <!-- Sub-Total -->
                            <div class="flex items-center justify-between text-xs sm:text-sm">
                                <span class="text-ink-subtle">Sub-Total</span>
                                <span class="font-semibold text-ink" x-text="formatMoney(total)"></span>
                            </div>

                            <!-- Uang Diterima (Khusus Tunai) -->
                            <template x-if="paymentMethod === 'tunai'">
                                <div class="flex items-center justify-between text-xs sm:text-sm">
                                    <span class="text-ink-subtle">Uang Diterima</span>
                                    <span class="font-semibold text-ink" x-text="formatMoney(paidAmount)"></span>
                                </div>
                            </template>

                            <!-- Divider Line -->
                            <div class="border-t border-hairline my-1.5"></div>

                            <!-- Bottom Row: Uang Kembalian / Total Pembayaran -->
                            <template x-if="paymentMethod === 'tunai'">
                                <div class="flex items-center justify-between text-sm sm:text-base">
                                    <span class="font-semibold" :class="paidAmount >= total ? 'text-ink' : 'text-danger'" x-text="paidAmount >= total ? 'Uang Kembalian' : 'Sisa Kurang Bayar'"></span>
                                    <span class="font-bold text-base sm:text-lg" :class="paidAmount >= total ? 'text-success' : 'text-danger'" x-text="paidAmount >= total ? formatMoney(change) : '- ' + formatMoney(total - paidAmount)"></span>
                                </div>
                            </template>

                            <template x-if="paymentMethod !== 'tunai'">
                                <div class="flex items-center justify-between text-sm sm:text-base">
                                    <span class="font-semibold text-ink">Total Pembayaran</span>
                                    <span class="font-bold text-base sm:text-lg text-ink" x-text="formatMoney(total)"></span>
                                </div>
                            </template>
                        </div>

                        <!-- CHECKOUT ACTION BUTTON (Layout sesuai gambar referensi: pill/rounded-2xl button) -->
                        <div class="mt-3 lg:sticky lg:bottom-0 lg:z-10 lg:bg-surface-1 lg:pt-2 lg:pb-4 sm:lg:pb-5">
                            <button
                                type="button"
                                @click="saveTransaction()"
                                :disabled="cartItems.length === 0 || loading || (paymentMethod === 'tunai' && paidAmount < total)"
                                class="w-full bg-primary hover:brightness-105 active:brightness-95 disabled:bg-surface-2 disabled:text-ink-subtle disabled:cursor-not-allowed text-on-primary font-bold text-sm sm:text-base rounded-2xl py-3.5 min-h-[50px] shadow-sm transition-all duration-150 focus-ring flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <template x-if="loading">
                                    <svg class="animate-spin h-5 w-5 text-on-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </template>
                                <template x-if="!loading">
                                    <svg class="w-5 h-5 text-on-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </template>
                                <span x-text="loading ? 'Memproses Transaksi...' : (cartItems.length === 0 ? 'Keranjang Kosong' : (isCustomDate ? 'Simpan Transaksi Lawas (' + formatMoney(total) + ')' : 'Selesaikan Pembayaran (' + formatMoney(total) + ')'))"></span>
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
                isCustomDate: false,
                customDate: '{{ now()->toDateString() }}',
                todayDate: '{{ now()->toDateString() }}',
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

                toggleCustomDate() {
                    this.isCustomDate = !this.isCustomDate;
                    if (this.isCustomDate && !this.customDate) {
                        this.customDate = this.todayDate;
                    }
                },

                formatSelectedDate(value) {
                    if (!value) return '-';
                    try {
                        const parts = value.split('-');
                        if (parts.length === 3) {
                            const d = new Date(parts[0], parts[1] - 1, parts[2]);
                            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                        }
                        return value;
                    } catch (e) {
                        return value;
                    }
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
                            image_url: product.image_url || '/storage/products/galon.jpg',
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
                    this.isCustomDate = false;
                    this.customDate = this.todayDate;
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
                                is_custom_date: this.isCustomDate,
                                transaction_date: this.isCustomDate ? this.customDate : null,
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

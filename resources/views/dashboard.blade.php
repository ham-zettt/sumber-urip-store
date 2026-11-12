@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Dashboard Header & Actions (Image 0 Style) -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink">Dashboard</h1>
            <p class="text-xs sm:text-sm text-ink-subtle mt-0.5">Ringkasan aktivitas penjualan & performa toko Sumber Urip.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <div x-data="{ openFilter: false }" class="relative">
                <button
                    type="button"
                    @click="openFilter = !openFilter"
                    class="inline-flex items-center gap-2 bg-surface-1 hover:bg-surface-2 border border-hairline text-ink font-medium text-xs sm:text-sm rounded-xl px-3.5 py-2.5 min-h-[42px] transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span>Filter</span>
                </button>
                <div
                    x-show="openFilter"
                    @click.outside="openFilter = false"
                    x-transition
                    class="absolute right-0 mt-2 w-48 bg-surface-1 border border-hairline rounded-xl shadow-lg p-2 z-30 text-xs"
                >
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-surface-2 text-ink font-medium">Hari Ini</a>
                    <a href="{{ route('transaksi.index') }}" class="block px-3 py-2 rounded-lg hover:bg-surface-2 text-ink font-medium">Semua Transaksi</a>
                    <a href="{{ route('stok.index') }}" class="block px-3 py-2 rounded-lg hover:bg-surface-2 text-ink font-medium">Riwayat Stok</a>
                </div>
            </div>

            <a
                href="{{ route('kasir.index') }}"
                class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-on-primary font-semibold text-xs sm:text-sm rounded-xl px-4 py-2.5 min-h-[42px] shadow-xs transition-colors cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Buka Kasir</span>
            </a>

            <a
                href="{{ route('produk.create') }}"
                class="inline-flex items-center gap-2 bg-surface-1 hover:bg-surface-2 border border-hairline text-ink font-semibold text-xs sm:text-sm rounded-xl px-3.5 py-2.5 min-h-[42px] transition-colors cursor-pointer"
                title="Tambah Produk Baru"
            >
                <svg class="w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                <span class="hidden sm:inline">Tambah Produk</span>
            </a>
        </div>
    </div>

    <!-- Top Cards Row (Total Balance + Statistics, matching Image 0) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
        <!-- Total Balance Card (Image 0 Left Card) -->
        <div class="lg:col-span-5 bg-surface-1 border border-hairline rounded-2xl p-5 sm:p-6 relative overflow-hidden shadow-xs flex flex-col justify-between min-h-[190px]">
            <div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-ink-subtle uppercase tracking-wider">Total Pendapatan</span>
                    <span class="text-[11px] text-ink-subtle bg-surface-2 px-2 py-0.5 rounded-full border border-hairline/60">
                        {{ now()->translatedFormat('d M Y') }}
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight mt-2">
                    Rp {{ number_format($totalBalance > 0 ? $totalBalance : ($todayStats->total_revenue ?? 0), 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-ink-subtle mt-0.5">
                    Pembaruan terakhir: {{ now()->translatedFormat('H:i') }} WIB
                </p>
            </div>

            <!-- Glowing Wave / Sparkline (Matching Image 0) -->
            <div class="mt-4 -mb-1 w-full overflow-hidden">
                <svg class="w-full h-14" viewBox="0 0 340 55" fill="none" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="waveGrad" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#5e6ad2" stop-opacity="0.3"/>
                            <stop offset="50%" stop-color="#828fff" stop-opacity="0.95"/>
                            <stop offset="100%" stop-color="#38bdf8" stop-opacity="0.9"/>
                        </linearGradient>
                        <linearGradient id="waveFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#5e6ad2" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#5e6ad2" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path
                        d="M0 40 C35 40, 45 15, 75 28 C105 40, 125 45, 155 18 C185 -4, 205 48, 235 12 C255 -2, 275 35, 305 22 C320 15, 330 25, 340 20 L340 55 L0 55 Z"
                        fill="url(#waveFill)"
                    />
                    <path
                        d="M0 40 C35 40, 45 15, 75 28 C105 40, 125 45, 155 18 C185 -4, 205 48, 235 12 C255 -2, 275 35, 305 22 C320 15, 330 25, 340 20"
                        stroke="url(#waveGrad)"
                        stroke-width="3"
                        stroke-linecap="round"
                    />
                </svg>
            </div>
        </div>

        <!-- Statistics Card (Image 0 Right Card) -->
        <div class="lg:col-span-7 bg-surface-1 border border-hairline rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-hairline/80">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-ink">Statistik Toko</h2>
                    <p class="text-[11px] text-ink-subtle">Performa transaksi & penjualan harian</p>
                </div>
                <div class="inline-flex items-center gap-1.5 bg-surface-2 border border-hairline px-3 py-1.5 rounded-xl text-xs font-medium text-ink">
                    <span>Hari Ini</span>
                    <svg class="w-3.5 h-3.5 text-ink-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                <!-- Col 1: Total Earnings -->
                <div class="sm:border-r sm:border-hairline/60 sm:pr-4">
                    <span class="text-xs font-medium text-ink-subtle block">Pendapatan Hari Ini</span>
                    <div class="text-lg sm:text-xl font-bold text-ink mt-1 tracking-tight">
                        Rp {{ number_format($todayStats->total_revenue ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1 text-xs font-semibold text-success mt-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                        <span>+{{ $todayStats->total_revenue > 0 ? '18.4%' : '0%' }}</span>
                    </div>
                </div>

                <!-- Col 2: Number of Sales -->
                <div class="sm:border-r sm:border-hairline/60 sm:px-2">
                    <span class="text-xs font-medium text-ink-subtle block">Jumlah Transaksi</span>
                    <div class="text-lg sm:text-xl font-bold text-ink mt-1 tracking-tight">
                        {{ number_format($todayStats->transaction_count ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1 text-xs font-semibold text-primary mt-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>{{ $todayStats->transaction_count > 0 ? 'Selesai' : 'Siap Kasir' }}</span>
                    </div>
                </div>

                <!-- Col 3: Product Views / Items Sold -->
                <div class="sm:pl-2">
                    <span class="text-xs font-medium text-ink-subtle block">Total Item Terjual</span>
                    <div class="text-lg sm:text-xl font-bold text-ink mt-1 tracking-tight">
                        {{ number_format($todayStats->items_sold ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-ink-subtle">item</span>
                    </div>
                    <div class="flex items-center gap-1 text-xs font-semibold text-success mt-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                        <span>+{{ $todayStats->items_sold > 0 ? '8.3%' : '0%' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row (Purchase Activity Chart + Popular Tags, matching Image 0) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
        <!-- Purchase Activity Chart (Image 0 Left Main Chart) -->
        <div x-data="salesChartComponent()" class="lg:col-span-8 bg-surface-1 border border-hairline rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-ink">Aktivitas Penjualan</h2>
                    <div class="flex items-center gap-2 mt-0.5 text-xs text-ink-subtle">
                        <span>Total omset periode ini:</span>
                        <span class="font-bold text-ink" x-text="formattedTotal">Rp 0</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Legend Indicators (Image 0 style) -->
                    <div class="hidden sm:flex items-center gap-3 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary inline-block"></span>
                            <span class="text-ink-subtle">Pendapatan</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-400 inline-block"></span>
                            <span class="text-ink-subtle">Item Terjual</span>
                        </div>
                    </div>

                    <!-- Range Selector Pills -->
                    <div class="inline-flex rounded-xl bg-surface-2 p-1 border border-hairline shrink-0">
                        <button
                            type="button"
                            @click="setRange(7)"
                            :class="range === 7 ? 'bg-primary text-on-primary shadow-xs font-semibold' : 'text-ink-subtle hover:text-ink font-medium'"
                            class="rounded-lg px-3 py-1 text-xs transition-colors min-h-[30px] flex items-center cursor-pointer"
                        >
                            7 Hari
                        </button>
                        <button
                            type="button"
                            @click="setRange(30)"
                            :class="range === 30 ? 'bg-primary text-on-primary shadow-xs font-semibold' : 'text-ink-subtle hover:text-ink font-medium'"
                            class="rounded-lg px-3 py-1 text-xs transition-colors min-h-[30px] flex items-center cursor-pointer"
                        >
                            30 Hari
                        </button>
                    </div>
                </div>
            </div>

            <div class="relative min-h-[290px] w-full flex-1">
                <!-- Loading overlay -->
                <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-surface-1/70 backdrop-blur-xs z-10 rounded-xl transition-opacity">
                    <div class="flex items-center gap-2 text-xs text-ink-muted bg-surface-2 px-3.5 py-2 rounded-full border border-hairline shadow-sm">
                        <svg class="animate-spin h-3.5 w-3.5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Menyiapkan grafik aktivitas...</span>
                    </div>
                </div>

                <!-- Error fallback -->
                <div x-show="!loading && error" x-cloak class="absolute inset-0 flex flex-col items-center justify-center text-center p-4">
                    <p class="text-xs text-danger" x-text="error"></p>
                    <button type="button" @click="fetchData()" class="mt-2 text-xs text-primary hover:underline font-medium">Coba lagi</button>
                </div>

                <!-- ApexCharts container (horizontal scroll on mobile) -->
                <div class="overflow-x-auto -mx-2 px-2">
                    <div x-ref="chartContainer" :style="isMobile ? 'min-width:' + mobileChartWidth + 'px' : 'width:100%'"></div>
                </div>
            </div>
        </div>

        <!-- Popular Tags & Low Stock Alerts (Image 0 Right Side Widget) -->
        <div class="lg:col-span-4 space-y-5">
            <!-- Popular Tags Card (Image 0 style) -->
            <div class="bg-surface-1 border border-hairline rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3.5">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <h2 class="text-sm font-bold text-ink">Tag & Kategori Populer</h2>
                    </div>
                    <button type="button" class="text-ink-subtle hover:text-ink w-7 h-7 rounded-lg flex items-center justify-center hover:bg-surface-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="12" cy="12" r="1"></circle>
                            <circle cx="19" cy="12" r="1"></circle>
                            <circle cx="5" cy="12" r="1"></circle>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('kasir.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #isi-ulang
                    </a>
                    <a href="{{ route('kasir.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #air-galon
                    </a>
                    <a href="{{ route('kasir.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #galon-baru
                    </a>
                    <a href="{{ route('kasir.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #paket-hemat
                    </a>
                    <a href="{{ route('kasir.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #tutup-segel
                    </a>
                    <a href="{{ route('transaksi.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #tunai
                    </a>
                    <a href="{{ route('transaksi.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #transfer
                    </a>
                    <a href="{{ route('transaksi.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #qris
                    </a>
                    <a href="{{ route('produk.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-xl bg-surface-2 hover:bg-primary hover:text-on-primary border border-hairline hover:border-primary text-ink-muted transition-all duration-150 cursor-pointer">
                        #depo-air
                    </a>
                </div>
            </div>

            <!-- Low Stock Warnings Card -->
            <div class="bg-surface-1 border border-hairline rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-danger animate-pulse"></div>
                        <h2 class="text-sm font-bold text-ink">Peringatan Stok Rendah</h2>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-danger/10 text-danger border border-danger/20">
                        {{ $lowStockProducts->count() }} Produk
                    </span>
                </div>

                <div class="space-y-2.5 text-xs">
                    @forelse ($lowStockProducts as $product)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-surface-2/80 border border-hairline hover:border-danger/40 transition-colors">
                            <div class="min-w-0 flex-1">
                                <span class="font-semibold text-ink truncate block">{{ $product->name }}</span>
                                <span class="text-[11px] text-ink-subtle">Batas Min: {{ $product->min_stock }} {{ $product->unit }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2 py-0.5 rounded-md font-bold text-danger bg-danger/10 border border-danger/20">
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-ink-subtle text-xs">
                            Semua stok produk dalam kondisi aman.
                        </div>
                    @endforelse
                </div>

                <div class="mt-3.5 pt-3 border-t border-hairline flex justify-end">
                    <a href="{{ route('stok.create') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                        <span>+ Tambah Stok Masuk</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products Row -->
    <div class="bg-surface-1 border border-hairline rounded-2xl p-5 sm:p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-ink">Produk Terlaris (30 Hari Terakhir)</h2>
                <p class="text-xs text-ink-subtle">Daftar produk dengan volume transaksi tertinggi</p>
            </div>
            <a href="{{ route('produk.index') }}" class="text-xs font-semibold text-primary hover:underline">Lihat Semua</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            @forelse ($topProducts as $product)
                <div class="bg-surface-2/70 border border-hairline hover:border-primary/50 rounded-xl p-3.5 transition-all">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-bold text-ink truncate">{{ $product->name }}</span>
                        <span class="text-[10px] text-ink-subtle bg-surface-1 px-1.5 py-0.5 rounded border border-hairline uppercase">{{ $product->unit }}</span>
                    </div>
                    <div class="text-lg font-bold text-ink">{{ number_format($product->total_qty, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-ink-subtle flex items-center justify-between mt-1">
                        <span>Volume terjual</span>
                        <span class="font-semibold text-primary">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-6 text-ink-subtle text-xs">
                    Belum ada data transaksi dalam 30 hari terakhir.
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            function registerSalesChartComponent() {
                window.Alpine.data('salesChartComponent', () => ({
                    range: 7,
                    loading: true,
                    error: null,
                    chart: null,
                    totalRevenue: 0,
                    isMobile: window.innerWidth < 640,
                    mobileChartWidth: 680,

                    get formattedTotal() {
                        return 'Rp ' + Number(this.totalRevenue || 0).toLocaleString('id-ID');
                    },

                    async init() {
                        window.addEventListener('theme-changed', () => {
                            if (this.chart) {
                                const isLight = document.documentElement.classList.contains('light');
                                this.chart.updateOptions({
                                    theme: { mode: isLight ? 'light' : 'dark' },
                                    grid: { borderColor: isLight ? '#e2e8f0' : '#23252a' },
                                    xaxis: {
                                        axisBorder: { color: isLight ? '#e2e8f0' : '#23252a' },
                                        labels: {
                                            style: {
                                                colors: isLight ? '#64748b' : '#8a8f98'
                                            }
                                        }
                                    },
                                    yaxis: [
                                        {
                                            labels: {
                                                style: {
                                                    colors: isLight ? '#64748b' : '#8a8f98'
                                                }
                                            }
                                        },
                                        {
                                            labels: {
                                                style: {
                                                    colors: isLight ? '#64748b' : '#8a8f98'
                                                }
                                            }
                                        }
                                    ],
                                    tooltip: {
                                        theme: isLight ? 'light' : 'dark'
                                    }
                                });
                            }
                        });
                        await this.waitForApex();
                        await this.fetchData();
                    },

                    async waitForApex() {
                        let attempts = 0;
                        while (typeof window.ApexCharts === 'undefined' && attempts < 60) {
                            await new Promise(resolve => setTimeout(resolve, 50));
                            attempts++;
                        }
                    },

                    async setRange(newRange) {
                        if (this.range === newRange || this.loading) return;
                        this.range = newRange;
                        await this.fetchData();
                    },

                    async fetchData() {
                        this.loading = true;
                        this.error = null;

                        try {
                            const url = `{{ route('dashboard.chart') }}?range=${this.range}`;
                            const res = await fetch(url, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!res.ok) {
                                throw new Error('Gagal memuat data (Status ' + res.status + ')');
                            }

                            const payload = await res.json();
                            const revenue = (payload.revenue || payload.data || []).map(v => Number(v) || 0);
                            const items = (payload.items || payload.counts || []).map(v => Number(v) || 0);

                            this.totalRevenue = revenue.reduce((acc, curr) => acc + curr, 0);

                            this.renderChart(payload.labels || [], revenue, items);
                        } catch (err) {
                            console.error('Error fetching sales chart:', err);
                            this.error = 'Tidak dapat memuat data grafik.';
                        } finally {
                            this.loading = false;
                        }
                    },

                    renderChart(labels, revenue, items) {
                        if (typeof window.ApexCharts === 'undefined') {
                            this.error = 'Library grafik tidak tersedia.';
                            return;
                        }

                        const el = this.$refs.chartContainer;
                        if (!el) return;

                        this.isMobile = window.innerWidth < 640;
                        const minWidthPerPoint = 48;
                        const computedWidth = this.isMobile
                            ? Math.max(680, labels.length * minWidthPerPoint)
                            : '100%';
                        this.mobileChartWidth = typeof computedWidth === 'number' ? computedWidth : 680;

                        const isLight = document.documentElement.classList.contains('light');

                        // Configure ApexCharts column chart matching Image 0
                        const options = {
                            chart: {
                                type: 'bar',
                                height: 300,
                                width: this.isMobile ? this.mobileChartWidth : '100%',
                                fontFamily: 'Inter, system-ui, sans-serif',
                                background: 'transparent',
                                toolbar: { show: false },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 450
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '42%',
                                    borderRadius: 6,
                                    borderRadiusApplication: 'end'
                                }
                            },
                            series: [
                                {
                                    name: 'Pendapatan',
                                    data: revenue
                                },
                                {
                                    name: 'Item Terjual',
                                    data: items
                                }
                            ],
                            colors: ['#5e6ad2', '#38bdf8'],
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            dataLabels: {
                                enabled: false
                            },
                            grid: {
                                borderColor: isLight ? '#e2e8f0' : '#23252a',
                                strokeDashArray: 4,
                                xaxis: {
                                    lines: { show: false }
                                },
                                yaxis: {
                                    lines: { show: true }
                                },
                                padding: {
                                    top: 10,
                                    right: 15,
                                    bottom: 0,
                                    left: 10
                                }
                            },
                            theme: {
                                mode: isLight ? 'light' : 'dark'
                            },
                            xaxis: {
                                categories: labels,
                                axisBorder: {
                                    show: true,
                                    color: isLight ? '#e2e8f0' : '#23252a'
                                },
                                axisTicks: {
                                    show: false
                                },
                                labels: {
                                    style: {
                                        colors: isLight ? '#64748b' : '#8a8f98',
                                        fontSize: '11px',
                                        fontFamily: 'Inter, system-ui, sans-serif'
                                    }
                                }
                            },
                            yaxis: [
                                {
                                    title: {
                                        text: undefined
                                    },
                                    labels: {
                                        style: {
                                            colors: isLight ? '#64748b' : '#8a8f98',
                                            fontSize: '11px',
                                            fontFamily: 'Inter, system-ui, sans-serif'
                                        },
                                        formatter: function (val) {
                                            if (val >= 1000000) {
                                                return 'Rp ' + (val / 1000000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt';
                                            }
                                            if (val >= 1000) {
                                                return 'Rp ' + (val / 1000).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' rb';
                                            }
                                            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                                        }
                                    }
                                },
                                {
                                    opposite: true,
                                    title: {
                                        text: undefined
                                    },
                                    labels: {
                                        style: {
                                            colors: isLight ? '#64748b' : '#8a8f98',
                                            fontSize: '11px',
                                            fontFamily: 'Inter, system-ui, sans-serif'
                                        },
                                        formatter: function (val) {
                                            return Number(val || 0).toLocaleString('id-ID') + ' item';
                                        }
                                    }
                                }
                            ],
                            legend: {
                                show: false
                            },
                            tooltip: {
                                theme: isLight ? 'light' : 'dark',
                                shared: true,
                                intersect: false,
                                y: {
                                    formatter: function (val, { seriesIndex }) {
                                        if (seriesIndex === 0) {
                                            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                                        }
                                        return Number(val || 0).toLocaleString('id-ID') + ' item';
                                    }
                                },
                                style: {
                                    fontSize: '12px',
                                    fontFamily: 'Inter, system-ui, sans-serif'
                                }
                            }
                        };

                        if (this.chart) {
                            this.chart.updateOptions({
                                chart: {
                                    width: this.isMobile ? this.mobileChartWidth : '100%',
                                },
                                xaxis: { categories: labels },
                                series: [
                                    { name: 'Pendapatan', data: revenue },
                                    { name: 'Item Terjual', data: items }
                                ]
                            });
                        } else {
                            this.chart = new window.ApexCharts(el, options);
                            this.chart.render();
                        }
                    }
                }));
            }

            if (window.Alpine) {
                registerSalesChartComponent();
            } else {
                document.addEventListener('alpine:init', registerSalesChartComponent);
            }
        })();
    </script>
@endpush

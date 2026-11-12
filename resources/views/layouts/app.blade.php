<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sumber Urip - @yield('title', 'Kasir')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        (function() {
            try {
                if (localStorage.getItem('sumber_urip_theme') === 'light') {
                    document.documentElement.classList.add('light');
                } else {
                    document.documentElement.classList.remove('light');
                }
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-full bg-canvas text-ink font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        @php
            $menus = [
                [
                    'name' => 'Dashboard',
                    'route' => 'dashboard',
                    'icon' => '<rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect>'
                ],
                [
                    'name' => 'Kasir (POS)',
                    'route' => 'kasir.index',
                    'icon' => '<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path>'
                ],
                [
                    'name' => 'Transaksi',
                    'route' => 'transaksi.index',
                    'icon' => '<rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line>'
                ],
                [
                    'name' => 'Produk',
                    'route' => 'produk.index',
                    'icon' => '<line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>'
                ],
                [
                    'name' => 'Stok',
                    'route' => 'stok.index',
                    'icon' => '<polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline>'
                ],
            ];
        @endphp

        <!-- Mobile topbar -->
        <header class="md:hidden sticky top-0 z-30 h-16 bg-surface-1 border-b border-hairline flex items-center justify-between px-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-primary flex items-center justify-center text-on-primary font-bold text-xs shadow-xs">
                    SU
                </div>
                <div>
                    <span class="text-sm font-bold text-ink tracking-tight block">Sumber Urip</span>
                    <span class="text-[10px] text-ink-subtle block -mt-0.5">Depo Air & POS</span>
                </div>
            </a>
            <div class="flex items-center gap-1.5">
                <button
                    type="button"
                    @click="$store.theme.toggle()"
                    class="text-ink-subtle hover:text-ink hover:bg-surface-2 transition-colors min-h-[40px] min-w-[40px] flex items-center justify-center rounded-xl cursor-pointer"
                    :aria-label="$store.theme.mode === 'light' ? 'Beralih ke mode gelap' : 'Beralih ke mode terang'"
                >
                    <template x-if="$store.theme.mode === 'light'">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                    </template>
                    <template x-if="$store.theme.mode !== 'light'">
                        <svg class="w-5 h-5 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </template>
                </button>
                <button type="button" class="text-ink-subtle hover:text-ink hover:bg-surface-2 transition-colors min-h-[40px] min-w-[40px] flex items-center justify-center rounded-xl cursor-pointer" @click="sidebarOpen = true" aria-label="Buka menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </header>

        <div class="md:flex md:h-screen">
            <!-- Desktop sidebar -->
            <aside class="hidden md:flex w-64 bg-surface-1 border-r border-hairline flex-col shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-5 border-b border-hairline">
                    <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-on-primary font-bold text-sm shadow-xs">
                        SU
                    </div>
                    <div>
                        <span class="text-base font-bold text-ink tracking-tight block">Sumber Urip</span>
                        <span class="text-xs text-ink-subtle block">Depo Air & POS Hub</span>
                    </div>
                </a>

                <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                    @foreach ($menus as $menu)
                        @php
                            $isActive = request()->routeIs(explode('.', $menu['route'])[0].'.*');
                        @endphp
                        <a href="{{ route($menu['route']) }}" class="{{ $isActive ? 'bg-primary text-on-primary font-semibold shadow-xs' : 'text-ink-muted hover:text-ink hover:bg-surface-2 font-medium' }} flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm transition-all duration-150 min-h-[44px]">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $menu['icon'] !!}</svg>
                            <span>{{ $menu['name'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-hairline p-4 space-y-2">
                    <button
                        type="button"
                        @click="$store.theme.toggle()"
                        class="flex items-center justify-between w-full text-left text-sm font-medium text-ink-muted hover:text-ink hover:bg-surface-2 rounded-xl px-3 py-2.5 transition-colors min-h-[44px] cursor-pointer"
                        :title="$store.theme.mode === 'light' ? 'Beralih ke Mode Gelap' : 'Beralih ke Mode Terang'"
                    >
                        <div class="flex items-center gap-3">
                            <template x-if="$store.theme.mode === 'light'">
                                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                </svg>
                            </template>
                            <template x-if="$store.theme.mode !== 'light'">
                                <svg class="w-5 h-5 text-ink-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                            </template>
                            <span x-text="$store.theme.mode === 'light' ? 'Mode Terang' : 'Mode Gelap'"></span>
                        </div>
                        <div class="w-8 h-4 rounded-full relative transition-colors border border-hairline shrink-0" :class="$store.theme.mode === 'light' ? 'bg-primary' : 'bg-surface-3'">
                            <div class="w-3 h-3 bg-white rounded-full absolute top-0.5 transition-transform duration-200" :class="$store.theme.mode === 'light' ? 'translate-x-4' : 'translate-x-0.5'"></div>
                        </div>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full text-left text-sm font-medium text-ink-muted hover:text-danger hover:bg-danger/10 rounded-xl px-3 py-2.5 transition-colors min-h-[44px] cursor-pointer">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Mobile drawer -->
            <div x-show="sidebarOpen" x-transition.opacity x-cloak class="md:hidden fixed inset-0 bg-black/80 z-40" @click="sidebarOpen = false"></div>
            <div x-show="sidebarOpen" x-cloak class="md:hidden fixed inset-y-0 left-0 z-50 w-72 bg-surface-1 border-r border-hairline flex flex-col transition-transform duration-300" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                <div class="flex items-center justify-between p-5 border-b border-hairline">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-primary flex items-center justify-center text-on-primary font-bold text-xs shadow-xs">
                            SU
                        </div>
                        <div>
                            <span class="text-sm font-bold text-ink tracking-tight block">Sumber Urip</span>
                            <span class="text-[10px] text-ink-subtle block -mt-0.5">Depo Air & POS</span>
                        </div>
                    </div>
                    <button type="button" class="text-ink-subtle hover:text-ink min-h-[40px] min-w-[40px] flex items-center justify-center rounded-lg hover:bg-surface-2 cursor-pointer" @click="sidebarOpen = false" aria-label="Tutup menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                    @foreach ($menus as $menu)
                        @php
                            $isActive = request()->routeIs(explode('.', $menu['route'])[0].'.*');
                        @endphp
                        <a href="{{ route($menu['route']) }}" class="{{ $isActive ? 'bg-primary text-on-primary font-semibold shadow-xs' : 'text-ink-muted hover:text-ink hover:bg-surface-2 font-medium' }} flex items-center gap-3 rounded-xl px-3.5 py-3 text-sm transition-colors min-h-[44px]" @click="sidebarOpen = false">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $menu['icon'] !!}</svg>
                            <span>{{ $menu['name'] }}</span>
                        </a>
                    @endforeach
                </nav>
                <div class="border-t border-hairline p-4 space-y-2">
                    <button
                        type="button"
                        @click="$store.theme.toggle()"
                        class="flex items-center justify-between w-full text-left text-sm font-medium text-ink-muted hover:text-ink hover:bg-surface-2 rounded-xl px-3 py-2.5 transition-colors min-h-[44px] cursor-pointer"
                    >
                        <div class="flex items-center gap-3">
                            <template x-if="$store.theme.mode === 'light'">
                                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                </svg>
                            </template>
                            <template x-if="$store.theme.mode !== 'light'">
                                <svg class="w-5 h-5 text-ink-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                            </template>
                            <span x-text="$store.theme.mode === 'light' ? 'Mode Terang' : 'Mode Gelap'"></span>
                        </div>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full text-left text-sm font-medium text-ink-muted hover:text-danger hover:bg-danger/10 rounded-xl px-3 py-2.5 transition-colors min-h-[44px] cursor-pointer">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main content container -->
            <div class="flex-1 flex flex-col min-w-0 md:overflow-hidden">
                <!-- Desktop Topbar (Matching Image 0) -->
                <header class="hidden md:flex h-16 bg-surface-1 border-b border-hairline items-center justify-between px-6 shrink-0 z-20">
                    <div class="flex items-center gap-3 w-80 lg:w-96">
                        <!-- <div class="relative w-full">
                            <svg class="w-4 h-4 text-ink-subtle absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                            <input
                                type="text"
                                placeholder="Cari transaksi, produk, fitur..."
                                class="w-full bg-surface-2 border border-hairline rounded-xl pl-10 pr-12 py-2 text-xs text-ink placeholder:text-ink-subtle focus:outline-none focus:border-primary transition-all"
                            >
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-ink-subtle bg-surface-1 px-1.5 py-0.5 rounded border border-hairline">⌘K</span>
                        </div> -->
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Theme Toggle Button -->
                        <!-- <button
                            type="button"
                            @click="$store.theme.toggle()"
                            class="w-9 h-9 rounded-xl border border-hairline bg-surface-2 hover:bg-surface-3 flex items-center justify-center text-ink-muted hover:text-ink transition-colors cursor-pointer"
                            :title="$store.theme.mode === 'light' ? 'Beralih ke Mode Gelap' : 'Beralih ke Mode Terang'"
                        >
                            <template x-if="$store.theme.mode === 'light'">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                </svg>
                            </template>
                            <template x-if="$store.theme.mode !== 'light'">
                                <svg class="w-4 h-4 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                            </template>
                        </button>

                        <div class="relative" x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="relative w-9 h-9 rounded-xl border border-hairline bg-surface-2 hover:bg-surface-3 flex items-center justify-center text-ink-muted hover:text-ink transition-colors cursor-pointer"
                                title="Notifikasi"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-primary ring-2 ring-surface-1"></span>
                            </button>
                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition
                                class="absolute right-0 mt-2 w-72 bg-surface-1 border border-hairline rounded-xl shadow-lg p-3 z-50 text-xs"
                            >
                                <div class="font-semibold text-ink pb-2 border-b border-hairline flex items-center justify-between">
                                    <span>Pemberitahuan</span>
                                    <span class="text-xs font-semibold text-primary">Baru</span>
                                </div>
                                <div class="py-2.5 space-y-2">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-2 h-2 rounded-full bg-primary mt-1 shrink-0"></div>
                                        <div>
                                            <p class="font-medium text-ink">Sistem Kasir Aktif</p>
                                            <p class="text-xs text-ink-subtle">Semua modul POS & inventaris siap digunakan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <!-- User Profile Card -->
                        <div class="flex items-center gap-3 pl-3 border-l border-hairline">
                            <div class="w-9 h-9 rounded-full bg-primary/20 text-primary border border-primary/30 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="hidden lg:block text-left">
                                <p class="text-xs font-bold text-ink leading-tight">{{ auth()->user()->name ?? 'Administrator' }}</p>
                                <p class="text-[10px] text-ink-subtle leading-tight capitalize">Kasir & Owner</p>
                            </div>
                        </div>
                    </div>
                </header>
                <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                    @if (session('success'))
                        <div class="mb-4 p-3 rounded-md bg-success/10 border border-success/20 text-success text-sm flex items-center gap-2">
                            <span class="flex-shrink-0">✓</span>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (isset($errors) && $errors->any())
                        <div class="mb-4 p-3 rounded-md bg-danger/10 border border-danger/20 text-danger text-sm">
                            <strong class="font-medium">Terjadi kesalahan:</strong>
                            <ul class="mt-1 list-disc list-inside text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Kasir tunggal di toko ritel fisik. Pengguna mengoperasikan sistem di meja kasir langsung saat melayani pembeli, dengan kebutuhan utama kecepatan input transaksi, respons instan saat memilih barang dan menghitung nominal uang/kembalian, serta kemudahan mencetak atau melihat nota transaksi.

## Product Purpose

Sumber Urip Store adalah aplikasi kasir (Point of Sale / POS) berbasis web yang dirancang untuk mempercepat proses pencatatan penjualan ritel, penghitungan pembayaran dan kembalian, pengelolaan katalog produk, serta pemantauan riwayat transaksi dan stok barang secara terpusat, akurat, dan tanpa friksi.

## Positioning

Sistem kasir ritel yang ringan, cepat, dan fokus pada efisiensi operasional kasir tunggal tanpa kompleksitas berlebih: alur pemesanan langsung dari satu layar kasir, navigasi keyboard/sentuh yang responsif, dan pencatatan transaksi yang reliabel.

## Operating Context

- Dioperasikan langsung di workstation kasir (laptop/desktop monitor di meja kasir toko ritel, dengan dukungan akses mobile responsive).
- Interaksi intensif dengan pembeli yang sedang menunggu giliran bayar; meminimalkan jumlah klik dan jeda sistem.
- Terhubung ke printer nota atau tampilan struk digital, dengan kalkulasi pembayaran tunai secara instan.
- Akses cepat untuk mengelola katalog produk, penambahan stok barang masuk, dan pengecekan rekap omset harian toko.

## Capabilities and Constraints

- **Kasir (POS):** Pemilihan produk via kategori/katalog, penyesuaian kuantitas keranjang, kalkulasi subtotal dan total otomatis, tombol nominal uang cepat, kalkulasi kembalian, cetak/lihat nota pembayaran.
- **Katalog Produk:** Pencatatan produk, pengaturan satuan unit (pcs, galon, dsb.), harga jual, aktivasi pelacakan stok, dan batas minimum stok.
- **Stok & Inventori:** Riwayat pergerakan stok (masuk, keluar, penyesuaian) dan penambahan stok barang.
- **Transaksi:** Riwayat transaksi lengkap, pencarian/filter tanggal, rincian nota, dan opsi pembatalan/void transaksi.
- **Dashboard & Analisis:** Ringkasan penjualan harian, total omset, grafik tren transaksi (ApexCharts).
- **Teknis:** Laravel 12, PHP 8.3+, Tailwind CSS v4, Alpine.js, SQLite/MySQL.
- **Batasan:** Dirancang untuk kasir ritel langsung di toko fisik; tidak mengasumsikan aturan khusus depo air minum atau fitur multi-cabang/delivery yang rumit.

## Brand Commitments

- Nama: Sumber Urip Store (SU)
- Nada suara / Voice: Jelas, ringkas, fungsional, dan dapat diandalkan dalam Bahasa Indonesia.
- Identitas Visual: Palet terstruktur terinspirasi craft software UI (Linear dark aesthetic), kontras tinggi untuk keterbacaan data numerik di meja kasir.

## Evidence on Hand

- Database schema dan migrasi untuk `products`, `transactions`, `transaction_items`, `stock_movements`, dan `users`.
- Komponen antarmuka yang sudah berjalan di `resources/views/cashier`, `products`, `stocks`, `transactions`, dan `dashboard.blade.php`.
- File konfigurasi desain eksisting di `DESIGN.md`.

## Product Principles

1. **Kecepatan di Meja Kasir Adalah Prioritas Utama:** Alur kasir harus meminimalkan jumlah ketukan dan klik; kasir dapat menyelesaikan transaksi dalam hitungan detik.
2. **Keterbacaan dan Ketepatan Angka:** Format mata uang (Rupiah), kuantitas barang, dan kembalian harus selalu terlihat jelas, kontras, dan bebas dari ambiguitas visual.
3. **Penyederhanaan Alur:** Hindari langkah konfirmasi berlebih atau modal popup yang menghambat ritme kerja kasir.
4. **Resiliensi Operasional:** Pencatatan stok dan transaksi harus konsisten; data tidak boleh hilang atau ambigu saat transaksi disimpan.

## Accessibility & Inclusion

- Dukungan kontras tinggi dan mode terang/gelap (dark/light mode) untuk kenyamanan mata kasir dalam berbagai kondisi pencahayaan toko.
- Target klik/tap yang cukup besar (min 40-44px) untuk kemudahan operasional layar sentuh atau mouse kasir.
- Dukungan navigasi keyboard pada form dan tombol aksi utama.

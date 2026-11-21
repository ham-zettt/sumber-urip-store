# Sumber Urip Store
### Sistem Kasir & Pencatatan Penjualan (Point of Sale)

---

## 📌 Ringkasan Proyek

**Sumber Urip Store** adalah aplikasi kasir digital (*Point of Sale* / POS) berbasis web yang dirancang khusus untuk toko ritel dan depo pengisian air galon **Toko Sumber Urip**. 

Aplikasi ini diciptakan untuk menggantikan pembukuan manual (kertas/buku kas) menjadi sistem digital yang cepat, rapi, dan otomatis. Dengan aplikasi ini, kasir dapat melayani pelanggan dalam hitungan detik, menghitung total belanja serta uang kembalian secara tepat, memantau ketersediaan stok barang secara *real-time*, dan pemilik toko dapat langsung melihat ringkasan omzet harian tanpa perlu menghitung nota satu per satu di akhir hari.

---

## 🎯 Mengapa Aplikasi Ini Dibuat?

Operasional toko fisik sering kali menghadapi kendala seperti:
1. **Antrean kasir yang melambat** karena kasir harus menghitung total belanja dan uang kembalian secara manual atau dengan kalkulator terpisah.
2. **Risiko selisih uang** akibat salah hitung nominal kembalian saat toko sedang ramai pembeli.
3. **Stok barang yang tidak terkontrol**, sehingga pemilik toko kerap tidak menyadari ketika barang dagangan (seperti galon kosong, tutup galon, atau air isi ulang) sudah hampir habis.
4. **Rekap penjualan yang memakan waktu**, di mana pemilik toko harus mengumpulkan tumpukan nota fisik untuk mengetahui pendapatan harian atau mingguan.

**Sumber Urip Store menyelesaikan seluruh masalah tersebut** dengan menyatukan alur kasir, pemotongan stok otomatis, dan laporan pendapatan ke dalam satu layar yang mudah digunakan siapa saja.

---

## 🔄 Gambaran Alur Penggunaan (Cara Kerja Singkat)

Aplikasi ini sangat intuitif dan dirancang dengan prinsip **minim klik**:

```
[1. Pilih Produk] ──> [2. Masukkan Pembayaran] ──> [3. Cetak Nota Struk]
        │                         │                           │
        ▼                         ▼                           ▼
Barang masuk ke           Kembalian dihitung         Stok terpotong otomatis &
keranjang otomatis        instan oleh sistem         omzet tercatat di dashboard
```

1. **Kasir Memilih Produk:** Kasir cukup mengklik produk yang dibeli pelanggan pada layar katalog.
2. **Penghitungan Otomatis:** Sistem langsung menampilkan total harga. Kasir memilih metode pembayaran (Tunai atau Transfer) dan memasukkan uang yang diterima. Nilai kembalian langsung muncul seketika.
3. **Transaksi Selesai:** Sekali klik tombol *Bayar*, nota struk siap dicetak atau dilihat, stok barang otomatis terpotong, dan pendapatan hari itu langsung terdata di laporan penjualan.

---

## ✨ Fitur-Fitur Utama

Aplikasi Sumber Urip Store dilengkapi dengan modul-modul penting yang praktis dan langsung tepat sasaran:

### 1. Layar Kasir Cepat (Point of Sale / POS)
- **Katalog Produk Visual:** Produk ditampilkan dalam bentuk kartu interaktif lengkap dengan nama dan harga, sehingga mudah dipilih lewat layar sentuh maupun tetikus (*mouse*).
- **Pencarian Cepat:** Kasir dapat mengetik nama produk untuk menemukan barang dalam sekejap.
- **Keranjang Belanja Real-Time:** Penyesuaian jumlah beli barang (tambah, kurang, hapus) langsung menghitung subtotal dan grand total seketika tanpa perlu memuat ulang (*refresh*) halaman.
- **Kalkulator Kembalian Cerdas:** Sistem otomatis menghitung uang kembalian secara presisi, mencegah kesalahan manusia (*human error*).
- **Tombol Uang Pas & Pecahan Cepat:** Tersedia tombol praktis seperti *Uang Pas*, *Rp 50.000*, *Rp 100.000*, dan pecahan lainnya untuk mempercepat pengisian uang tunai.
- **Dukungan Pembayaran Fleksibel:** Mendukung pembayaran **Tunai** maupun **Transfer Bank**.
- **Cetak Nota Belanja (Struk):** Struk transaksi rapi dapat langsung dicetak ke printer nota fisik maupun disimpan sebagai tanda bukti pembelian.

### 2. Manajemen Katalog & Harga Produk
- **Pendataan Produk Terstruktur:** Memasukkan nama barang, satuan jual (seperti *galon*, *pcs*, *botol*), serta harga jual satuan.
- **Fleksibilitas Pelacakan Stok:** Dapat membedakan produk fisik yang memiliki stok terbatas (misalnya: *Air Galon Baru*, *Tutup Galon*) dengan produk jasa yang tidak memerlukan stok fisik (misalnya: *Jasa Isi Ulang Air*).
- **Perlindungan Data Penjualan:** Produk yang pernah terjual tidak dapat dihapus sembarangan agar catatan pembukuan lama tidak rusak. Jika produk sudah tidak dijual lagi, sistem menyediakannya dalam opsi "nonaktif".

### 3. Kontrol Stok & Inventori Otomatis
- **Pengurangan Stok Otomatis:** Setiap kali terjadi transaksi penjualan di kasir, stok produk bersangkutan langsung berkurang secara otomatis.
- **Pencatatan Barang Masuk (Restock):** Formulir mudah untuk mencatat stok barang yang baru datang dari suplier lengkap dengan tanggal dan catatan pengiriman.
- **Riwayat Pergerakan Stok (Kartu Stok Digital):** Mencatat setiap histori perubahan stok barang (kapan barang masuk, kapan barang keluar karena terjual, atau jika ada pembatalan transaksi).
- **Pengembalian Stok Otomatis:** Jika ada transaksi yang dibatalkan karena pembeli salah beli, stok barang otomatis dikembalikan ke gudang secara utuh.

### 4. Laporan Riwayat Transaksi & Pembatalan (Void)
- **Daftar Transaksi Rinci:** Rekapitulasi seluruh transaksi yang tersusun urut dari yang terbaru.
- **Nomor Faktur Terstruktur:** Setiap nota memiliki kode unik harian resmi (contoh: `INV-20261118-0001`) yang memudahkan penelusuran.
- **Filter Tanggal & Pencarian Nota:** Pemilik toko dapat memfilter penjualan hari ini, kemarin, atau rentang tanggal tertentu untuk melihat total pendapatan kotor pada periode tersebut.
- **Detail Transaksi & Cetak Ulang:** Nota transaksi lama dapat dibuka kembali dan dicetak ulang kapan saja jika pelanggan meminta struk susulan.
- **Fitur Pembatalan Aman (*Void Transaction*):** Membatalkan transaksi salah dengan konfirmasi aman dan sistem otomatis mengembalikan stok barang.

### 5. Dashboard Analisis & Pantauan Bisnis
- **Kartu Ringkasan Hari Ini:** Menampilkan tiga angka penting setiap kali aplikasi dibuka:
  - *Total Uang Masuk / Omzet Hari Ini* (Rp).
  - *Jumlah Transaksi Berhasil*.
  - *Total Jumlah Barang Terjual*.
- **Grafik Tren Penjualan Interaktif:** Grafik visual yang memperlihatkan tren naik-turun penjualan harian (bisa diatur untuk melihat performa **7 hari terakhir** atau **30 hari terakhir**).
- **Daftar Produk Terlaris (*Top Products*):** Memberikan informasi produk mana yang paling banyak diminati oleh pelanggan toko.
- **Peringatan Dini Stok Menipis (*Low Stock Alert*):** Memberi tanda peringatan jika ada barang yang jumlahnya sudah menyentuh batas minimum, sehingga pemilik toko tahu kapan harus memesan kembali ke suplier sebelum stok habis.

### 6. Keamanan & Akses Terkendali
- **Sistem Masuk Aman (Login Kasir/Admin):** Akses aplikasi dilindungi kata sandi agar data penjualan dan keuangan toko tidak bisa diakses sembarang orang dari luar.

---

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan teknologi web modern berstandar industri. Berikut penjelasannya dengan bahasa yang mudah dipahami:

| Bagian Sistem | Teknologi yang Digunakan | Penjelasan Sederhana & Manfaatnya |
| :--- | :--- | :--- |
| **Mesin Utama (Backend)** | **Laravel 13 (PHP 8.3+)** | Berfungsi sebagai "otak" di balik layar yang memproses pesanan, menghitung keuangan, menjaga keamanan data, serta memastikan semua transaksi tersimpan dengan akurat tanpa takut data hilang. |
| **Tampilan & Desain (UI/UX)** | **Tailwind CSS v4** | Berfungsi sebagai "pakaian" atau desain visual. Menjadikan tampilan aplikasi tampak elegan, modern, bersih, dengan kontras tulisan yang jelas dan sangat nyaman di mata kasir meski dilihat berjam-jam di meja kasir. |
| **Kenyamanan Kasir (Frontend)** | **Alpine.js** | Berfungsi membuat halaman kasir bergerak responsif dan lincah. Menambah barang ke keranjang dan menghitung kembalian terjadi seketika tanpa ada jeda layar memuat ulang (*loading*). |
| **Visualisasi Grafik** | **ApexCharts** | Berfungsi menerjemahkan data penjualan harian menjadi grafik batang atau garis yang cantik, jelas, dan mudah dimengerti pemilik toko dalam sekali lihat. |
| **Penyimpanan Data (Database)** | **MySQL / SQLite** | Berfungsi sebagai "buku brankas digital" yang menyimpan seluruh daftar harga barang, riwayat ribuan transaksi penjualan, pergerakan stok, dan data akun secara permanen serta aman. |

---

## 💡 Nilai Tambah & Keunggulan bagi Pemilik Toko

1. **Sangat Mudah Digunakan (Ramah Orang Awam)**  
   Antarmuka dibuat sesederhana mungkin tanpa menu-menu rumit yang membingungkan. Kasir baru pun dapat langsung mengoperasikannya dalam hitungan menit tanpa pelatihan berbelit-belit.
2. **Pelayanan Lebih Cepat**  
   Alur kasir satu halaman (*single screen*) meminimalkan waktu transaksi sehingga antrean pembeli di toko air galon/ritel tidak menumpuk.
3. **Mencegah Kerugian Toko**  
   Penghitungan nominal uang dan kembalian yang otomatis meniadakan kerugian akibat kasir salah memberi kembalian.
4. **Stok Selalu Akurat**  
   Tidak ada lagi kebingungan berapa galon yang tersisa di toko karena setiap botol yang keluar atau masuk selalu tercatat secara otomatis.
5. **Keputusan Usaha Lebih Tepat**  
   Dengan adanya grafik omzet dan daftar barang terlaris, pemilik toko dapat mengetahui kapan waktu toko paling ramai dan produk apa yang perlu diperbanyak stoknya.

---

## 📋 Kesimpulan

**Sumber Urip Store** adalah solusi kasir digital yang ringkas, kokoh, dan berdaya guna tinggi. Aplikasi ini berhasil menjembatani kebutuhan operasional harian toko fisik—mulai dari pelayanan cepat di meja kasir, pengawasan stok barang, hingga laporan omzet harian—dalam satu paket aplikasi web yang modern, aman, dan mudah digunakan oleh siapa saja.

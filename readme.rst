############################
FastPrint Product Management
############################

Sistem manajemen produk untuk FastPrint menggunakan CodeIgniter 3.

*******************
Teknologi Digunakan
*******************

* PHP 7.4+
* CodeIgniter 3
* MySQL Database
* Bootstrap 5
* DataTables
* Select2
* Font Awesome

************
Requirements
************

* PHP 7.4 atau lebih tinggi
* MySQL
* Web Server (Apache/Nginx)

**********
Instalasi
**********

1. Clone repository::

    git clone https://github.com/taufikhdyt01/fastprint_test.git
    cd fastprint_test

2. Import database:

   * Buat database baru dengan nama ``fastprint_db``
   * Import file SQL yang ada di folder ``database/fastprint_db.sql``

3. Konfigurasi database:
   
   Buka file ``application/config/database.php`` dan sesuaikan konfigurasi::

    $db['default'] = array(
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'fastprint_db'
    );

4. Konfigurasi base URL:
   
   Buka file ``application/config/config.php`` dan sesuaikan::

    $config['base_url'] = 'http://localhost/fastprint_test/';

******************
Struktur Database
******************

1. Tabel ``produk``::

    CREATE TABLE produk (
        id_produk VARCHAR(100) PRIMARY KEY,
        nama_produk VARCHAR(255),
        harga INT,
        kategori_id INT,
        status_id INT,
        FOREIGN KEY (kategori_id) REFERENCES kategori(id_kategori),
        FOREIGN KEY (status_id) REFERENCES status(id_status)
    );

2. Tabel ``kategori``::

    CREATE TABLE kategori (
        id_kategori INT PRIMARY KEY AUTO_INCREMENT,
        nama_kategori VARCHAR(100)
    );

3. Tabel ``status``::

    CREATE TABLE status (
        id_status INT PRIMARY KEY AUTO_INCREMENT,
        nama_status VARCHAR(100)
    );

***************
Fitur Aplikasi
***************

1. Integrasi API
---------------
* Mengambil data dari API FastPrint melalui tombol "Sync Data API"
* Autentikasi dinamis menggunakan username dari server dan password yang mengikuti waktu server
* Menyimpan data produk ke database lokal dengan penanganan duplikasi
* Menampilkan status sync data melalui flash message

2. Manajemen Produk
------------------
* **Daftar Produk**: Menampilkan semua produk dengan fitur pencarian dan pengurutan
* **Filter Status**: Menampilkan produk yang bisa dijual
* **Tambah Produk**: Form untuk menambah produk baru
* **Edit Produk**: Mengubah data produk yang sudah ada
* **Hapus Produk**: Menghapus produk dengan konfirmasi

3. Validasi
----------
* Validasi nama produk (wajib diisi)
* Validasi harga (wajib diisi, harus berupa angka)
* Validasi kategori dan status (wajib dipilih)

***************
Cara Penggunaan
***************

Sync Data API
------------
1. Akses aplikasi di: `http://localhost/fastprint_test`
2. Klik tombol "Sync Data API" di pojok kanan atas
3. Sistem akan:
   * Mengambil username valid dari server
   * Generate password sesuai format dan waktu
   * Mengambil data dari API
   * Menyimpan/mengupdate data ke database lokal
   * Menampilkan status hasil sync

Manajemen Produk
---------------
1. Melihat Daftar Produk:
   
   * Akses: ``http://localhost/fastprint_test/products``
   * Gunakan fitur search untuk mencari produk
   * Klik header tabel untuk mengurutkan data

2. Filter Produk Bisa Dijual:
   
   * Klik menu "Produk Bisa Dijual"
   * Atau akses: ``http://localhost/fastprint_test/products/sellable``

3. Tambah Produk:
   
   * Klik tombol "Tambah Produk"
   * Isi form dengan lengkap
   * Klik "Simpan"

4. Edit Produk:
   
   * Klik icon edit (pensil) pada produk yang ingin diubah
   * Update informasi yang diperlukan
   * Klik "Update"

5. Hapus Produk:
   
   * Klik icon hapus (tempat sampah)
   * Konfirmasi penghapusan

*************
Flow Aplikasi
*************

1. **Sync Data API**
   * Pengguna dapat melakukan sync data kapan saja melalui UI
   * Sistem mengambil username valid dari server
   * Password di-generate secara otomatis sesuai waktu
   * Data baru akan ditambahkan, data existing akan diupdate
   * Status sync ditampilkan melalui flash message

2. **Manajemen Data**
   
   * Data ditampilkan dalam format tabel yang interaktif
   * Pengguna dapat melakukan operasi CRUD
   * Validasi form mencegah input data yang tidak valid

3. **Filter dan Pencarian**
   
   * Pencarian real-time menggunakan DataTables
   * Filter khusus untuk produk yang bisa dijual
   * Pengurutan data berdasarkan kolom

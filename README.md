# Europe Sales Dashboard

Dashboard Laravel untuk melihat ringkasan penjualan, analisis drill-down, dan simulasi what-if dari tabel `sales_transactions`.

## Persyaratan

- PHP 8.2 atau lebih baru dengan ekstensi `pdo_mysql`
- Composer
- MySQL atau MariaDB

## Menjalankan aplikasi

1. Pastikan database `europe_sales_db` tersedia di MySQL.
2. Salin `.env.example` menjadi `.env` jika file `.env` belum ada.
3. Sesuaikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env` dengan konfigurasi MySQL lokal Anda. Contoh bawaan memakai `127.0.0.1:3306`, database `europe_sales_db`, dan pengguna `root` tanpa kata sandi.
4. Jalankan:

   ```sh
   composer install
   php artisan key:generate
   php artisan config:clear
   php artisan migrate
   php artisan serve
   ```

5. Buka `http://127.0.0.1:8000`.

Migrasi menyiapkan tabel Laravel seperti sesi, cache, dan antrean. Migrasi juga membuat `sales_transactions` hanya jika tabel itu belum ada. Jika tabel penjualan sudah ada, migrasi membiarkannya beserta datanya tetap utuh. Tabel baru akan kosong sampai data penjualan dimasukkan.

Tabel `sales_transactions` perlu memiliki kolom `region`, `country`, `item_type`, `sales_channel`, `order_priority`, `order_date`, `order_id`, `ship_date`, `units_sold`, `unit_price`, `unit_cost`, `total_revenue`, `total_cost`, dan `total_profit`.

## Query SQL dashboard

Berikut query MySQL yang mencerminkan agregasi utama di dashboard. Kolaborator dapat menjalankannya langsung di database `europe_sales_db`. Untuk menerapkan filter dashboard, tambahkan kondisi yang diperlukan ke bagian `WHERE`, seperti `country = 'Germany'`, `item_type = 'Beverages'`, atau rentang tanggal pada `order_date`.

### Ringkasan KPI

```sql
SELECT
    COALESCE(SUM(total_revenue), 0) AS revenue,
    COALESCE(SUM(total_cost), 0) AS cost,
    COALESCE(SUM(total_profit), 0) AS profit,
    COALESCE(SUM(units_sold), 0) AS units_sold
FROM sales_transactions;
```

### Tren penjualan bulanan

```sql
SELECT
    YEAR(order_date) AS year,
    MONTH(order_date) AS month,
    SUM(total_revenue) AS revenue,
    SUM(total_cost) AS cost,
    SUM(total_profit) AS profit
FROM sales_transactions
GROUP BY YEAR(order_date), MONTH(order_date)
ORDER BY YEAR(order_date), MONTH(order_date);
```

### Profit per negara

```sql
SELECT
    country,
    SUM(total_revenue) AS revenue,
    SUM(total_cost) AS cost,
    SUM(total_profit) AS profit,
    SUM(units_sold) AS units
FROM sales_transactions
GROUP BY country
ORDER BY profit DESC;
```

### Profit per tipe produk dan kanal penjualan

```sql
SELECT
    item_type,
    SUM(total_profit) AS profit
FROM sales_transactions
GROUP BY item_type
ORDER BY profit DESC;

SELECT
    sales_channel,
    SUM(total_profit) AS profit
FROM sales_transactions
GROUP BY sales_channel;
```

Untuk mengimpor CSV/TSV, termasuk angka berformat `$4,080.50`, jalankan:

```sh
php artisan sales:import "C:\path\to\sales.tsv" --replace
```

Opsi `--replace` hanya mengganti isi tabel transaksi, dilakukan dalam satu transaksi database setelah seluruh file lolos validasi.

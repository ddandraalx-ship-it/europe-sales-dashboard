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

Query MySQL berikut menggabungkan KPI, tren bulanan, dan ringkasan berdasarkan negara, tipe produk, serta kanal penjualan dalam satu hasil. Sesuaikan atau tambahkan kondisi `AND` di bagian `WHERE` untuk memfilter data.

```sql
WITH filtered AS (
    SELECT
        order_date,
        country,
        item_type,
        sales_channel,
        units_sold,
        total_revenue,
        total_cost,
        total_profit
    FROM sales_transactions
    WHERE 1 = 1
        -- AND country = 'Germany'
        -- AND item_type = 'Beverages'
        -- AND sales_channel = 'Online'
        -- AND order_date >= '2026-01-01'
        -- AND order_date <= '2026-12-31'
),
aggregates AS (
    SELECT
        'KPI' AS breakdown,
        'Semua transaksi' AS label,
        COALESCE(SUM(total_revenue), 0) AS revenue,
        COALESCE(SUM(total_cost), 0) AS cost,
        COALESCE(SUM(total_profit), 0) AS profit,
        COALESCE(SUM(units_sold), 0) AS units_sold
    FROM filtered

    UNION ALL

    SELECT
        'Bulanan',
        DATE_FORMAT(order_date, '%Y-%m'),
        SUM(total_revenue),
        SUM(total_cost),
        SUM(total_profit),
        SUM(units_sold)
    FROM filtered
    GROUP BY YEAR(order_date), MONTH(order_date)

    UNION ALL

    SELECT
        'Negara',
        country,
        SUM(total_revenue),
        SUM(total_cost),
        SUM(total_profit),
        SUM(units_sold)
    FROM filtered
    GROUP BY country

    UNION ALL

    SELECT
        'Produk',
        item_type,
        SUM(total_revenue),
        SUM(total_cost),
        SUM(total_profit),
        SUM(units_sold)
    FROM filtered
    GROUP BY item_type

    UNION ALL

    SELECT
        'Kanal',
        sales_channel,
        SUM(total_revenue),
        SUM(total_cost),
        SUM(total_profit),
        SUM(units_sold)
    FROM filtered
    GROUP BY sales_channel
)
SELECT
    breakdown,
    label,
    revenue,
    cost,
    profit,
    units_sold,
    CASE
        WHEN revenue > 0 THEN ROUND(profit / revenue * 100, 2)
        ELSE 0
    END AS margin_percent
FROM aggregates
ORDER BY breakdown, label;
```

Untuk mengimpor CSV/TSV, termasuk angka berformat `$4,080.50`, jalankan:

```sh
php artisan sales:import "C:\path\to\sales.tsv" --replace
```

Opsi `--replace` hanya mengganti isi tabel transaksi, dilakukan dalam satu transaksi database setelah seluruh file lolos validasi.

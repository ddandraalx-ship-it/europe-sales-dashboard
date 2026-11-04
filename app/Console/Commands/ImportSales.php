<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SplFileObject;

class ImportSales extends Command
{
    protected $signature = 'sales:import
        {file : Path to a CSV or tab-separated sales data file}
        {--replace : Replace existing sales transactions after the file passes validation}';

    protected $description = 'Import sales data from CSV or tab-separated file';

    private const COLUMNS = [
        'region',
        'country',
        'item_type',
        'sales_channel',
        'order_priority',
        'order_date',
        'order_id',
        'ship_date',
        'units_sold',
        'unit_price',
        'unit_cost',
        'total_revenue',
        'total_cost',
        'total_profit',
    ];

    public function handle(): int
    {
        $path = realpath($this->argument('file'));

        if ($path === false || ! is_file($path) || ! is_readable($path)) {
            $this->error('File tidak ditemukan atau tidak dapat dibaca.');

            return self::FAILURE;
        }

        if (! Schema::hasTable('sales_transactions')) {
            $this->error('Tabel sales_transactions belum tersedia. Jalankan php artisan migrate terlebih dahulu.');

            return self::FAILURE;
        }

        $file = new SplFileObject($path, 'r');
        $firstLine = $file->fgets();
        $delimiter = substr_count($firstLine, "\t") > 0 ? "\t" : ',';
        $file->rewind();

        $rows = [];
        $orderIds = [];
        $lineNumber = 0;

        while (! $file->eof()) {
            $values = $file->fgetcsv($delimiter, '"', '\\');
            $lineNumber++;

            if ($values === false || $values === [null] || $values === [null, null]) {
                continue;
            }

            $values[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $values[0]);

            if ($lineNumber === 1 && strtolower(trim($values[0])) === 'region') {
                continue;
            }

            if (count($values) !== count(self::COLUMNS)) {
                $this->error("Baris {$lineNumber} memiliki " . count($values) . ' kolom; seharusnya 14.');

                return self::FAILURE;
            }

            $row = array_combine(self::COLUMNS, array_map(
                static fn ($value) => trim((string) $value),
                $values
            ));

            foreach (['region', 'country', 'item_type', 'sales_channel', 'order_priority'] as $column) {
                if ($row[$column] === '') {
                    $this->error("Kolom {$column} kosong pada baris {$lineNumber}.");

                    return self::FAILURE;
                }
            }

            foreach (['order_date', 'ship_date'] as $column) {
                $date = Carbon::createFromFormat('!Y-m-d', $row[$column]);

                if ($date === false || $date->format('Y-m-d') !== $row[$column]) {
                    $this->error("Format tanggal {$column} tidak valid pada baris {$lineNumber}.");

                    return self::FAILURE;
                }
            }

            foreach ([
                'order_id',
                'units_sold',
                'unit_price',
                'unit_cost',
                'total_revenue',
                'total_cost',
                'total_profit',
            ] as $column) {
                $number = str_replace([',', '$', ' ', "\u{00A0}"], '', $row[$column]);

                if ($number === '' || ! is_numeric($number)) {
                    $this->error("Nilai {$column} tidak valid pada baris {$lineNumber}.");

                    return self::FAILURE;
                }

                $row[$column] = $number;
            }

            $row['order_id'] = (int) $row['order_id'];
            $row['units_sold'] = (int) $row['units_sold'];

            if ($row['order_id'] <= 0 || $row['units_sold'] < 0) {
                $this->error("Order ID atau jumlah unit tidak valid pada baris {$lineNumber}.");

                return self::FAILURE;
            }

            if (isset($orderIds[$row['order_id']])) {
                $this->error("Order ID duplikat {$row['order_id']} pada baris {$lineNumber}.");

                return self::FAILURE;
            }

            $orderIds[$row['order_id']] = true;
            $rows[] = $row;
        }

        if ($rows === []) {
            $this->error('File tidak memiliki transaksi yang dapat diimpor.');

            return self::FAILURE;
        }

        $timestamp = now();
        $rows = array_map(
            static fn (array $row) => $row + [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            $rows
        );
        $updateColumns = array_merge(array_values(array_diff(self::COLUMNS, ['order_id'])), ['updated_at']);

        DB::transaction(function () use ($rows, $updateColumns): void {
            if ($this->option('replace')) {
                DB::table('sales_transactions')->delete();
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('sales_transactions')->upsert($chunk, ['order_id'], $updateColumns);
            }
        });

        $this->info(number_format(count($rows)) . ' transaksi berhasil diimpor ke database ' . DB::connection()->getDatabaseName() . '.');

        if ($this->option('replace')) {
            $this->line('Data lama pada tabel sales_transactions telah diganti dengan isi file yang divalidasi.');
        }

        return self::SUCCESS;
    }
}

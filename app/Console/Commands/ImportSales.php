<?php

namespace App\Console\Commands;

use App\Services\SalesDataUndo;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SplFileObject;

class ImportSales extends Command
{
    protected $signature = 'sales:import
        {file : Path to a CSV or tab-separated sales data file}
        {--replace : Replace existing sales transactions after the file passes validation}
        {--validate-only : Validate and report without modifying the database}';

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
        $header = $file->fgetcsv($delimiter, '"', '\\');

        if ($header === false || $header === [null]) {
            $this->error('File kosong. Total data terbaca: 0 baris; valid: 0; tidak valid: 0. Baris pertama harus berisi header 14 kolom yang diwajibkan.');

            return self::FAILURE;
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        $normalizedHeader = array_map(
            static fn ($column) => strtolower(trim((string) $column)),
            $header
        );

        if ($normalizedHeader !== self::COLUMNS) {
            $missing = array_values(array_diff(self::COLUMNS, $normalizedHeader));
            $unexpected = array_values(array_diff($normalizedHeader, self::COLUMNS));
            $details = [];

            if ($missing !== []) {
                $details[] = 'kolom wajib yang hilang: '.implode(', ', $missing);
            }

            if ($unexpected !== []) {
                $details[] = 'kolom tidak dikenal: '.implode(', ', array_filter($unexpected, static fn ($column) => $column !== ''));
            }

            if ($details === []) {
                $details[] = 'urutan kolom tidak sesuai';
            }

            $totalRecords = 0;

            while (! $file->eof()) {
                $values = $file->fgetcsv($delimiter, '"', '\\');

                if ($values !== false && $values !== [null] && $values !== [null, null]) {
                    $totalRecords++;
                }
            }

            $this->error(
                'Header file tidak sesuai. Urutan yang diwajibkan: '.implode(', ', self::COLUMNS)
                .'. Masalah: '.implode('; ', $details).'. Total data terbaca: '.number_format($totalRecords)
                .' baris; valid: 0; tidak valid: '.number_format($totalRecords).'.'
            );

            return self::FAILURE;
        }

        $rows = [];
        $orderIds = [];
        $validationErrors = [];
        $totalRecords = 0;
        $lineNumber = 1;

        while (! $file->eof()) {
            $values = $file->fgetcsv($delimiter, '"', '\\');
            $lineNumber++;

            if ($values === false || $values === [null] || $values === [null, null]) {
                continue;
            }

            $totalRecords++;

            if (count($values) !== count(self::COLUMNS)) {
                $validationErrors[] = "Baris {$lineNumber} memiliki ".count($values).' kolom; seharusnya 14.';

                continue;
            }

            $row = array_combine(self::COLUMNS, array_map(
                static fn ($value) => trim((string) $value),
                $values
            ));
            $rowErrors = [];

            foreach (['region', 'country', 'item_type', 'sales_channel', 'order_priority'] as $column) {
                if ($row[$column] === '') {
                    $rowErrors[] = "Kolom {$column} kosong pada baris {$lineNumber}.";
                }
            }

            foreach (['order_date', 'ship_date'] as $column) {
                try {
                    $date = Carbon::createFromFormat('!Y-m-d', $row[$column]);
                } catch (InvalidFormatException) {
                    $date = false;
                }

                if ($date === false || $date->format('Y-m-d') !== $row[$column]) {
                    $rowErrors[] = "Format tanggal {$column} tidak valid pada baris {$lineNumber}.";
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
                    $rowErrors[] = "Nilai {$column} tidak valid pada baris {$lineNumber}.";

                    continue;
                }

                $row[$column] = $number;
            }

            foreach (['order_id', 'units_sold'] as $column) {
                if (! isset($row[$column]) || ! is_numeric($row[$column])) {
                    continue;
                }

                if (! preg_match('/^\d+$/D', $row[$column])) {
                    $rowErrors[] = "Kolom {$column} harus berupa bilangan bulat pada baris {$lineNumber}.";

                    continue;
                }

                $integerText = ltrim($row[$column], '0');
                $integerText = $integerText === '' ? '0' : $integerText;
                $integer = filter_var($integerText, FILTER_VALIDATE_INT, [
                    'options' => [
                        'min_range' => $column === 'order_id' ? 1 : 0,
                        'max_range' => $column === 'units_sold' ? 4294967295 : PHP_INT_MAX,
                    ],
                ]);

                if ($integer === false) {
                    $rowErrors[] = "Nilai {$column} di luar batas yang didukung pada baris {$lineNumber}.";

                    continue;
                }

                $row[$column] = $integer;
            }

            foreach (['unit_price', 'unit_cost', 'total_revenue', 'total_cost', 'total_profit'] as $column) {
                if (! isset($row[$column]) || ! is_numeric($row[$column])) {
                    continue;
                }

                if (! preg_match('/^-?(?:\d+(?:\.\d{1,2})?|\.\d{1,2})$/D', $row[$column])) {
                    $rowErrors[] = "Kolom {$column} harus berupa angka dengan maksimal 2 desimal pada baris {$lineNumber}.";

                    continue;
                }

                if (in_array($column, ['unit_price', 'unit_cost', 'total_revenue', 'total_cost'], true) && (float) $row[$column] < 0) {
                    $rowErrors[] = "Kolom {$column} tidak boleh bernilai negatif pada baris {$lineNumber}.";

                    continue;
                }

                $integerPart = ltrim(explode('.', ltrim($row[$column], '-'))[0], '0');
                $maxIntegerDigits = in_array($column, ['unit_price', 'unit_cost'], true) ? 13 : 16;

                if (strlen($integerPart) > $maxIntegerDigits) {
                    $rowErrors[] = "Nilai {$column} melebihi kapasitas kolom database pada baris {$lineNumber}.";
                }
            }

            if (isset($row['order_id']) && is_int($row['order_id'])) {
                if (isset($orderIds[$row['order_id']])) {
                    $rowErrors[] = "Order ID duplikat {$row['order_id']} pada baris {$lineNumber}.";
                }

                $orderIds[$row['order_id']] = true;
            }

            if ($rowErrors !== []) {
                array_push($validationErrors, ...$rowErrors);
            } else {
                $rows[] = $row;
            }
        }

        if ($validationErrors !== []) {
            $this->error(
                'Validasi gagal. File belum siap diproses. Total data terbaca: '.number_format($totalRecords)
                .' baris; valid: '.number_format(count($rows))
                .'; tidak valid: '.number_format($totalRecords - count($rows)).'.'
            );

            foreach (array_slice($validationErrors, 0, 20) as $error) {
                $this->line('- '.$error);
            }

            if (count($validationErrors) > 20) {
                $this->line('- '.number_format(count($validationErrors) - 20).' kesalahan lainnya tidak ditampilkan.');
            }

            return self::FAILURE;
        }

        if ($rows === []) {
            $this->error('Total data terbaca: 0 baris. File tidak memiliki transaksi yang dapat diimpor.');

            return self::FAILURE;
        }

        if ($this->option('validate-only')) {
            $this->info(
                'File siap diproses. Total data terbaca: '.number_format($totalRecords)
                .' baris; valid: '.number_format(count($rows)).'; tidak valid: 0.'
            );

            return self::SUCCESS;
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
            SalesDataUndo::capture();

            if ($this->option('replace')) {
                DB::table('sales_transactions')->delete();
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('sales_transactions')->upsert($chunk, ['order_id'], $updateColumns);
            }
        });

        $this->info(
            'Total data terbaca: '.number_format($totalRecords)
            .' baris; valid: '.number_format(count($rows))
            .'; tidak valid: 0. '
            .number_format(count($rows)).' transaksi berhasil diimpor ke database '.DB::connection()->getDatabaseName().'.'
        );

        if ($this->option('replace')) {
            $this->line('Data lama pada tabel sales_transactions telah diganti dengan isi file yang divalidasi.');
        }

        return self::SUCCESS;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use App\Services\SalesDataUndo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class EtlController extends Controller
{
    private const FIELDS = [
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

    private const FIELD_LABELS = [
        'region' => 'Region',
        'country' => 'Country',
        'item_type' => 'Item type',
        'sales_channel' => 'Sales channel',
        'order_priority' => 'Order priority',
        'order_date' => 'Order date',
        'order_id' => 'Order ID',
        'ship_date' => 'Ship date',
        'units_sold' => 'Units sold',
        'unit_price' => 'Unit price',
        'unit_cost' => 'Unit cost',
        'total_revenue' => 'Total revenue',
        'total_cost' => 'Total cost',
        'total_profit' => 'Total profit',
    ];

    public function index()
    {
        $transactionCount = DB::table('sales_transactions')->count();
        $latestOrderDate = DB::table('sales_transactions')->max('order_date');
        $transactions = SalesTransaction::query()
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(10);
        $canUndo = DB::table('sales_data_undo')->where('id', 1)->exists();
        $fieldLabels = self::FIELD_LABELS;

        return view('etl', compact('transactionCount', 'latestOrderDate', 'transactions', 'canUndo', 'fieldLabels'));
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,tsv,txt', 'max:10240'],
            'replace' => ['nullable', 'boolean'],
        ]);

        $arguments = [
            'file' => $validated['file']->getRealPath(),
            '--replace' => $request->boolean('replace'),
        ];

        $exitCode = Artisan::call('sales:import', $arguments);
        $output = trim(Artisan::output());

        if ($exitCode !== 0) {
            return back()->withErrors(['file' => $output ?: 'File gagal diproses.'])->withInput();
        }

        return redirect()->route('etl')->with('status', $output);
    }

    public function edit(SalesTransaction $transaction)
    {
        return view('etl-edit', compact('transaction'));
    }

    public function previewCreate(Request $request)
    {
        $data = $this->validatedTransaction($request);

        return $this->stageChange('create', null, $data, null);
    }

    public function previewUpdate(Request $request, SalesTransaction $transaction)
    {
        $data = $this->validatedTransaction($request, $transaction);

        return $this->stageChange('update', $transaction->id, $data, $this->transactionValues($transaction));
    }

    public function previewDelete(SalesTransaction $transaction)
    {
        return $this->stageChange('delete', $transaction->id, null, $this->transactionValues($transaction));
    }

    public function confirm()
    {
        $pending = session('etl_pending_change');

        abort_unless(is_array($pending) && isset($pending['token'], $pending['action']), 404);

        return view('etl-confirm', [
            'pending' => $pending,
            'fieldLabels' => self::FIELD_LABELS,
        ]);
    }

    public function applyChange(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'confirmation' => ['accepted'],
        ]);

        $pending = session('etl_pending_change');

        if (! is_array($pending) || ! hash_equals($pending['token'] ?? '', $request->string('token')->toString())) {
            throw ValidationException::withMessages([
                'confirmation' => 'Konfirmasi sudah kedaluwarsa. Periksa kembali data sebelum mengirim ulang.',
            ]);
        }

        DB::transaction(function () use ($pending): void {
            if ($pending['action'] === 'create') {
                if (DB::table('sales_transactions')->where('order_id', $pending['data']['order_id'])->exists()) {
                    throw ValidationException::withMessages([
                        'order_id' => 'Order ID tersebut sudah digunakan. Muat ulang data sebelum mencoba lagi.',
                    ]);
                }

                SalesDataUndo::capture();
                SalesTransaction::create($pending['data']);

                return;
            }

            $transaction = SalesTransaction::query()
                ->whereKey($pending['id'])
                ->lockForUpdate()
                ->first();

            if (! $transaction || $this->transactionValues($transaction) !== $pending['old']) {
                throw ValidationException::withMessages([
                    'record' => 'Data transaksi sudah berubah sejak ditinjau. Buka kembali data dan ulangi konfirmasi.',
                ]);
            }

            if ($pending['action'] === 'update') {
                if (DB::table('sales_transactions')
                    ->where('order_id', $pending['data']['order_id'])
                    ->where('id', '!=', $transaction->id)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'order_id' => 'Order ID tersebut sudah digunakan oleh transaksi lain.',
                    ]);
                }

                SalesDataUndo::capture();
                $transaction->update($pending['data']);

                return;
            }

            if ($pending['action'] === 'delete') {
                SalesDataUndo::capture();
                $transaction->delete();
            }
        });

        session()->forget('etl_pending_change');

        return redirect()->route('etl')->with('status', match ($pending['action']) {
            'create' => 'Transaksi baru berhasil ditambahkan.',
            'update' => 'Transaksi berhasil diperbarui.',
            'delete' => 'Transaksi berhasil dihapus.',
        });
    }

    public function cancelChange()
    {
        session()->forget('etl_pending_change');

        return redirect()->route('etl')->with('status', 'Perubahan dibatalkan dan tidak disimpan.');
    }

    public function undo()
    {
        if (! SalesDataUndo::restore()) {
            return redirect()->route('etl')->withErrors([
                'undo' => 'Tidak ada perubahan ETL yang dapat dibatalkan.',
            ]);
        }

        session()->forget('etl_pending_change');

        return redirect()->route('etl')->with('status', 'Perubahan ETL terakhir berhasil dikembalikan.');
    }

    private function validatedTransaction(Request $request, ?SalesTransaction $transaction = null): array
    {
        $orderIdRule = Rule::unique('sales_transactions', 'order_id');

        if ($transaction) {
            $orderIdRule->ignore($transaction->id);
        }

        $validated = $request->validate([
            'region' => ['required', 'string', 'max:80'],
            'country' => ['required', 'string', 'max:120'],
            'item_type' => ['required', 'string', 'max:80'],
            'sales_channel' => ['required', 'string', 'max:20'],
            'order_priority' => ['required', 'string', 'size:1'],
            'order_date' => ['required', 'date_format:Y-m-d'],
            'order_id' => ['required', 'integer', 'min:1', $orderIdRule],
            'ship_date' => ['required', 'date_format:Y-m-d'],
            'units_sold' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'unit_price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'unit_cost' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'total_revenue' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'total_cost' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'total_profit' => ['required', 'numeric', 'decimal:0,2'],
        ]);

        return array_intersect_key($validated, array_flip(self::FIELDS));
    }

    private function transactionValues(SalesTransaction $transaction): array
    {
        return array_combine(
            self::FIELDS,
            array_map(
                static fn (string $field) => $transaction->getRawOriginal($field),
                self::FIELDS
            )
        );
    }

    private function stageChange(string $action, ?int $id, ?array $data, ?array $old)
    {
        session()->put('etl_pending_change', [
            'action' => $action,
            'id' => $id,
            'data' => $data,
            'old' => $old,
            'token' => Str::random(64),
        ]);

        return redirect()->route('etl.confirm');
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use JsonException;

class SalesDataUndo
{
    /**
     * Capture the current dataset inside the mutation's database transaction.
     *
     * @throws JsonException
     */
    public static function capture(): void
    {
        $rows = DB::table('sales_transactions')
            ->orderBy('id')
            ->get()
            ->map(static fn (object $row): array => (array) $row)
            ->all();

        DB::table('sales_data_undo')->updateOrInsert(
            ['id' => 1],
            [
                'payload' => json_encode($rows, JSON_THROW_ON_ERROR),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Restore the most recent snapshot. Returns false when no undo is available.
     *
     * @throws JsonException
     */
    public static function restore(): bool
    {
        return DB::transaction(function (): bool {
            $snapshot = DB::table('sales_data_undo')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            if (! $snapshot) {
                return false;
            }

            $rows = json_decode($snapshot->payload, true, 512, JSON_THROW_ON_ERROR);

            DB::table('sales_transactions')->delete();

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('sales_transactions')->insert($chunk);
            }

            DB::table('sales_data_undo')->where('id', 1)->delete();

            return true;
        });
    }
}

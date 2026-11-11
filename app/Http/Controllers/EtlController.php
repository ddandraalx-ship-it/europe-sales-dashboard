<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class EtlController extends Controller
{
    public function index()
    {
        $transactionCount = DB::table('sales_transactions')->count();
        $latestOrderDate = DB::table('sales_transactions')->max('order_date');

        return view('etl', compact('transactionCount', 'latestOrderDate'));
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
}

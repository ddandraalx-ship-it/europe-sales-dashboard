<?php

namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $query = SalesTransaction::query();

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        if ($request->filled('channel')) {
            $query->where('sales_channel', $request->channel);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        return $query;
    }

    private function getFilterOptions()
    {
        return [
            'countryOptions' => SalesTransaction::query()
                ->select('country')
                ->distinct()
                ->orderBy('country')
                ->pluck('country'),

            'productOptions' => SalesTransaction::query()
                ->select('item_type')
                ->distinct()
                ->orderBy('item_type')
                ->pluck('item_type'),

            'channelOptions' => SalesTransaction::query()
                ->select('sales_channel')
                ->distinct()
                ->orderBy('sales_channel')
                ->pluck('sales_channel'),
        ];
    }

    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $kpi = (clone $query)
            ->selectRaw('
                COALESCE(SUM(total_revenue), 0) as revenue,
                COALESCE(SUM(total_cost), 0) as cost,
                COALESCE(SUM(total_profit), 0) as profit,
                COALESCE(SUM(units_sold), 0) as units_sold
            ')
            ->first();

        $kpi->margin = $kpi->revenue > 0
            ? ($kpi->profit / $kpi->revenue) * 100
            : 0;

        $yearExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%Y', order_date) AS INTEGER)"
            : 'YEAR(order_date)';
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', order_date) AS INTEGER)"
            : 'MONTH(order_date)';

        $trends = (clone $query)
            ->selectRaw("
                {$yearExpression} as year,
                {$monthExpression} as month,
                SUM(total_revenue) as revenue,
                SUM(total_cost) as cost,
                SUM(total_profit) as profit
            ")
            ->groupByRaw("{$yearExpression}, {$monthExpression}")
            ->orderByRaw("{$yearExpression}, {$monthExpression}")
            ->get();

        $countries = (clone $query)
            ->selectRaw('
                country,
                SUM(total_revenue) as revenue,
                SUM(total_cost) as cost,
                SUM(total_profit) as profit,
                SUM(units_sold) as units
            ')
            ->groupBy('country')
            ->orderByDesc('profit')
            ->get();

        $products = (clone $query)
            ->selectRaw('
                item_type,
                SUM(total_profit) as profit
            ')
            ->groupBy('item_type')
            ->orderByDesc('profit')
            ->get();

        $channels = (clone $query)
            ->selectRaw('
                sales_channel,
                SUM(total_profit) as profit
            ')
            ->groupBy('sales_channel')
            ->get();

        $transactions = (clone $query)
            ->orderByDesc('order_date')
            ->paginate(10)
            ->withQueryString();

        $filters = $this->getFilterOptions();

        return view('dashboard', array_merge(
            compact(
                'kpi',
                'trends',
                'countries',
                'products',
                'channels',
                'transactions'
            ),
            $filters
        ));
    }

    public function drilldown(Request $request)
    {
        $level = (int) $request->input('level', 1);

        $query = $this->filteredQuery($request);

        $data = collect();

        if ($level <= 1) {
            $data = (clone $query)
                ->selectRaw('
                    country as label,
                    SUM(total_revenue) as revenue,
                    SUM(total_cost) as cost,
                    SUM(total_profit) as profit,
                    SUM(units_sold) as units
                ')
                ->groupBy('country')
                ->orderByDesc('profit')
                ->get();
        } elseif ($level === 2) {
            $data = (clone $query)
                ->selectRaw('
                    sales_channel as label,
                    SUM(total_revenue) as revenue,
                    SUM(total_cost) as cost,
                    SUM(total_profit) as profit,
                    SUM(units_sold) as units
                ')
                ->groupBy('sales_channel')
                ->orderByDesc('profit')
                ->get();
        } elseif ($level === 3) {
            $data = (clone $query)
                ->selectRaw('
                    item_type as label,
                    SUM(total_revenue) as revenue,
                    SUM(total_cost) as cost,
                    SUM(total_profit) as profit,
                    SUM(units_sold) as units
                ')
                ->groupBy('item_type')
                ->orderByDesc('profit')
                ->get();
        } else {
            $level = 4;

            $data = (clone $query)
                ->orderByDesc('order_date')
                ->paginate(15)
                ->withQueryString();
        }

        $filters = $this->getFilterOptions();

        return view('drilldown', array_merge(
            compact('level', 'data'),
            $filters
        ));
    }

    public function whatIf(Request $request)
    {
        $query = $this->filteredQuery($request);

        $base = (clone $query)
            ->selectRaw('
                COALESCE(SUM(total_revenue), 0) as revenue,
                COALESCE(SUM(total_cost), 0) as cost,
                COALESCE(SUM(total_profit), 0) as profit,
                COALESCE(SUM(units_sold), 0) as units,
                COALESCE(SUM(CASE
                    WHEN total_revenue <> 0
                        OR total_cost <> 0
                        OR total_profit <> 0
                        OR unit_price <> 0
                        OR unit_cost <> 0
                    THEN 1 ELSE 0
                END), 0) as priced_transactions
            ')
            ->first();

        $priceChange = (float) $request->input('price_change', 0);
        $unitsChange = (float) $request->input('units_change', 0);
        $costChange = (float) $request->input('cost_change', 0);

        $priceChange = max(-100, min(1000, $priceChange));
        $unitsChange = max(-100, min(1000, $unitsChange));
        $costChange = max(-100, min(1000, $costChange));

        $revenueNew = $base->revenue
            * (1 + ($priceChange / 100))
            * (1 + ($unitsChange / 100));

        $costNew = $base->cost
            * (1 + ($costChange / 100))
            * (1 + ($unitsChange / 100));

        $profitNew = $revenueNew - $costNew;

        $marginNew = $revenueNew > 0
            ? ($profitNew / $revenueNew) * 100
            : 0;

        $filters = $this->getFilterOptions();

        return view('whatif', array_merge(
            compact(
                'base',
                'priceChange',
                'unitsChange',
                'costChange',
                'revenueNew',
                'costNew',
                'profitNew',
                'marginNew'
            ),
            $filters
        ));
    }
}

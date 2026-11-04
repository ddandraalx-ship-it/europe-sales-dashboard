
@extends('layout.app')

@section('title', 'Executive Dashboard')
@section('page-heading', 'Executive Dashboard')

@section('content')

<div class="mb-7">
    <h1 class="page-title">
        Sales Performance Overview
    </h1>

    <p class="text-sm text-slate-500 mt-2">
        Ringkasan performa penjualan, biaya, dan profitabilitas pasar Eropa.
    </p>
</div>

{{-- FILTER DATA --}}
<div class="card mb-6">

    <div class="mb-5">
        <h3 class="font-bold text-slate-800">
            Filter Data
        </h3>

        <p class="text-xs text-slate-400 mt-1">
            Pilih kriteria untuk menyesuaikan informasi dashboard.
        </p>
    </div>

    <form method="GET" action="{{ route('dashboard') }}">

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">

            <select name="country" class="form-control">
                <option value="">Semua Negara</option>

                @foreach($countryOptions as $country)
                    <option value="{{ $country }}"
                        @selected(request('country') == $country)>
                        {{ $country }}
                    </option>
                @endforeach
            </select>

            <select name="item_type" class="form-control">
                <option value="">Semua Produk</option>

                @foreach($productOptions as $product)
                    <option value="{{ $product }}"
                        @selected(request('item_type') == $product)>
                        {{ $product }}
                    </option>
                @endforeach
            </select>

            <select name="channel" class="form-control">
                <option value="">Semua Channel</option>

                @foreach($channelOptions as $channel)
                    <option value="{{ $channel }}"
                        @selected(request('channel') == $channel)>
                        {{ $channel }}
                    </option>
                @endforeach
            </select>

            <input
                type="date"
                name="date_from"
                value="{{ request('date_from') }}"
                class="form-control"
                aria-label="Tanggal mulai"
            >

            <input
                type="date"
                name="date_to"
                value="{{ request('date_to') }}"
                class="form-control"
                aria-label="Tanggal akhir"
            >

        </div>

        <div class="flex gap-2 mt-4">

            <button type="submit" class="btn-primary">
                Terapkan Filter
            </button>

            <a href="{{ route('dashboard') }}" class="btn-secondary">
                Reset Filter
            </a>

        </div>

    </form>
</div>

{{-- KPI --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">

    <div class="card kpi-card kpi-revenue">
        <p class="text-xs text-slate-500 font-medium">
            TOTAL REVENUE
        </p>

        <h3 class="kpi-value font-bold text-slate-800 mt-3">
            ${{ number_format($kpi->revenue, 2) }}
        </h3>

        <p class="text-xs text-slate-400 mt-2">
            Total pendapatan penjualan
        </p>
    </div>

    <div class="card kpi-card kpi-cost">
        <p class="text-xs text-slate-500 font-medium">
            TOTAL COST
        </p>

        <h3 class="kpi-value font-bold text-slate-800 mt-3">
            ${{ number_format($kpi->cost, 2) }}
        </h3>

        <p class="text-xs text-slate-400 mt-2">
            Total biaya penjualan
        </p>
    </div>

    <div class="card kpi-card kpi-profit">
        <p class="text-xs text-slate-500 font-medium">
            TOTAL PROFIT
        </p>

        <h3 class="kpi-value font-bold text-green-600 mt-3">
            ${{ number_format($kpi->profit, 2) }}
        </h3>

        <p class="text-xs text-slate-400 mt-2">
            Pendapatan dikurangi biaya
        </p>
    </div>

    <div class="card kpi-card kpi-margin">
        <p class="text-xs text-slate-500 font-medium">
            PROFIT MARGIN
        </p>

        <h3 class="kpi-value font-bold text-blue-600 mt-3">
            {{ number_format($kpi->margin, 2) }}%
        </h3>

        <p class="text-xs text-slate-400 mt-2">
            Persentase profit terhadap revenue
        </p>
    </div>

    <div class="card kpi-card kpi-units">
        <p class="text-xs text-slate-500 font-medium">
            UNITS SOLD
        </p>

        <h3 class="kpi-value font-bold text-slate-800 mt-3">
            {{ number_format($kpi->units_sold) }}
        </h3>

        <p class="text-xs text-slate-400 mt-2">
            Jumlah unit terjual
        </p>
    </div>

</div>

{{-- CHART --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-6">

    <div class="card">
        <div class="mb-5">
            <h3 class="font-bold text-slate-800">
                Profit Trend
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Perkembangan profit berdasarkan bulan.
            </p>
        </div>

        <div style="height: 280px;">
            <canvas id="profitChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="mb-5">
            <h3 class="font-bold text-slate-800">
                Revenue vs Cost
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Perbandingan pendapatan dan biaya.
            </p>
        </div>

        <div style="height: 280px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="mb-5">
            <h3 class="font-bold text-slate-800">
                Profit by Item Type
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Kontribusi profit berdasarkan jenis produk.
            </p>
        </div>

        <div style="height: 280px;">
            <canvas id="productChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="mb-5">
            <h3 class="font-bold text-slate-800">
                Sales Channel Contribution
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Perbandingan kontribusi profit setiap channel.
            </p>
        </div>

        <div style="height: 280px;">
            <canvas id="channelChart"></canvas>
        </div>
    </div>

</div>

{{-- COUNTRY PERFORMANCE --}}
<div class="card mb-6">

    <div class="flex flex-wrap justify-between items-center gap-3 mb-5">

        <div>
            <h3 class="font-bold text-slate-800">
                Country Performance
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Performa penjualan berdasarkan negara.
            </p>
        </div>

        <a href="{{ route('drilldown') }}"
           class="text-sm text-blue-600 font-semibold">
            Lihat Drill-Down →
        </a>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="table-head">
                <tr>
                    <th class="table-cell text-left">Country</th>
                    <th class="table-cell text-right">Revenue</th>
                    <th class="table-cell text-right">Cost</th>
                    <th class="table-cell text-right">Profit</th>
                    <th class="table-cell text-right">Units</th>
                    <th class="table-cell text-center">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($countries as $country)

                    <tr class="hover:bg-slate-50">

                        <td class="table-cell font-medium">
                            {{ $country->country }}
                        </td>

                        <td class="table-cell text-right">
                            ${{ number_format($country->revenue, 2) }}
                        </td>

                        <td class="table-cell text-right">
                            ${{ number_format($country->cost, 2) }}
                        </td>

                        <td class="table-cell text-right font-semibold text-green-600">
                            ${{ number_format($country->profit, 2) }}
                        </td>

                        <td class="table-cell text-right">
                            {{ number_format($country->units) }}
                        </td>

                        <td class="table-cell text-center">
                            <a
                                href="{{ route('drilldown', [
                                    'level' => 2,
                                    'country' => $country->country
                                ]) }}"
                                class="text-blue-600 font-medium"
                            >
                                Detail →
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="table-cell text-center text-slate-400">
                            Tidak ada data negara.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- TRANSACTION DETAIL --}}
<div class="card">

    <div class="mb-5">
        <h3 class="font-bold text-slate-800">
            Transaction Details
        </h3>

        <p class="text-xs text-slate-400 mt-1">
            Daftar transaksi berdasarkan filter yang dipilih.
        </p>
    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="table-head">
                <tr>
                    <th class="table-cell text-left">Order ID</th>
                    <th class="table-cell text-left">Country</th>
                    <th class="table-cell text-left">Item Type</th>
                    <th class="table-cell text-left">Channel</th>
                    <th class="table-cell text-left">Order Date</th>
                    <th class="table-cell text-right">Revenue</th>
                    <th class="table-cell text-right">Profit</th>
                </tr>
            </thead>

            <tbody>

                @forelse($transactions as $row)

                    <tr class="hover:bg-slate-50">

                        <td class="table-cell">
                            {{ $row->order_id }}
                        </td>

                        <td class="table-cell">
                            {{ $row->country }}
                        </td>

                        <td class="table-cell">
                            {{ $row->item_type }}
                        </td>

                        <td class="table-cell">
                            {{ $row->sales_channel }}
                        </td>

                        <td class="table-cell">
                            {{ $row->order_date?->format('d-m-Y') }}
                        </td>

                        <td class="table-cell text-right">
                            ${{ number_format($row->total_revenue, 2) }}
                        </td>

                        <td class="table-cell text-right font-semibold">
                            ${{ number_format($row->total_profit, 2) }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="table-cell text-center text-slate-400">
                            Tidak ada transaksi.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-5">
        {{ $transactions->links() }}
    </div>

</div>

@endsection

@push('scripts')
<script>
const trendData = @json($trends);

const trendLabels = trendData.map(item => {
    return item.year + '-' + String(item.month).padStart(2, '0');
});

new Chart(document.getElementById('profitChart'), {
    type: 'line',

    data: {
        labels: trendLabels,

        datasets: [{
            label: 'Profit',
            data: trendData.map(item => Number(item.profit)),
            borderColor: '#58d6af',
            backgroundColor: 'rgba(88, 214, 175, 0.12)',
            fill: true,
            tension: 0.35
        }]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',

    data: {
        labels: trendLabels,

        datasets: [
            {
                label: 'Revenue',
                data: trendData.map(item => Number(item.revenue)),
                backgroundColor: '#56c9a6'
            },
            {
                label: 'Cost',
                data: trendData.map(item => Number(item.cost)),
                backgroundColor: '#efb96b'
            }
        ]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

const productData = @json($products);

new Chart(document.getElementById('productChart'), {
    type: 'bar',

    data: {
        labels: productData.map(item => item.item_type),

        datasets: [{
            label: 'Profit',
            data: productData.map(item => Number(item.profit)),
            backgroundColor: ['#57d6af', '#75a9ff', '#e88f77', '#e6c46a', '#ad91ee', '#65c9d4', '#ed7fa6', '#9dcb77', '#ffad6e', '#81a8bb']
        }]
    },

    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

const channelData = @json($channels);

new Chart(document.getElementById('channelChart'), {
    type: 'doughnut',

    data: {
        labels: channelData.map(item => item.sales_channel),

        datasets: [{
            label: 'Profit',
            data: channelData.map(item => Number(item.profit)),
            backgroundColor: ['#56d6af', '#efb96b']
        }]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush


@extends('layout.app')

@section('title', 'What-If Analysis')
@section('page-heading', 'What-If Analysis')

@section('content')

<div class="mb-7">
    <h1 class="page-title">
        What-If Analysis
    </h1>

    <p class="text-sm text-slate-500 mt-2">
        Simulasikan perubahan harga, jumlah unit, dan biaya untuk melihat dampaknya terhadap profitabilitas.
    </p>
</div>

{{-- FILTER DATA --}}
<div class="card mb-6">

    <h3 class="font-bold mb-4">
        Data Scope
    </h3>

    <form method="GET" action="{{ route('whatif') }}">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

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

        </div>

        <div class="flex gap-2 mt-4">
            <button type="submit" class="btn-primary">
                Terapkan Filter
            </button>

            <a href="{{ route('whatif') }}" class="btn-secondary">
                Reset
            </a>
        </div>

    </form>

</div>

{{-- SIMULATION FORM --}}
<div class="card mb-6">

    <div class="mb-6">
        <h3 class="font-bold text-slate-800">
            Scenario Parameters
        </h3>

        <p class="text-xs text-slate-400 mt-1">
            Geser parameter dan hasil simulasi akan berubah langsung dari data database.
        </p>
    </div>

    <form method="GET" action="{{ route('whatif') }}" id="scenario-form"
        data-base-revenue="{{ (float) $base->revenue }}"
        data-base-cost="{{ (float) $base->cost }}"
        data-base-profit="{{ (float) $base->profit }}">

        <input type="hidden" name="country" value="{{ request('country') }}">
        <input type="hidden" name="item_type" value="{{ request('item_type') }}">
        <input type="hidden" name="channel" value="{{ request('channel') }}">
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <div>
                <label for="price-change" class="flex justify-between gap-3 text-sm font-semibold">
                    <span>Harga jual</span>
                    <output id="price-change-value" class="scenario-value"> {{ $priceChange }}% </output>
                </label>

                <input
                    type="range"
                    id="price-change"
                    name="price_change"
                    value="{{ $priceChange }}"
                    step="1"
                    min="-100"
                    max="100"
                    class="whatif-slider"
                >

                <p class="text-xs text-slate-400 mt-2">
                    Ubah harga jual per unit.
                </p>
            </div>

            <div>
                <label for="units-change" class="flex justify-between gap-3 text-sm font-semibold">
                    <span>Jumlah unit</span>
                    <output id="units-change-value" class="scenario-value">{{ $unitsChange }}%</output>
                </label>

                <input
                    type="range"
                    id="units-change"
                    name="units_change"
                    value="{{ $unitsChange }}"
                    step="1"
                    min="-100"
                    max="100"
                    class="whatif-slider"
                >

                <p class="text-xs text-slate-400 mt-2">
                    Ubah jumlah unit yang terjual.
                </p>
            </div>

            <div>
                <label for="cost-change" class="flex justify-between gap-3 text-sm font-semibold">
                    <span>Biaya</span>
                    <output id="cost-change-value" class="scenario-value">{{ $costChange }}%</output>
                </label>

                <input
                    type="range"
                    id="cost-change"
                    name="cost_change"
                    value="{{ $costChange }}"
                    step="1"
                    min="-100"
                    max="100"
                    class="whatif-slider"
                >

                <p class="text-xs text-slate-400 mt-2">
                    Ubah biaya per unit.
                </p>
            </div>

        </div>

        <button type="submit" class="btn-primary mt-6">
            Perbarui hasil simulasi
        </button>

    </form>

</div>

@if($base->units > 0 && $base->priced_transactions == 0)
    <div class="card mb-6" role="status" style="border-color:#765b2c;background:#211b12">
        <h3 class="font-semibold text-amber-300">
            Data keuangan di database masih bernilai nol
        </h3>
        <p class="text-sm text-slate-300 mt-2 leading-6">
            Dashboard sudah terhubung ke <code>europe_sales_db</code> dan membaca {{ number_format($base->units) }} unit,
            tetapi harga, biaya, revenue, dan profit pada transaksi yang dipilih tersimpan sebagai 0.
            Slider tetap menghitung skenario, namun hasil uang akan tetap 0 sampai data sumber diimpor ulang dengan kolom angka yang benar.
        </p>
    </div>
@endif

{{-- BASELINE --}}
<div class="mb-4">
    <h3 class="font-bold text-lg">
        Actual Performance
    </h3>

    <p class="text-xs text-slate-400 mt-1">
        Baseline berdasarkan {{ number_format($base->units) }} unit pada database dan filter yang dipilih.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-7">

    <div class="card">
        <p class="text-xs text-slate-500">
            ACTUAL REVENUE
        </p>

        <h3 class="text-2xl font-bold mt-3">
            ${{ number_format($base->revenue, 2) }}
        </h3>
    </div>

    <div class="card">
        <p class="text-xs text-slate-500">
            ACTUAL COST
        </p>

        <h3 class="text-2xl font-bold mt-3">
            ${{ number_format($base->cost, 2) }}
        </h3>
    </div>

    <div class="card">
        <p class="text-xs text-slate-500">
            ACTUAL PROFIT
        </p>

        <h3 class="text-2xl font-bold text-green-600 mt-3">
            ${{ number_format($base->profit, 2) }}
        </h3>
    </div>

</div>

{{-- SIMULATION RESULT --}}
<div class="mb-4">
    <h3 class="font-bold text-lg">
        Simulation Results
    </h3>

    <p class="text-xs text-slate-400 mt-1">
        Perbandingan hasil aktual dan hasil skenario.
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <div class="card">
        <p class="text-xs text-slate-500">
            NEW REVENUE
        </p>

        <h3 class="text-xl font-bold mt-3" id="scenario-revenue">
            ${{ number_format($revenueNew, 2) }}
        </h3>

        <p id="scenario-revenue-delta" class="text-xs mt-2 {{ $revenueNew >= $base->revenue ? 'text-green-600' : 'text-red-500' }}">
            {{ $revenueNew - $base->revenue >= 0 ? '+' : '' }}${{ number_format($revenueNew - $base->revenue, 2) }}
            dibanding aktual
        </p>
    </div>

    <div class="card">
        <p class="text-xs text-slate-500">
            NEW COST
        </p>

        <h3 class="text-xl font-bold mt-3" id="scenario-cost">
            ${{ number_format($costNew, 2) }}
        </h3>

        <p id="scenario-cost-delta" class="text-xs mt-2 {{ $costNew <= $base->cost ? 'text-green-600' : 'text-red-500' }}">
            {{ $costNew - $base->cost >= 0 ? '+' : '' }}${{ number_format($costNew - $base->cost, 2) }}
            dibanding aktual
        </p>
    </div>

    <div class="card">
        <p class="text-xs text-slate-500">
            NEW PROFIT
        </p>

        <h3 class="text-xl font-bold text-green-600 mt-3" id="scenario-profit">
            ${{ number_format($profitNew, 2) }}
        </h3>

        <p id="scenario-profit-delta" class="text-xs mt-2 {{ $profitNew >= $base->profit ? 'text-green-600' : 'text-red-500' }}">
            {{ $profitNew - $base->profit >= 0 ? '+' : '' }}${{ number_format($profitNew - $base->profit, 2) }}
            dibanding aktual
        </p>
    </div>

    <div class="card">
        <p class="text-xs text-slate-500">
            NEW PROFIT MARGIN
        </p>

        <h3 class="text-xl font-bold text-blue-600 mt-3" id="scenario-margin">
            {{ number_format($marginNew, 2) }}%
        </h3>

        <p class="text-xs text-slate-400 mt-2">
            Margin setelah simulasi.
        </p>
    </div>

</div>

{{-- COMPARISON TABLE --}}
<div class="card mb-6">

    <h3 class="font-bold mb-5">
        Actual vs Simulation
    </h3>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="table-head">
                <tr>
                    <th class="table-cell text-left">Metric</th>
                    <th class="table-cell text-right">Actual</th>
                    <th class="table-cell text-right">Simulation</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td class="table-cell">Revenue</td>
                    <td class="table-cell text-right">
                        ${{ number_format($base->revenue, 2) }}
                    </td>
                    <td class="table-cell text-right font-semibold">
                        <span id="compare-revenue">${{ number_format($revenueNew, 2) }}</span>
                    </td>
                </tr>

                <tr>
                    <td class="table-cell">Cost</td>
                    <td class="table-cell text-right">
                        ${{ number_format($base->cost, 2) }}
                    </td>
                    <td class="table-cell text-right font-semibold">
                        <span id="compare-cost">${{ number_format($costNew, 2) }}</span>
                    </td>
                </tr>

                <tr>
                    <td class="table-cell">Profit</td>
                    <td class="table-cell text-right">
                        ${{ number_format($base->profit, 2) }}
                    </td>
                    <td class="table-cell text-right font-semibold text-green-600">
                        <span id="compare-profit">${{ number_format($profitNew, 2) }}</span>
                    </td>
                </tr>

                <tr>
                    <td class="table-cell">Profit Margin</td>
                    <td class="table-cell text-right">
                        {{ $base->revenue > 0 ? number_format(($base->profit / $base->revenue) * 100, 2) : 0 }}%
                    </td>
                    <td class="table-cell text-right font-semibold">
                        <span id="compare-margin">{{ number_format($marginNew, 2) }}%</span>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

<div class="card">

    <h3 class="font-bold mb-3">
        Catatan Simulasi
    </h3>

    <p class="text-sm text-slate-600 leading-6">
        Hasil What-If Analysis merupakan simulasi matematis berdasarkan
        persentase perubahan harga, jumlah unit, dan biaya. Perhitungan ini
        mengasumsikan perubahan unit berlaku proporsional terhadap revenue
        dan cost. Hasil simulasi bukan prediksi permintaan pasar dan tidak
        memperhitungkan elastisitas harga.
    </p>

</div>

@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('scenario-form');

    if (!form) {
        return;
    }

    const base = {
        revenue: Number(form.dataset.baseRevenue),
        cost: Number(form.dataset.baseCost),
        profit: Number(form.dataset.baseProfit),
    };
    const sliders = {
        price: document.getElementById('price-change'),
        units: document.getElementById('units-change'),
        cost: document.getElementById('cost-change'),
    };
    const currency = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
    const percent = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const setText = (id, value) => {
        document.getElementById(id).textContent = value;
    };
    const setDelta = (id, change, positiveIsGood) => {
        const element = document.getElementById(id);
        element.textContent = `${change >= 0 ? '+' : '−'}${currency.format(Math.abs(change))} dibanding aktual`;
        element.classList.toggle('text-green-600', positiveIsGood ? change >= 0 : change <= 0);
        element.classList.toggle('text-red-500', positiveIsGood ? change < 0 : change > 0);
    };

    const updateScenario = () => {
        const priceChange = Number(sliders.price.value) / 100;
        const unitsChange = Number(sliders.units.value) / 100;
        const costChange = Number(sliders.cost.value) / 100;
        const revenue = base.revenue * (1 + priceChange) * (1 + unitsChange);
        const cost = base.cost * (1 + costChange) * (1 + unitsChange);
        const profit = revenue - cost;
        const margin = revenue > 0 ? (profit / revenue) * 100 : 0;

        setText('price-change-value', `${sliders.price.value}%`);
        setText('units-change-value', `${sliders.units.value}%`);
        setText('cost-change-value', `${sliders.cost.value}%`);
        setText('scenario-revenue', currency.format(revenue));
        setText('scenario-cost', currency.format(cost));
        setText('scenario-profit', currency.format(profit));
        setText('scenario-margin', `${percent.format(margin)}%`);
        setText('compare-revenue', currency.format(revenue));
        setText('compare-cost', currency.format(cost));
        setText('compare-profit', currency.format(profit));
        setText('compare-margin', `${percent.format(margin)}%`);
        setDelta('scenario-revenue-delta', revenue - base.revenue, true);
        setDelta('scenario-cost-delta', cost - base.cost, false);
        setDelta('scenario-profit-delta', profit - base.profit, true);
    };

    Object.values(sliders).forEach((slider) => slider.addEventListener('input', updateScenario));
    updateScenario();
})();
</script>
@endpush

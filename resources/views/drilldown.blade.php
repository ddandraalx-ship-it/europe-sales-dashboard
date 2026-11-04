
@extends('layout.app')

@section('title', 'Drill-Down Analysis')
@section('page-heading', 'Drill-Down Analysis')

@section('content')

<div class="mb-7">
    <h1 class="page-title">
        Drill-Down Analysis
    </h1>

    <p class="text-sm text-slate-500 mt-2">
        Telusuri performa penjualan dari tingkat negara hingga detail transaksi.
    </p>
</div>

@php
    $currentCountry = request('country');
    $currentChannel = request('channel');
    $currentItem = request('item_type');
    $levelFilters = request()->except(['level', 'page']);
@endphp

{{-- BREADCRUMB --}}
<div class="card mb-6">

    <div class="flex flex-wrap items-center gap-2 text-sm">

        <a href="{{ route('drilldown') }}"
           class="{{ $level == 1 ? 'font-bold text-blue-600' : 'text-slate-500' }}">
            Europe
        </a>

        @if($currentCountry)
            <span class="text-slate-300">/</span>

            <a href="{{ route('drilldown', [
                'level' => 2,
                'country' => $currentCountry
            ]) }}"
            class="{{ $level == 2 ? 'font-bold text-blue-600' : 'text-slate-500' }}">
                {{ $currentCountry }}
            </a>
        @endif

        @if($currentChannel)
            <span class="text-slate-300">/</span>

            <a href="{{ route('drilldown', [
                'level' => 3,
                'country' => $currentCountry,
                'channel' => $currentChannel
            ]) }}"
            class="{{ $level == 3 ? 'font-bold text-blue-600' : 'text-slate-500' }}">
                {{ $currentChannel }}
            </a>
        @endif

        @if($currentItem)
            <span class="text-slate-300">/</span>

            <span class="font-bold text-blue-600">
                {{ $currentItem }}
            </span>
        @endif

    </div>

</div>

{{-- FILTER --}}
<div class="card mb-6">

    <h3 class="font-bold mb-4">
        Filter Data
    </h3>

    <form method="GET" action="{{ route('drilldown') }}">

        <input type="hidden" name="level" value="{{ $level }}">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

            <select name="country" class="form-control">
                <option value="">Semua Negara</option>

                @foreach($countryOptions as $country)
                    <option value="{{ $country }}"
                        @selected($currentCountry == $country)>
                        {{ $country }}
                    </option>
                @endforeach
            </select>

            <select name="channel" class="form-control">
                <option value="">Semua Channel</option>

                @foreach($channelOptions as $channel)
                    <option value="{{ $channel }}"
                        @selected($currentChannel == $channel)>
                        {{ $channel }}
                    </option>
                @endforeach
            </select>

            <select name="item_type" class="form-control">
                <option value="">Semua Produk</option>

                @foreach($productOptions as $product)
                    <option value="{{ $product }}"
                        @selected($currentItem == $product)>
                        {{ $product }}
                    </option>
                @endforeach
            </select>

        </div>

        <div class="flex gap-2 mt-4">

            <button type="submit" class="btn-primary">
                Terapkan Filter
            </button>

            <a href="{{ route('drilldown') }}" class="btn-secondary">
                Reset
            </a>

        </div>

    </form>

</div>

{{-- LEVEL INFORMATION --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <a href="{{ route('drilldown', array_merge($levelFilters, ['level' => 1])) }}"
       class="card level-card {{ $level == 1 ? 'active' : '' }}"
       @if($level == 1) aria-current="step" @endif>
        <p class="text-xs text-slate-400">LEVEL 1</p>
        <h3 class="font-bold mt-2">Country</h3>
        <p class="text-xs text-slate-500 mt-1">Performa negara <span class="float-right">→</span></p>
    </a>

    <a href="{{ route('drilldown', array_merge($levelFilters, ['level' => 2])) }}"
       class="card level-card {{ $level == 2 ? 'active' : '' }}"
       @if($level == 2) aria-current="step" @endif>
        <p class="text-xs text-slate-400">LEVEL 2</p>
        <h3 class="font-bold mt-2">Sales Channel</h3>
        <p class="text-xs text-slate-500 mt-1">Online dan Offline <span class="float-right">→</span></p>
    </a>

    <a href="{{ route('drilldown', array_merge($levelFilters, ['level' => 3])) }}"
       class="card level-card {{ $level == 3 ? 'active' : '' }}"
       @if($level == 3) aria-current="step" @endif>
        <p class="text-xs text-slate-400">LEVEL 3</p>
        <h3 class="font-bold mt-2">Item Type</h3>
        <p class="text-xs text-slate-500 mt-1">Kategori produk <span class="float-right">→</span></p>
    </a>

    <a href="{{ route('drilldown', array_merge($levelFilters, ['level' => 4])) }}"
       class="card level-card {{ $level == 4 ? 'active' : '' }}"
       @if($level == 4) aria-current="step" @endif>
        <p class="text-xs text-slate-400">LEVEL 4</p>
        <h3 class="font-bold mt-2">Transactions</h3>
        <p class="text-xs text-slate-500 mt-1">Detail transaksi <span class="float-right">→</span></p>
    </a>

</div>

{{-- DATA TABLE --}}
<div class="card">

    <div class="flex flex-wrap justify-between items-center gap-3 mb-5">

        <div>
            <h3 class="font-bold text-slate-800">
                @if($level == 1)
                    Country Performance
                @elseif($level == 2)
                    Sales Channel Performance
                @elseif($level == 3)
                    Item Type Performance
                @else
                    Transaction Details
                @endif
            </h3>

            <p class="text-xs text-slate-400 mt-1">
                Data berdasarkan tingkatan analisis yang dipilih.
            </p>
        </div>

        @if($level > 1)
            <a
                href="{{ route('drilldown', array_merge(
                    request()->except(['level', 'page']),
                    ['level' => $level - 1]
                )) }}"
                class="btn-secondary"
            >
                ← Kembali ke Level Sebelumnya
            </a>
        @endif

    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="table-head">

                <tr>

                    @if($level < 4)
                        <th class="table-cell text-left">
                            {{ $level == 1 ? 'Country' : ($level == 2 ? 'Sales Channel' : 'Item Type') }}
                        </th>

                        <th class="table-cell text-right">Revenue</th>
                        <th class="table-cell text-right">Cost</th>
                        <th class="table-cell text-right">Profit</th>
                        <th class="table-cell text-right">Units Sold</th>
                    @else
                        <th class="table-cell">Order ID</th>
                        <th class="table-cell">Country</th>
                        <th class="table-cell">Channel</th>
                        <th class="table-cell">Item Type</th>
                        <th class="table-cell">Order Date</th>
                        <th class="table-cell text-right">Revenue</th>
                        <th class="table-cell text-right">Profit</th>
                    @endif

                </tr>

            </thead>

            <tbody>

                @forelse($data as $row)

                    <tr class="hover:bg-slate-50">

                        @if($level < 4)

                            <td class="table-cell font-medium">
                                @php
                                    $nextLevel = $level + 1;
                                    $nextFilters = match ($level) {
                                        1 => ['country' => $row->label],
                                        2 => ['channel' => $row->label],
                                        default => ['item_type' => $row->label],
                                    };
                                @endphp
                                <a class="drilldown-row-link"
                                   href="{{ route('drilldown', array_merge($levelFilters, $nextFilters, ['level' => $nextLevel])) }}">
                                    {{ $row->label }} <span aria-hidden="true">→</span>
                                </a>
                            </td>

                            <td class="table-cell text-right">
                                ${{ number_format($row->revenue, 2) }}
                            </td>

                            <td class="table-cell text-right">
                                ${{ number_format($row->cost, 2) }}
                            </td>

                            <td class="table-cell text-right font-semibold text-green-600">
                                ${{ number_format($row->profit, 2) }}
                            </td>

                            <td class="table-cell text-right">
                                {{ number_format($row->units) }}
                            </td>

                        @else

                            <td class="table-cell">
                                {{ $row->order_id }}
                            </td>

                            <td class="table-cell">
                                {{ $row->country }}
                            </td>

                            <td class="table-cell">
                                {{ $row->sales_channel }}
                            </td>

                            <td class="table-cell">
                                {{ $row->item_type }}
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

                        @endif

                    </tr>

                @empty

                    <tr>
                        <td colspan="{{ $level < 4 ? 5 : 7 }}" class="table-cell text-center text-slate-400">
                            Data tidak ditemukan.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($level == 4)
        <div class="mt-5">
            {{ $data->links() }}
        </div>
    @endif

</div>

@endsection

@extends('layout.app')

@section('title', 'Choose workspace · Europe Sales')
@section('page-heading', 'Workspace gateway')

@section('content')
<div class="mb-7">
    <p class="text-xs uppercase tracking-widest text-slate-400">EUROPE SALES / WORKSPACE</p>
    <h1 class="page-title mt-2">Where would you like to go?</h1>
    <p class="text-sm text-slate-400 mt-2">Choose a workspace. Your analytics dashboard and ETL tools are ready.</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
    <a href="{{ route('dashboard') }}" class="card gateway-card">
        <span class="gateway-icon">▦</span>
        <p class="gateway-kicker">01 / ANALYTICS</p>
        <h2>Executive Dashboard</h2>
        <p>Track sales, margins and market performance with filters, trends and drill-down analysis.</p>
        <span class="gateway-link">Open dashboard <span>→</span></span>
    </a>
    <a href="{{ route('etl') }}" class="card gateway-card gateway-card-accent">
        <span class="gateway-icon">⌘</span>
        <p class="gateway-kicker">02 / DATA OPERATIONS</p>
        <h2>ETL Management</h2>
        <p>Upload retail CSV data, validate and transform records, and load them into the sales workspace.</p>
        <span class="gateway-link">Open ETL console <span>→</span></span>
    </a>
</div>
<form method="POST" action="{{ route('logout') }}" class="mt-6">
    @csrf
    <button class="btn-secondary" type="submit">Sign out</button>
</form>
@endsection

@push('styles')
<style>
.gateway-card{position:relative;min-height:260px;display:flex;flex-direction:column;align-items:flex-start;color:inherit;text-decoration:none;transition:transform .22s,border-color .22s,box-shadow .22s}
.gateway-card:hover{transform:translateY(-4px);border-color:rgba(102,218,170,.4);box-shadow:0 22px 55px rgba(0,0,0,.27)}
.gateway-icon{width:45px;height:45px;display:grid;place-items:center;border:1px solid rgba(112,223,181,.2);border-radius:13px;background:rgba(42,123,91,.13);color:#8be0b6;font-size:21px}
.gateway-kicker{margin:24px 0 5px;color:#76cfa7;font-size:10px;letter-spacing:.13em}
.gateway-card h2{margin:0;color:#edf5f0;font-size:20px}
.gateway-card>p:not(.gateway-kicker){max-width:440px;margin:9px 0 20px;color:#a4b4ad;font-size:13px;line-height:1.7}
.gateway-link{margin-top:auto;color:#9ce4bf;font-size:12px;font-weight:600}
.gateway-link span{display:inline-block;margin-left:4px;transition:transform .2s}
.gateway-card:hover .gateway-link span{transform:translateX(4px)}
.gateway-card-accent{background:linear-gradient(145deg,rgba(31,67,55,.8),rgba(19,30,34,.86))}
</style>
@endpush

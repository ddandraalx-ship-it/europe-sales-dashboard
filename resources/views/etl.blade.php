@extends('layout.app')

@section('title', 'ETL Management · Europe Sales')
@section('page-heading', 'ETL Management')

@section('content')
<div class="mb-7">
    <p class="text-xs uppercase tracking-widest text-slate-400">DATA OPERATIONS / EUROPE</p>
    <h1 class="page-title mt-2">Retail data pipeline</h1>
    <p class="text-sm text-slate-400 mt-2">Extract transaction files, validate and normalize their data, then load them into sales analytics.</p>
</div>

@if (session('status'))
    <div class="card mb-5 etl-success" role="status">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="card mb-5 etl-error" role="alert">{{ $errors->first('file') ?? $errors->first() }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
    <div class="card etl-metric"><span class="metric-icon">⌁</span><p>EXTRACT</p><strong>CSV / TSV upload</strong><small>14-column sales dataset</small></div>
    <div class="card etl-metric"><span class="metric-icon">⟳</span><p>TRANSFORM</p><strong>Validate & normalize</strong><small>Dates, IDs, numeric and currency formats</small></div>
    <div class="card etl-metric"><span class="metric-icon">▥</span><p>LOAD</p><strong>{{ number_format($transactionCount) }} records</strong><small>Latest order: {{ $latestOrderDate ?: 'No data yet' }}</small></div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-5">
    <section class="card xl:col-span-3">
        <div class="mb-5">
            <h2 class="font-bold text-slate-800">Load sales data</h2>
            <p class="text-xs text-slate-400 mt-1">Upload a CSV or tab-separated file with the 14 expected sales columns.</p>
        </div>
        <form method="POST" action="{{ route('etl.import') }}" enctype="multipart/form-data">
            @csrf
            <label for="sales-file" class="upload-zone">
                <span class="upload-icon">↑</span>
                <strong>Choose a data file</strong>
                <span>CSV or TSV · up to 10 MB</span>
                <input id="sales-file" name="file" type="file" accept=".csv,.tsv,.txt,text/csv,text/tab-separated-values" required>
                <span id="file-name" class="file-name">No file selected</span>
            </label>
            <label class="replace-option">
                <input type="checkbox" name="replace" value="1">
                <span><strong>Replace existing data</strong><small>Clear current transactions only after the new file passes validation.</small></span>
            </label>
            <button class="btn-primary mt-5" type="submit">Validate &amp; load data <span>→</span></button>
        </form>
    </section>

    <section class="card xl:col-span-2">
        <h2 class="font-bold text-slate-800">Pipeline checklist</h2>
        <div class="pipeline-step"><span>01</span><div><strong>Extract</strong><small>CSV / TSV source file</small></div><b>READY</b></div>
        <div class="pipeline-step"><span>02</span><div><strong>Transform</strong><small>Schema, dates, numeric values and duplicates</small></div><b>ON UPLOAD</b></div>
        <div class="pipeline-step"><span>03</span><div><strong>Load</strong><small>Database transaction with upsert</small></div><b>ON VALID</b></div>
        <div class="fx-box">
            <p>FX CONVERSION PREVIEW</p>
            <div class="fx-inputs"><input id="fx-amount" type="number" value="100" min="0" aria-label="Amount in EUR"><span>EUR × 1.08 =</span><output id="fx-result">$108.00</output></div>
            <small>Illustrative rate only. Imported dataset amounts are retained as provided.</small>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.etl-success{border-color:rgba(86,214,160,.35);color:#9ce4bf;font-size:13px}
.etl-error{border-color:rgba(255,130,145,.35);color:#ff9aa6;font-size:13px}
.etl-metric{position:relative;min-height:148px}
.etl-metric p{margin:16px 0 5px;color:#7e968c;font-size:10px;letter-spacing:.14em}
.etl-metric strong{display:block;color:#e4eee8;font-size:14px}
.etl-metric small{display:block;margin-top:6px;color:#96aaa0;font-size:10px;line-height:1.55}
.metric-icon{position:absolute;top:17px;right:18px;color:#7fddb0;font-size:18px}
.upload-zone{min-height:195px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:22px;border:1px dashed rgba(125,190,156,.34);border-radius:12px;background:rgba(26,82,60,.09);color:#e4eee8;cursor:pointer;transition:border-color .2s,background .2s}
.upload-zone:hover{border-color:rgba(105,220,163,.65);background:rgba(26,102,69,.15)}
.upload-zone>span:not(.upload-icon){color:#91a69c;font-size:11px}
.upload-icon{width:36px;height:36px;display:grid;place-items:center;border:1px solid rgba(112,223,181,.22);border-radius:11px;color:#8be0b6;font-size:18px}
.upload-zone input{position:absolute;width:1px;height:1px;opacity:0}
.upload-zone .file-name{max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.replace-option{display:flex;gap:11px;align-items:flex-start;margin-top:18px;color:#dbe7e0;font-size:12px;cursor:pointer}
.replace-option input{margin-top:3px;accent-color:#42b987}
.replace-option small{display:block;margin-top:5px;color:#91a69c;font-size:10px;line-height:1.5}
.pipeline-step{display:flex;align-items:center;gap:12px;padding:15px 0;border-bottom:1px solid rgba(145,180,169,.1)}
.pipeline-step>span{width:27px;height:27px;display:grid;place-items:center;border-radius:8px;background:rgba(47,131,92,.14);color:#8cddb2;font-size:10px}
.pipeline-step div{flex:1}
.pipeline-step strong,.pipeline-step small{display:block}
.pipeline-step strong{color:#dce8e1;font-size:12px}
.pipeline-step small{margin-top:3px;color:#91a69c;font-size:10px}
.pipeline-step b{color:#89dcb0;font-size:9px;letter-spacing:.08em}
.fx-box{margin-top:21px;padding:15px;border:1px solid rgba(145,180,169,.13);border-radius:11px;background:rgba(7,15,18,.27)}
.fx-box p{margin:0 0 12px;color:#82988e;font-size:9px;letter-spacing:.12em}
.fx-inputs{display:flex;align-items:center;gap:8px;flex-wrap:wrap;color:#a6b9af;font-size:11px}
.fx-inputs input{width:78px;padding:7px 8px;border:1px solid rgba(145,180,169,.2);border-radius:7px;background:#101b1e;color:#eef6f2}
.fx-inputs output{color:#a4e7c2;font-weight:700}
.fx-box>small{display:block;margin-top:9px;color:#80958c;font-size:9px}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('sales-file').addEventListener('change', event => {
    document.getElementById('file-name').textContent = event.target.files[0]?.name || 'No file selected';
});
document.getElementById('fx-amount').addEventListener('input', event => {
    const amount = Number(event.target.value);
    document.getElementById('fx-result').textContent = `$${(amount * 1.08).toFixed(2)}`;
});
</script>
@endpush

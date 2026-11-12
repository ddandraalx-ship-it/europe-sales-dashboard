@extends('layout.app')

@section('title', 'Verify ETL change · Europe Sales')
@section('page-heading', 'Verify ETL change')

@section('content')
@php
    $actionLabels = ['create' => 'Add transaction', 'update' => 'Update transaction', 'delete' => 'Delete transaction'];
@endphp

<div class="mb-7">
    <p class="text-xs uppercase tracking-widest text-slate-400">SECOND VERIFICATION</p>
    <h1 class="page-title mt-2">Review before saving</h1>
    <p class="text-sm text-slate-400 mt-2">Perubahan belum disimpan. Cocokkan seluruh nilai di bawah sebelum konfirmasi terakhir.</p>
</div>

@if ($errors->any())
    <div class="card mb-5 etl-error" role="alert">{{ $errors->first() }}</div>
@endif

<section class="card">
    <h2 class="font-bold text-slate-800">{{ $actionLabels[$pending['action']] }}</h2>
    @if (in_array($pending['action'], ['update', 'delete'], true))
        <p class="text-xs text-slate-400 mt-2">Transaksi #{{ $pending['old']['order_id'] }} · nilai saat ini</p>
        <div class="etl-review-grid mt-4">
            @foreach ($pending['old'] as $field => $value)
                <div><small>{{ $fieldLabels[$field] }}</small><strong>{{ $value }}</strong></div>
            @endforeach
        </div>
    @endif

    @if ($pending['action'] !== 'delete')
        <p class="text-xs text-slate-400 mt-5">{{ $pending['action'] === 'update' ? 'Nilai setelah perubahan' : 'Nilai transaksi baru' }}</p>
        <div class="etl-review-grid mt-3">
            @foreach ($pending['data'] as $field => $value)
                <div><small>{{ $fieldLabels[$field] }}</small><strong>{{ $value }}</strong></div>
            @endforeach
        </div>
    @else
        <p class="text-sm text-red-500 mt-4">Transaksi ini akan dihapus. Anda dapat menggunakan tombol Undo di halaman ETL untuk mengembalikannya.</p>
    @endif

    <form class="mt-6" method="POST" action="{{ route('etl.confirm.apply') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $pending['token'] }}">
        <label class="etl-confirm-check">
            <input type="checkbox" name="confirmation" value="1" required>
            <span>Saya sudah memeriksa kembali data dan ingin melanjutkan.</span>
        </label>
        @error('confirmation')<p class="etl-field-error mt-2">{{ $message }}</p>@enderror
        <div class="mt-5 flex flex-wrap gap-3">
            <button class="btn-primary" type="submit">Confirm and save</button>
        </div>
    </form>
    <form class="mt-3" method="POST" action="{{ route('etl.cancel') }}">
        @csrf
        <button class="btn-secondary" type="submit">Cancel change</button>
    </form>
</section>
@endsection

@push('styles')
<style>
.etl-error{border-color:rgba(255,130,145,.35);color:#ff9aa6;font-size:13px}
.etl-review-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:9px}
.etl-review-grid>div{min-width:0;padding:10px 12px;border:1px solid rgba(145,180,169,.13);border-radius:8px;background:rgba(7,15,18,.24)}
.etl-review-grid small,.etl-review-grid strong{display:block;overflow-wrap:anywhere}
.etl-review-grid small{color:#91a69c;font-size:10px}
.etl-review-grid strong{margin-top:4px;color:#e4eee8;font-size:12px}
.etl-confirm-check{display:flex;align-items:flex-start;gap:9px;color:#dbe7e0;font-size:12px;cursor:pointer}
.etl-confirm-check input{margin-top:3px;accent-color:#42b987}
.etl-field-error{color:#ff9aa6;font-size:10px}
</style>
@endpush

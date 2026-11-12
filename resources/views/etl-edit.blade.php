@extends('layout.app')

@section('title', 'Edit transaction · Europe Sales')
@section('page-heading', 'Edit transaction')

@section('content')
<div class="mb-7">
    <p class="text-xs uppercase tracking-widest text-slate-400">DATA OPERATIONS / EUROPE</p>
    <h1 class="page-title mt-2">Edit transaction #{{ $transaction->order_id }}</h1>
    <p class="text-sm text-slate-400 mt-2">Periksa setiap kolom. Data baru akan ditampilkan kembali untuk verifikasi sebelum disimpan.</p>
</div>

@if ($errors->any())
    <div class="card mb-5 etl-error" role="alert">{{ $errors->first() }}</div>
@endif

<section class="card">
    <form method="POST" action="{{ route('etl.transactions.update-preview', $transaction) }}">
        @csrf
        @include('partials.etl-transaction-fields', ['transaction' => $transaction])
        <div class="mt-5 flex flex-wrap gap-3">
            <button class="btn-primary" type="submit">Review changes <span>→</span></button>
            <a class="btn-secondary" href="{{ route('etl') }}">Cancel</a>
        </div>
    </form>
</section>
@endsection

@push('styles')
<style>
.etl-error{border-color:rgba(255,130,145,.35);color:#ff9aa6;font-size:13px}
</style>
@endpush

@php
    $transaction = $transaction ?? null;
    $fields = [
        'region' => ['Region', 'text'],
        'country' => ['Country', 'text'],
        'item_type' => ['Item type', 'text'],
        'sales_channel' => ['Sales channel', 'text'],
        'order_priority' => ['Order priority', 'text'],
        'order_date' => ['Order date', 'date'],
        'order_id' => ['Order ID', 'number'],
        'ship_date' => ['Ship date', 'date'],
        'units_sold' => ['Units sold', 'number'],
        'unit_price' => ['Unit price', 'number'],
        'unit_cost' => ['Unit cost', 'number'],
        'total_revenue' => ['Total revenue', 'number'],
        'total_cost' => ['Total cost', 'number'],
        'total_profit' => ['Total profit', 'number'],
    ];
@endphp

<div class="etl-field-grid">
    @foreach ($fields as $field => [$label, $type])
        <label class="etl-field">
            <span>{{ $label }}</span>
            <input
                class="form-control"
                name="{{ $field }}"
                type="{{ $type }}"
                value="{{ old($field, $transaction?->getAttribute($field) ?? '') }}"
                @if ($type === 'number')
                    @if (in_array($field, ['unit_price', 'unit_cost', 'total_revenue', 'total_cost', 'total_profit'], true))
                        min="{{ $field === 'total_profit' ? '' : '0' }}"
                        step="0.01"
                    @else
                        min="0"
                        step="1"
                    @endif
                @endif
                required
            >
            @error($field)<small class="etl-field-error">{{ $message }}</small>@enderror
        </label>
    @endforeach
</div>

@push('styles')
<style>
.etl-field-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
.etl-field{display:flex;flex-direction:column;gap:6px;color:#b9cbc2;font-size:11px}
.etl-field-error{color:#ff9aa6;font-size:10px}
@media(max-width:640px){.etl-field-grid{grid-template-columns:1fr}}
</style>
@endpush

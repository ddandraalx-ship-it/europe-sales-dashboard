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
                id="transaction-{{ $field }}"
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
                @if (in_array($field, ['total_revenue', 'total_cost', 'total_profit'], true))
                    readonly
                    data-calculated="true"
                    aria-label="{{ $label }} (calculated automatically)"
                @endif
                required
            >
            @error($field)
                <small class="etl-field-error" id="error-{{ $field }}">{{ $message }}</small>
            @else
                <small class="etl-field-error" id="error-{{ $field }}" aria-live="polite"></small>
            @enderror
        </label>
    @endforeach
</div>

@push('styles')
<style>
.etl-field-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
.etl-field{display:flex;flex-direction:column;gap:6px;color:#b9cbc2;font-size:11px}
.etl-field-error{color:#ff9aa6;font-size:10px}
.etl-field input[readonly]{border-style:dashed;color:#a8e7c7;cursor:not-allowed}
@media(max-width:640px){.etl-field-grid{grid-template-columns:1fr}}
</style>
@endpush

@push('scripts')
<script>
(() => {
    document.querySelectorAll('.etl-transaction-form').forEach(form => {
        const fields = Array.from(form.querySelectorAll('input[name]'));
        const quantity = form.querySelector('[name="units_sold"]');
        const unitPrice = form.querySelector('[name="unit_price"]');
        const unitCost = form.querySelector('[name="unit_cost"]');

        const showError = (field, message) => {
            const error = document.getElementById(`error-${field.name}`);
            if (error) error.textContent = message;
            field.setAttribute('aria-invalid', message ? 'true' : 'false');
        };

        const updateTotals = () => {
            if (!quantity || !unitPrice || !unitCost) return;

            const units = Number(quantity.value);
            const price = Number(unitPrice.value);
            const cost = Number(unitCost.value);
            const valid = quantity.value !== '' && unitPrice.value !== '' && unitCost.value !== ''
                && Number.isFinite(units) && Number.isFinite(price) && Number.isFinite(cost);
            const revenue = valid ? Number((units * price).toFixed(2)) : 0;
            const costTotal = valid ? Number((units * cost).toFixed(2)) : 0;

            form.querySelector('[name="total_revenue"]').value = valid ? revenue.toFixed(2) : '';
            form.querySelector('[name="total_cost"]').value = valid ? costTotal.toFixed(2) : '';
            form.querySelector('[name="total_profit"]').value = valid ? (revenue - costTotal).toFixed(2) : '';
        };

        const validateField = field => {
            if (field.dataset.calculated === 'true') return true;

            let message = '';
            if (field.required && field.value.trim() === '') {
                message = 'Field ini wajib diisi.';
            } else if (field.type === 'number' && field.value !== '') {
                const value = Number(field.value);
                if (!Number.isFinite(value)) {
                    message = 'Harus diisi dengan angka yang valid.';
                } else if (field.step === '1' && !Number.isInteger(value)) {
                    message = 'Harus diisi dengan angka bulat yang valid.';
                } else if (field.validity.stepMismatch) {
                    message = 'Nilai mata uang maksimal menggunakan 2 angka desimal.';
                } else if (field.min !== '' && value < Number(field.min)) {
                    message = `Nilai tidak boleh kurang dari ${field.min}.`;
                }
            } else if (field.type === 'text' && field.value !== '' && field.value.trim() === '') {
                message = 'Harus diisi dengan format teks.';
            } else if (field.type === 'date' && field.value !== '' && !field.validity.valid) {
                message = 'Gunakan format tanggal yang valid.';
            }

            showError(field, message);
            return message === '';
        };

        fields.forEach(field => {
            field.addEventListener('input', () => {
                if (['units_sold', 'unit_price', 'unit_cost'].includes(field.name)) updateTotals();
                validateField(field);
            });
            field.addEventListener('change', () => validateField(field));
        });

        form.addEventListener('submit', event => {
            updateTotals();
            if (!fields.map(validateField).every(Boolean)) {
                event.preventDefault();
                const firstInvalid = form.querySelector('[aria-invalid="true"]');
                if (firstInvalid) firstInvalid.focus();
            }
        });

        updateTotals();
    });
})();
</script>
@endpush

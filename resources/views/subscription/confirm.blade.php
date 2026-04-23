@extends('layouts.app')
@section('title', 'Confirm Subscription')
@section('content')

<style>
/* Same radio-option style as registration */
.radio-group { display: flex; gap: 10px; flex-wrap: wrap; }
.radio-option {
    display: flex; align-items: center; gap: 8px; cursor: pointer;
    padding: 9px 14px; border: 1.5px solid var(--border); border-radius: 9px;
    background: var(--off); transition: border-color .18s, background .15s;
    font-size: 13.5px; font-weight: 400; color: var(--black);
    user-select: none; flex: 1; min-width: 130px;
}
.radio-option:hover { border-color: var(--black); background: var(--white); opacity: 1; }
.radio-option input[type="radio"] {
    width: 16px !important; height: 16px !important;
    min-width: 16px !important; padding: 0 !important;
    margin: 0 !important; cursor: pointer; flex-shrink: 0;
    accent-color: var(--black);
    appearance: auto !important; -webkit-appearance: auto !important;
}
.radio-option:has(input:checked) {
    border-color: var(--black); background: var(--black); color: var(--white);
}

/* Same submit button as registration */
.auth-submit-btn {
    padding: 13px 32px; background: var(--black); color: var(--white);
    border: 1.5px solid var(--black); border-radius: 9px;
    font-size: 14.5px; font-weight: 600; font-family: var(--font-sans);
    cursor: pointer; letter-spacing: .02em;
    transition: opacity .15s, transform .15s, box-shadow .15s;
}
.auth-submit-btn:hover { opacity: .87; transform: translateY(-1px); box-shadow: var(--shadow-md); }
.auth-submit-btn:active { transform: translateY(0); box-shadow: none; }

.cancel-btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 13px 24px; border: 1.5px solid var(--border); border-radius: 9px;
    color: var(--black); text-decoration: none; font-size: 14px; font-weight: 500;
    background: transparent; transition: border-color .18s, background .15s;
}
.cancel-btn:hover { border-color: var(--black); background: var(--off); opacity: 1; }

/* Order summary table fix */
.summary-td-label { padding: 7px 24px 7px 0; color: var(--muted); border: none; font-size: 14px; }
.summary-td-value { padding: 7px 0; border: none; font-size: 14px; color: var(--black); }

/* Cash note */
#cash-note {
    display: none;
    padding: 13px 16px;
    background: rgba(217,119,6,.08);
    border: 1px solid rgba(217,119,6,.25);
    border-radius: 9px;
    margin-bottom: 18px;
    font-size: 13.5px;
    color: var(--black);
    line-height: 1.6;
}
[data-theme="dark"] #cash-note { background: rgba(217,119,6,.12); border-color: rgba(217,119,6,.3); }
</style>

<div class="card">
    <p style="margin-bottom:8px;"><a href="{{ route('subscription.index') }}">&larr; Back to Subscription</a></p>
    <h1>Confirm Subscription</h1>
    <p class="muted">Review your plan and choose how to pay before confirming.</p>
</div>

<div class="card">
    <h2>Order Summary</h2>
    <table style="width:auto;">
        <tr>
            <td class="summary-td-label">Plan</td>
            <td class="summary-td-value"><strong>{{ $label }}</strong></td>
        </tr>
        <tr>
            <td class="summary-td-label">Amount</td>
            <td class="summary-td-value"><strong style="font-size:22px;font-family:var(--font-disp);letter-spacing:.02em;">&#8369;{{ number_format($amount, 2) }}</strong></td>
        </tr>
        <tr>
            <td class="summary-td-label">Duration</td>
            <td class="summary-td-value">{{ $plan === 'yearly' ? '12 months' : '1 month' }}</td>
        </tr>
        <tr>
            <td class="summary-td-label">Account</td>
            <td class="summary-td-value">{{ $user->email }}</td>
        </tr>
    </table>
</div>

<div class="card">
    <h2 style="margin-bottom:16px;">Payment Method</h2>
    <form method="POST" action="{{ route('subscription.subscribe') }}" id="subscribeForm">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan }}">

        <div class="radio-group" style="margin-bottom:16px;">
            @foreach($paymentMethods as $value => $label)
                <label class="radio-option">
                    <input type="radio" name="payment_method" value="{{ $value }}"
                           onchange="selectMethod('{{ $value }}')"
                           {{ old('payment_method') === $value ? 'checked' : '' }}>
                    {{ $label }}
                </label>
            @endforeach
        </div>

        @error('payment_method')
            <p style="color:#dc2626;font-size:13px;margin-bottom:12px;">{{ $message }}</p>
        @enderror

        <div id="cash-note">
            For Cash at Counter or Bank Transfer: your subscription will be recorded. Please present your receipt at the library counter within 3 business days to complete payment. Your subscription activates immediately but may be suspended if payment is not received.
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:4px;">
            <button type="submit" class="auth-submit-btn">
                Confirm and Pay &#8369;{{ number_format($amount, 2) }}
            </button>
            <a href="{{ route('subscription.index') }}" class="cancel-btn">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function selectMethod(value) {
    var note = document.getElementById('cash-note');
    note.style.display = (value === 'cash' || value === 'bank_transfer') ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    var checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) selectMethod(checked.value);
});
</script>
@endpush
@endsection
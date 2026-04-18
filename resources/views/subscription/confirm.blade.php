@extends('layouts.app')
@section('title', 'Confirm Subscription')
@section('content')
<div class="card">
    <p style="margin-bottom: 8px;"><a href="{{ route('subscription.index') }}">&larr; Back to Subscription</a></p>
    <h1>Confirm Subscription</h1>
    <p class="muted">Review your plan and choose how to pay before confirming.</p>
</div>

<div class="card">
    <h2>Order Summary</h2>
    <table style="width: auto;">
        <tr>
            <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Plan</td>
            <td style="padding: 6px 0; border: none;"><strong>{{ $label }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Amount</td>
            <td style="padding: 6px 0; border: none;"><strong style="font-size: 20px;">&#8369;{{ number_format($amount, 2) }}</strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Subscriber for</td>
            <td style="padding: 6px 0; border: none;">{{ $plan === 'yearly' ? '12 months' : '1 month' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Account</td>
            <td style="padding: 6px 0; border: none;">{{ $user->email }}</td>
        </tr>
    </table>
</div>

<div class="card">
    <h2>Payment Method</h2>
    <form method="POST" action="{{ route('subscription.subscribe') }}" id="subscribeForm">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan }}">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 20px;">
            @foreach($paymentMethods as $value => $label)
                <label style="display: flex; align-items: center; gap: 10px; padding: 14px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;" id="method-label-{{ $value }}">
                    <input type="radio" name="payment_method" value="{{ $value }}"
                           onchange="selectMethod('{{ $value }}')"
                           {{ old('payment_method') === $value ? 'checked' : '' }}
                           style="width: auto; margin: 0;">
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>

        @error('payment_method')
            <p style="color: #dc2626; font-size: 13px; margin-bottom: 12px;">{{ $message }}</p>
        @enderror

        <div id="cash-note" style="display: none; padding: 12px; background: #fef9c3; border-radius: 6px; margin-bottom: 16px; font-size: 14px;">
            For Cash at Counter or Bank Transfer: your subscription will be recorded. Please present your receipt at the library counter within 3 business days to complete payment. Your subscription activates immediately but may be suspended if payment is not received.
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px;">
            <button type="submit" id="confirmBtn" style="width: auto; padding: 12px 32px;">
                Confirm and Pay &#8369;{{ number_format($amount, 2) }}
            </button>
            <a href="{{ route('subscription.index') }}" style="display: inline-block; padding: 12px 24px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function selectMethod(value) {
    document.querySelectorAll('[id^="method-label-"]').forEach(function(el) {
        el.style.borderColor = '#e5e7eb';
    });
    var selected = document.getElementById('method-label-' + value);
    if (selected) selected.style.borderColor = '#111827';

    var note = document.getElementById('cash-note');
    note.style.display = (value === 'cash' || value === 'bank_transfer') ? 'block' : 'none';
}

// Highlight already-selected on load
document.addEventListener('DOMContentLoaded', function() {
    var checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) selectMethod(checked.value);
});
</script>
@endpush
@endsection
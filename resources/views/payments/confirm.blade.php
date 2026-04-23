@extends('layouts.app')

@section('title', 'Pay Fine')

@section('content')
    <style>
        .payment-summary td { padding: 6px 24px 6px 0; border: none; }
        .payment-summary .label { color: var(--muted); }
        .payment-summary .value { color: var(--black); }

        .radio-group { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px; margin-bottom: 20px; }
        .radio-option {
            display: flex; align-items: center; gap: 10px; cursor: pointer;
            padding: 14px 16px; border: 1.5px solid var(--border); border-radius: 10px;
            background: var(--off); transition: border-color .18s, background .15s;
            font-size: 13.5px; font-weight: 500; color: var(--black); user-select: none;
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
        .radio-option.is-selected {
            border-color: var(--black); background: var(--black); color: var(--white);
        }
        .fine-note {
            display: none; padding: 13px 16px; margin-bottom: 16px; border-radius: 10px;
            background: rgba(217,119,6,.08); border: 1px solid rgba(217,119,6,.24);
            color: var(--black); line-height: 1.65; font-size: 13.5px;
        }
        [data-theme="dark"] .fine-note { background: rgba(217,119,6,.12); border-color: rgba(217,119,6,.32); }
        .action-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .btn-ghost {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 12px 24px; border: 1.5px solid var(--border); border-radius: 8px;
            color: var(--black); text-decoration: none; background: transparent;
            font-size: 14px; font-weight: 500;
        }
        .btn-ghost:hover { border-color: var(--black); background: var(--off); opacity: 1; }
    </style>

    <div class="card">
        <p style="margin-bottom: 8px;"><a href="{{ route('user.dashboard') }}">&larr; Back to Dashboard</a></p>
        <h1>Pay Fine</h1>
        <p class="muted">Select your payment method and confirm to pay the overdue fine.</p>
    </div>

    <div class="card">
        <h2>Fine Details</h2>
        <table class="payment-summary" style="width: auto;">
            <tr>
                <td class="label">Book</td>
                <td class="value"><strong>{{ $borrowing->book->title ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Due Date</td>
                <td class="value">{{ $borrowing->due_date?->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Days Overdue</td>
                <td class="value">{{ max(0, now()->diffInDays($borrowing->due_date, false) * -1) }} days</td>
            </tr>
            <tr>
                <td class="label">Fine Amount</td>
                <td class="value"><strong style="font-size: 20px;">&#8369;{{ number_format($fine, 2) }}</strong></td>
            </tr>
            @if($payment)
            <tr>
                <td class="label">Payment ID</td>
                <td class="value" style="font-family: monospace;">#{{ $payment->id }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($payment && !$payment->isPending())
        <div class="card">
            <div class="flash {{ $payment->isCompleted() ? 'success' : 'warning' }}">
                @if($payment->isCompleted())
                    This payment has been completed.
                @else
                    This payment has a status of: {{ $payment->status }}.
                @endif
            </div>
            @if($payment->isCompleted())
                <a href="{{ route('payments.receipt', $payment->id) }}" style="display: inline-block; padding: 10px 24px; background: #111827; color: white; border-radius: 6px; text-decoration: none; margin-top: 8px;">
                    View Receipt
                </a>
            @endif
        </div>
    @elseif($payment && $payment->isPending())
        <div class="card">
            <h2>Confirm Payment</h2>

            @if($payment->payment_method === 'cash')
                <div class="flash warning" style="margin-bottom: 16px;">
                    You selected Cash at Counter. Please proceed to the library counter to pay &#8369;{{ number_format($fine, 2) }}. Click the button below to acknowledge your intent to pay.
                </div>
            @elseif($payment->payment_method === 'bank_transfer')
                <div class="flash warning" style="margin-bottom: 16px;">
                    You selected Bank Transfer. Please transfer &#8369;{{ number_format($fine, 2) }} to the library's bank account and bring proof of transfer to the counter.
                </div>
            @endif

            <form method="POST" action="{{ route('payments.manual_confirm', $payment->id) }}">
                @csrf
                <div class="action-row">
                    <button type="submit" style="width: auto; padding: 12px 32px;">
                        Acknowledge — I Will Pay at the Counter
                    </button>
                    <a href="{{ route('user.dashboard') }}" class="btn-ghost">
                        Back
                    </a>
                </div>
            </form>
        </div>
    @else
        @php $selectedMethod = old('payment_method', array_key_first($paymentMethods)); @endphp
        <div class="card">
            <h2>Choose Payment Method</h2>
            <form method="POST" action="{{ route('payments.process', $borrowing->id) }}" id="finePayForm">
                @csrf

                <div class="radio-group">
                    @foreach($paymentMethods as $value => $label)
                        <label class="radio-option" id="fine-method-label-{{ $value }}">
                            <input type="radio" name="payment_method" value="{{ $value }}"
                                   onchange="selectFineMethod('{{ $value }}')"
                                   {{ $selectedMethod === $value ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                @error('payment_method')
                    <p style="color: #dc2626; font-size: 13px; margin-bottom: 12px;">{{ $message }}</p>
                @enderror

                <div id="fine-cash-note" class="fine-note">
                    For Cash at Counter or Bank Transfer: a payment record will be created. Please bring your receipt to the library counter to complete the payment.
                </div>

                <div class="action-row">
                    <button type="submit" style="width: auto; padding: 12px 32px;">
                        Confirm Payment — &#8369;{{ number_format($fine, 2) }}
                    </button>
                    <a href="{{ route('user.dashboard') }}" class="btn-ghost">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    @endif
@endsection

@push('scripts')
<script>
function selectFineMethod(value) {
    document.querySelectorAll('[id^="fine-method-label-"]').forEach(function(el) {
        el.classList.remove('is-selected');
    });
    var selected = document.getElementById('fine-method-label-' + value);
    if (selected) selected.classList.add('is-selected');

    var note = document.getElementById('fine-cash-note');
    if (note) note.style.display = (value === 'cash' || value === 'bank_transfer') ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    var checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) selectFineMethod(checked.value);
});
</script>
@endpush

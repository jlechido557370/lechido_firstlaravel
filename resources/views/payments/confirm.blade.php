@extends('layouts.app')

@section('title', 'Pay Fine')

@section('content')
    <div class="card">
        <p style="margin-bottom: 8px;"><a href="{{ route('user.dashboard') }}">&larr; Back to Dashboard</a></p>
        <h1>Pay Fine</h1>
        <p class="muted">Select your payment method and confirm to pay the overdue fine.</p>
    </div>

    <div class="card">
        <h2>Fine Details</h2>
        <table style="width: auto;">
            <tr>
                <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Book</td>
                <td style="padding: 6px 0; border: none;"><strong>{{ $borrowing->book->title ?? 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Due Date</td>
                <td style="padding: 6px 0; border: none;">{{ $borrowing->due_date?->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Days Overdue</td>
                <td style="padding: 6px 0; border: none;">{{ max(0, now()->diffInDays($borrowing->due_date, false) * -1) }} days</td>
            </tr>
            <tr>
                <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Fine Amount</td>
                <td style="padding: 6px 0; border: none;"><strong style="font-size: 20px;">&#8369;{{ number_format($fine, 2) }}</strong></td>
            </tr>
            @if($payment)
            <tr>
                <td style="padding: 6px 24px 6px 0; color: #6b7280; border: none;">Payment ID</td>
                <td style="padding: 6px 0; border: none; font-family: monospace;">#{{ $payment->id }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($payment && !$payment->isPending())
        {{-- Payment already processed, just show receipt link --}}
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
        {{-- Payment created, awaiting manual confirm --}}
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
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" style="width: auto; padding: 12px 32px;">
                        Acknowledge — I Will Pay at the Counter
                    </button>
                    <a href="{{ route('user.dashboard') }}" style="display: inline-block; padding: 12px 24px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">
                        Back
                    </a>
                </div>
            </form>
        </div>
    @else
        {{-- Initial method selection --}}
        <div class="card">
            <h2>Choose Payment Method</h2>
            <form method="POST" action="{{ route('payments.process', $borrowing->id) }}" id="finePayForm">
                @csrf

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 20px;">
                    @foreach($paymentMethods as $value => $label)
                        <label style="display: flex; align-items: center; gap: 10px; padding: 14px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;" id="fine-method-label-{{ $value }}">
                            <input type="radio" name="payment_method" value="{{ $value }}"
                                   onchange="selectFineMethod('{{ $value }}')"
                                   style="width: auto; margin: 0;">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                @error('payment_method')
                    <p style="color: #dc2626; font-size: 13px; margin-bottom: 12px;">{{ $message }}</p>
                @enderror

                <div id="fine-cash-note" style="display: none; padding: 12px; background: #fef9c3; border-radius: 6px; margin-bottom: 16px; font-size: 14px;">
                    For Cash at Counter or Bank Transfer: a payment record will be created. Please bring your receipt to the library counter to complete the payment.
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" style="width: auto; padding: 12px 32px;">
                        Confirm Payment — &#8369;{{ number_format($fine, 2) }}
                    </button>
                    <a href="{{ route('user.dashboard') }}" style="display: inline-block; padding: 12px 24px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">
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
        el.style.borderColor = '#e5e7eb';
    });
    var selected = document.getElementById('fine-method-label-' + value);
    if (selected) selected.style.borderColor = '#111827';

    var note = document.getElementById('fine-cash-note');
    note.style.display = (value === 'cash' || value === 'bank_transfer') ? 'block' : 'none';
}
</script>
@endpush
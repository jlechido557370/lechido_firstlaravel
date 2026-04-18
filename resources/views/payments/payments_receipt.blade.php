@extends('layouts.app')
@section('title', 'Payment Receipt')

@section('content')

{{-- Modal overlay --}}
<div id="receipt-overlay" style="position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 500; display: flex; align-items: center; justify-content: center; padding: 16px;">
    <div id="receipt-modal" style="background: white; border-radius: 8px; border: 1px solid #ddd; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; position: relative;">

        {{-- Modal header --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #e5e7eb;">
            <h2 style="margin: 0; font-size: 18px;">Payment Receipt</h2>
            <a href="{{ route('user.dashboard') }}" style="color: #6b7280; font-size: 22px; line-height: 1; text-decoration: none;">&times;</a>
        </div>

        {{-- Receipt body --}}
        <div id="receipt-body" style="padding: 20px;">
            <div style="text-align: center; padding-bottom: 16px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                <div style="font-weight: bold; font-size: 16px;">Library Management System</div>
                <div class="muted" style="font-size: 13px; margin-top: 4px;">Fine Payment Receipt</div>
            </div>

            <table style="width: 100%; margin-bottom: 16px;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; border: none; width: 45%; font-size: 14px;">Receipt No.</td>
                    <td style="padding: 6px 0; border: none; font-family: monospace;"><strong>#{{ $payment->id }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; border: none; font-size: 14px;">Date</td>
                    <td style="padding: 6px 0; border: none; font-size: 14px;">
                        {{ ($payment->paid_at ?? $payment->created_at)->format('F d, Y h:i A') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; border: none; font-size: 14px;">Name</td>
                    <td style="padding: 6px 0; border: none; font-size: 14px;">{{ auth()->user()->displayName() }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; border: none; font-size: 14px;">Account</td>
                    <td style="padding: 6px 0; border: none; font-size: 14px;">{{ auth()->user()->email }}</td>
                </tr>
                @if($borrowing)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; border: none; font-size: 14px;">Book</td>
                    <td style="padding: 6px 0; border: none; font-size: 14px;">{{ $borrowing->book->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; border: none; font-size: 14px;">Due Date</td>
                    <td style="padding: 6px 0; border: none; font-size: 14px;">{{ $borrowing->due_date?->format('M d, Y') }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; border: none; font-size: 14px;">Payment Method</td>
                    <td style="padding: 6px 0; border: none; font-size: 14px;">{{ $methodLabel }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; border: none; font-size: 14px;">Status</td>
                    <td style="padding: 6px 0; border: none;">
                        @if($payment->isCompleted())
                            <span class="badge badge-green">Paid</span>
                        @else
                            <span class="badge badge-yellow">Pending Verification</span>
                        @endif
                    </td>
                </tr>
            </table>

            <div style="border-top: 2px solid #111827; padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: bold;">Fine Amount</span>
                <span style="font-size: 20px; font-weight: bold;">&#8369;{{ number_format($payment->amount, 2) }}</span>
            </div>

            @if(!$payment->isCompleted())
            <div class="flash warning" style="margin-top: 14px; font-size: 13px;">
                Payment is pending staff verification. Please present this receipt at the library counter.
            </div>
            @endif

            <div style="margin-top: 14px; text-align: center; color: #9ca3af; font-size: 12px;">
                Generated on {{ now()->format('M d, Y') }}.
            </div>
        </div>

        {{-- Modal footer --}}
        <div style="display: flex; gap: 8px; padding: 14px 20px; border-top: 1px solid #e5e7eb; flex-wrap: wrap;">
            <button onclick="downloadPdf()" style="width: auto; padding: 9px 20px; flex: 1;">
                Download PDF
            </button>
            <a href="{{ route('user.dashboard') }}"
               style="flex: 1; text-align: center; padding: 9px 20px; border: 1px solid #d1d5db; border-radius: 6px; color: #374151; text-decoration: none;">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>

{{-- Hidden content used by Download PDF --}}
<div id="print-area" style="display: none;">
    <div style="font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 24px;">
        <div style="text-align: center; padding-bottom: 16px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
            <div style="font-weight: bold; font-size: 18px;">Library Management System</div>
            <div style="color: #6b7280; font-size: 13px; margin-top: 4px;">Fine Payment Receipt</div>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
            <tr><td style="padding: 6px 0; color: #6b7280; width: 45%;">Receipt No.</td><td style="padding: 6px 0;"><strong>#{{ $payment->id }}</strong></td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Date</td><td style="padding: 6px 0;">{{ ($payment->paid_at ?? $payment->created_at)->format('F d, Y h:i A') }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Name</td><td style="padding: 6px 0;">{{ auth()->user()->displayName() }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Account</td><td style="padding: 6px 0;">{{ auth()->user()->email }}</td></tr>
            @if($borrowing)
            <tr><td style="padding: 6px 0; color: #6b7280;">Book</td><td style="padding: 6px 0;">{{ $borrowing->book->title ?? 'N/A' }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Due Date</td><td style="padding: 6px 0;">{{ $borrowing->due_date?->format('M d, Y') }}</td></tr>
            @endif
            <tr><td style="padding: 6px 0; color: #6b7280;">Payment Method</td><td style="padding: 6px 0;">{{ $methodLabel }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Status</td><td style="padding: 6px 0;">{{ $payment->isCompleted() ? 'Paid' : 'Pending Verification' }}</td></tr>
        </table>
        <div style="border-top: 2px solid #111827; padding-top: 14px; display: flex; justify-content: space-between;">
            <strong>Fine Amount</strong>
            <strong style="font-size: 18px;">&#8369;{{ number_format($payment->amount, 2) }}</strong>
        </div>
        @if(!$payment->isCompleted())
        <div style="margin-top: 12px; padding: 10px; background: #fef9c3; border-radius: 4px; font-size: 13px;">
            Payment pending staff verification. Please present this receipt at the library counter.
        </div>
        @endif
        <div style="margin-top: 20px; text-align: center; color: #9ca3af; font-size: 12px;">
            Generated on {{ now()->format('M d, Y') }}.
        </div>
    </div>
</div>

@push('scripts')
<script>
function downloadPdf() {
    var content = document.getElementById('print-area').innerHTML;
    var win = window.open('', '_blank', 'width=650,height=700');
    win.document.write(
        '<!DOCTYPE html><html><head>' +
        '<title>Payment Receipt #{{ $payment->id }}</title>' +
        '<style>body{font-family:Arial,sans-serif;margin:24px;}@media print{body{margin:0;}}</style>' +
        '</head><body>' + content + '</body></html>'
    );
    win.document.close();
    win.focus();
    setTimeout(function(){ win.print(); }, 400);
}
</script>
@endpush

@endsection
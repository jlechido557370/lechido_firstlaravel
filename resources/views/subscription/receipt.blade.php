@extends('layouts.app')
@section('title', 'Subscription Receipt')

@section('content')

<style>
/* Dim the page body behind the modal so the nav doesn't show through */
.page-body {
    position: relative;
    animation: none !important; /* disable page-in animation so overlay isn't clipped */
}

.receipt-overlay {
    position: fixed;
    inset: 0;
    top: 68px; /* below the sticky nav */
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(4px);
    z-index: 400;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
    animation: overlayIn .3s ease both;
}

@keyframes overlayIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

.receipt-modal {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 14px;
    width: 100%;
    max-width: 480px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
    position: relative;
    box-shadow: var(--shadow-lg);
    animation: modalIn .35s cubic-bezier(0.34,1.56,0.64,1) both;
    /* Modern scrollbar */
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}
.receipt-modal::-webkit-scrollbar { width: 4px; }
.receipt-modal::-webkit-scrollbar-track { background: transparent; }
.receipt-modal::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

@keyframes modalIn {
    from { opacity: 0; transform: translateY(20px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
}
.receipt-header h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--black);
}
.receipt-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 7px;
    border: 1.5px solid var(--border);
    color: var(--muted);
    font-size: 16px;
    text-decoration: none;
    line-height: 1;
    transition: border-color .15s, color .15s, background .15s;
}
.receipt-close:hover { border-color: var(--black); color: var(--black); background: var(--off); opacity: 1; }

.receipt-body {
    padding: 24px;
}

.receipt-brand {
    text-align: center;
    padding-bottom: 18px;
    margin-bottom: 18px;
    border-bottom: 1px solid var(--border);
}
.receipt-brand-name {
    font-family: var(--font-disp);
    font-size: 20px;
    letter-spacing: .06em;
    color: var(--black);
}
.receipt-brand-sub {
    font-size: 12px;
    color: var(--muted);
    font-family: var(--font-mono);
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-top: 4px;
}

.receipt-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 18px;
}
.receipt-table td {
    padding: 8px 0;
    border: none;
    font-size: 14px;
    vertical-align: top;
    background: transparent !important;
}
.receipt-table tr:hover td { background: transparent !important; }
.receipt-table .receipt-label {
    color: var(--muted);
    width: 42%;
    font-size: 13px;
}
.receipt-table .receipt-value {
    color: var(--black);
    font-weight: 500;
}

.receipt-total {
    border-top: 2px solid var(--black);
    padding-top: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}
.receipt-total-label {
    font-weight: 600;
    font-size: 14px;
    color: var(--black);
}
.receipt-total-amount {
    font-size: 22px;
    font-weight: 700;
    color: var(--black);
    font-family: var(--font-disp);
    letter-spacing: .02em;
}

.receipt-footer {
    display: flex;
    gap: 10px;
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.receipt-footer-btn {
    flex: 1;
    text-align: center;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: opacity .15s, background .15s;
    border: none;
    font-family: var(--font-sans);
}
.receipt-footer-btn-primary {
    background: var(--black);
    color: var(--white);
}
.receipt-footer-btn-primary:hover { opacity: .82; }
.receipt-footer-btn-secondary {
    background: transparent;
    color: var(--black);
    border: 1.5px solid var(--border) !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.receipt-footer-btn-secondary:hover { border-color: var(--black) !important; background: var(--off); opacity: 1; }
</style>

{{-- Overlay + Modal --}}
<div class="receipt-overlay">
    <div class="receipt-modal">

        {{-- Header --}}
        <div class="receipt-header">
            <h2>Subscription Receipt</h2>
            <a href="{{ route('subscription.index') }}" class="receipt-close" title="Close">&times;</a>
        </div>

        {{-- Body --}}
        <div class="receipt-body">
            <div class="receipt-brand">
                <div class="receipt-brand-name">.Library</div>
                <div class="receipt-brand-sub">Subscription Receipt</div>
            </div>

            <table class="receipt-table">
                <tr>
                    <td class="receipt-label">Receipt Date</td>
                    <td class="receipt-value">{{ now()->format('F d, Y h:i A') }}</td>
                </tr>
                <tr>
                    <td class="receipt-label">Name</td>
                    <td class="receipt-value">{{ $user->displayName() }}</td>
                </tr>
                <tr>
                    <td class="receipt-label">Account</td>
                    <td class="receipt-value">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td class="receipt-label">Plan</td>
                    <td class="receipt-value">{{ $label }}</td>
                </tr>
                <tr>
                    <td class="receipt-label">Payment Method</td>
                    <td class="receipt-value">{{ $methodLabel }}</td>
                </tr>
                <tr>
                    <td class="receipt-label">Status</td>
                    <td class="receipt-value">
                        <span class="badge badge-green">Active</span>
                    </td>
                </tr>
                @if($expiresAt)
                <tr>
                    <td class="receipt-label">Valid Until</td>
                    <td class="receipt-value"><strong>{{ $expiresAt->format('F d, Y') }}</strong></td>
                </tr>
                @endif
            </table>

            <div class="receipt-total">
                <span class="receipt-total-label">Amount Paid</span>
                <span class="receipt-total-amount">&#8369;{{ number_format($amount, 2) }}</span>
            </div>

            @if(in_array($method, ['cash', 'bank_transfer']))
            <div class="flash warning" style="margin-top: 16px; font-size: 13px;">
                Payment via {{ $methodLabel }}. Please present this receipt at the library counter to complete your payment.
            </div>
            @endif

            <div style="margin-top: 14px; text-align: center; color: var(--muted); font-size: 12px; font-family: var(--font-mono);">
                Generated on {{ now()->format('M d, Y') }}
            </div>
        </div>

        {{-- Footer --}}
        <div class="receipt-footer">
            <button onclick="downloadPdf()" class="receipt-footer-btn receipt-footer-btn-primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:5px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download PDF
            </button>
            <a href="{{ route('subscription.index') }}" class="receipt-footer-btn receipt-footer-btn-secondary">
                My Subscription
            </a>
        </div>

    </div>
</div>

{{-- Hidden print area --}}
<div id="print-area" style="display:none;">
    <div style="font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 24px;">
        <div style="text-align: center; padding-bottom: 16px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
            <div style="font-weight: bold; font-size: 18px;">.Library</div>
            <div style="color: #6b7280; font-size: 13px; margin-top: 4px;">Subscription Receipt</div>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
            <tr><td style="padding: 6px 0; color: #6b7280; width: 45%;">Receipt Date</td><td style="padding: 6px 0;">{{ now()->format('F d, Y h:i A') }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Name</td><td style="padding: 6px 0;">{{ $user->displayName() }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Account</td><td style="padding: 6px 0;">{{ $user->email }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Plan</td><td style="padding: 6px 0;">{{ $label }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Payment Method</td><td style="padding: 6px 0;">{{ $methodLabel }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b7280;">Status</td><td style="padding: 6px 0;">Active</td></tr>
            @if($expiresAt)
            <tr><td style="padding: 6px 0; color: #6b7280;">Valid Until</td><td style="padding: 6px 0;"><strong>{{ $expiresAt->format('F d, Y') }}</strong></td></tr>
            @endif
        </table>
        <div style="border-top: 2px solid #111; padding-top: 14px; display: flex; justify-content: space-between;">
            <strong>Amount Paid</strong>
            <strong style="font-size: 18px;">&#8369;{{ number_format($amount, 2) }}</strong>
        </div>
        @if(in_array($method, ['cash', 'bank_transfer']))
        <div style="margin-top: 12px; padding: 10px; background: #fef9c3; border-radius: 4px; font-size: 13px;">
            Payment via {{ $methodLabel }}. Please present this receipt at the library counter.
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
        '<title>Subscription Receipt — dotLibrary</title>' +
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
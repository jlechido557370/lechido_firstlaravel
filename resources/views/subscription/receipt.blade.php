@extends('layouts.app')
@section('title', 'Subscription Receipt')

@section('content')
<style>
    .receipt-page {
        max-width: 560px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }

    /* Success banner */
    .receipt-success-banner {
        text-align: center;
        padding: 36px 32px 28px;
        margin-bottom: 20px;
        background: var(--glass-bg);
        backdrop-filter: blur(16px) saturate(1.4);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--glass-shadow);
        position: relative;
        overflow: hidden;
    }
    .receipt-success-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 50% 0%, rgba(22,163,74,0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .receipt-checkmark {
        width: 56px; height: 56px;
        background: rgba(22,163,74,0.12);
        border: 1.5px solid rgba(22,163,74,0.3);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        animation: checkPop .5s cubic-bezier(0.34,1.56,0.64,1) both;
    }
    @keyframes checkPop {
        from { transform: scale(0); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
    .receipt-checkmark svg {
        width: 26px; height: 26px;
        stroke: #16a34a; fill: none; stroke-width: 2.5;
        animation: checkDraw .4s ease .3s both;
    }
    @keyframes checkDraw {
        from { stroke-dashoffset: 40; }
        to   { stroke-dashoffset: 0; }
    }
    .receipt-checkmark svg polyline { stroke-dasharray: 40; }

    /* Receipt card */
    .receipt-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px) saturate(1.4);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--glass-shadow);
        overflow: hidden;
        margin-bottom: 16px;
        animation: cardIn .45s ease .1s both;
    }
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .receipt-card-header {
        padding: 20px 28px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--off);
    }
    .receipt-brand-block {
        display: flex; align-items: center; gap: 10px;
    }
    .receipt-brand-icon {
        width: 32px; height: 32px;
        border: 1.5px solid var(--black);
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 9px; font-family: var(--font-mono);
        letter-spacing: .02em; color: var(--black);
        flex-shrink: 0;
    }
    .receipt-brand-name {
        font-family: var(--font-disp);
        font-size: 18px; letter-spacing: .06em; color: var(--black);
    }
    .receipt-date-block {
        text-align: right;
    }
    .receipt-date-label {
        font-size: 9px; font-weight: 700; letter-spacing: .1em;
        text-transform: uppercase; color: var(--muted);
        font-family: var(--font-mono); margin-bottom: 3px;
    }
    .receipt-date-value {
        font-size: 13px; color: var(--black); font-weight: 500;
    }

    .receipt-card-body { padding: 24px 28px; }

    /* Info rows */
    .receipt-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 11px 0;
        border-bottom: 1px solid var(--mid);
        gap: 16px;
    }
    .receipt-row:last-child { border-bottom: none; }
    .receipt-row-label {
        font-size: 13px; color: var(--muted);
        font-family: var(--font-mono); letter-spacing: .03em;
        text-transform: uppercase; font-size: 11px; font-weight: 600;
        flex-shrink: 0;
    }
    .receipt-row-value {
        font-size: 14.5px; color: var(--black); font-weight: 500;
        text-align: right;
    }

    /* Total row */
    .receipt-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 28px;
        border-top: 2px solid var(--black);
        background: var(--off);
    }
    .receipt-total-label {
        font-size: 14px; font-weight: 700; color: var(--black);
        letter-spacing: .03em; text-transform: uppercase;
        font-family: var(--font-mono);
    }
    .receipt-total-amount {
        font-family: var(--font-disp);
        font-size: 32px; color: var(--black); letter-spacing: .02em;
    }

    /* Actions */
    .receipt-actions {
        display: flex; gap: 12px; flex-wrap: wrap;
    }
    .receipt-btn-primary {
        flex: 1; min-width: 160px;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        background: var(--black); color: var(--white);
        padding: 13px 24px; border-radius: 10px;
        font-size: 14px; font-weight: 600;
        cursor: pointer; border: none; font-family: var(--font-sans);
        transition: opacity .18s, transform .18s, box-shadow .18s;
    }
    .receipt-btn-primary:hover {
        opacity: .86; transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    }
    .receipt-btn-secondary {
        flex: 1; min-width: 140px;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        background: transparent; color: var(--black);
        padding: 13px 24px; border-radius: 10px;
        font-size: 14px; font-weight: 500;
        border: 1.5px solid var(--border);
        text-decoration: none;
        transition: border-color .18s, background .15s, transform .18s;
    }
    .receipt-btn-secondary:hover {
        border-color: var(--black); background: var(--off);
        opacity: 1; transform: translateY(-1px);
    }

    /* Warning note */
    .receipt-warning {
        padding: 13px 16px;
        background: rgba(217,119,6,.08);
        border: 1px solid rgba(217,119,6,.25);
        border-radius: 10px;
        font-size: 13.5px;
        color: var(--black);
        line-height: 1.65;
        margin-top: 20px;
    }
    [data-theme="dark"] .receipt-warning {
        background: rgba(217,119,6,.1);
        border-color: rgba(217,119,6,.3);
    }
    .receipt-footer-note {
        text-align: center;
        color: var(--muted);
        font-size: 12px;
        font-family: var(--font-mono);
        letter-spacing: .04em;
        margin-top: 12px;
    }
</style>

<div class="receipt-page">

    {{-- Success Banner --}}
    <div class="receipt-success-banner">
        <div class="receipt-checkmark">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h1 style="font-size:22px;font-weight:600;color:var(--black);margin:0 0 8px;">Subscription Activated!</h1>
        <p style="color:var(--muted);font-size:15px;margin:0;line-height:1.6;">
            Your <strong style="color:var(--black);">{{ $label }}</strong> is now active.
            @if($expiresAt) Valid until <strong style="color:var(--black);">{{ $expiresAt->format('F d, Y') }}</strong>.@endif
        </p>
    </div>

    {{-- Receipt Card --}}
    <div class="receipt-card">
        <div class="receipt-card-header">
            <div class="receipt-brand-block">
                <div class="receipt-brand-icon">IVO.</div>
                <div class="receipt-brand-name">.Library</div>
            </div>
            <div class="receipt-date-block">
                <div class="receipt-date-label">Receipt Date</div>
                <div class="receipt-date-value">{{ now()->format('M d, Y · h:i A') }}</div>
            </div>
        </div>

        <div class="receipt-card-body">
            <div class="receipt-row">
                <span class="receipt-row-label">Name</span>
                <span class="receipt-row-value">{{ $user->displayName() }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Account</span>
                <span class="receipt-row-value" style="font-size:13px;font-family:var(--font-mono);">{{ $user->email }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Plan</span>
                <span class="receipt-row-value">{{ $label }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Payment</span>
                <span class="receipt-row-value">{{ $methodLabel }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-row-label">Status</span>
                <span class="receipt-row-value">
                    <span class="badge badge-green" style="font-size:12px;padding:4px 12px;">● Active</span>
                </span>
            </div>
            @if($expiresAt)
            <div class="receipt-row">
                <span class="receipt-row-label">Valid Until</span>
                <span class="receipt-row-value"><strong>{{ $expiresAt->format('F d, Y') }}</strong></span>
            </div>
            @endif
        </div>

        <div class="receipt-total-row">
            <span class="receipt-total-label">Amount Paid</span>
            <span class="receipt-total-amount">&#8369;{{ number_format($amount, 2) }}</span>
        </div>
    </div>

    @if(in_array($method, ['cash', 'bank_transfer']))
    <div class="receipt-warning">
        <strong>⚠ Action Required:</strong> Payment via {{ $methodLabel }} was selected. Please present this receipt at the library counter within 3 business days to complete your payment. Your subscription is currently active but may be suspended if payment is not received.
    </div>
    @endif

    {{-- Actions --}}
    <div class="receipt-actions" style="margin-top:20px;">
        <button onclick="downloadPdf()" class="receipt-btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDF
        </button>
        <a href="{{ route('subscription.index') }}" class="receipt-btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            My Subscription
        </a>
        <a href="{{ route('home') }}" class="receipt-btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Go Home
        </a>
    </div>

    <p class="receipt-footer-note">Generated on {{ now()->format('M d, Y') }} &nbsp;·&nbsp; dotLibrary</p>
</div>

{{-- Hidden print area --}}
<div id="print-area" style="display:none;">
    <div style="font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 28px;">
        <div style="text-align: center; padding-bottom: 16px; margin-bottom: 20px; border-bottom: 2px solid #111;">
            <div style="font-weight: 700; font-size: 20px; letter-spacing: .06em;">.Library</div>
            <div style="color: #6b7280; font-size: 12px; margin-top: 5px; letter-spacing: .08em; text-transform: uppercase;">Subscription Receipt</div>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr><td style="padding: 8px 0; color: #6b7280; width: 40%; font-size:13px;">Receipt Date</td><td style="padding: 8px 0; font-size:14px;">{{ now()->format('F d, Y h:i A') }}</td></tr>
            <tr><td style="padding: 8px 0; color: #6b7280; font-size:13px;">Name</td><td style="padding: 8px 0; font-size:14px;">{{ $user->displayName() }}</td></tr>
            <tr><td style="padding: 8px 0; color: #6b7280; font-size:13px;">Account</td><td style="padding: 8px 0; font-size:14px;">{{ $user->email }}</td></tr>
            <tr><td style="padding: 8px 0; color: #6b7280; font-size:13px;">Plan</td><td style="padding: 8px 0; font-size:14px; font-weight:600;">{{ $label }}</td></tr>
            <tr><td style="padding: 8px 0; color: #6b7280; font-size:13px;">Payment Method</td><td style="padding: 8px 0; font-size:14px;">{{ $methodLabel }}</td></tr>
            <tr><td style="padding: 8px 0; color: #6b7280; font-size:13px;">Status</td><td style="padding: 8px 0; font-size:14px; color:#16a34a; font-weight:600;">● Active</td></tr>
            @if($expiresAt)
            <tr><td style="padding: 8px 0; color: #6b7280; font-size:13px;">Valid Until</td><td style="padding: 8px 0; font-size:14px; font-weight:700;">{{ $expiresAt->format('F d, Y') }}</td></tr>
            @endif
        </table>
        <div style="border-top: 2px solid #111; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
            <strong style="font-size:14px; text-transform:uppercase; letter-spacing:.04em;">Amount Paid</strong>
            <strong style="font-size: 26px;">&#8369;{{ number_format($amount, 2) }}</strong>
        </div>
        @if(in_array($method, ['cash', 'bank_transfer']))
        <div style="margin-top: 16px; padding: 12px; background: #fef9c3; border-left: 3px solid #d97706; border-radius: 4px; font-size: 13px; line-height:1.6;">
            <strong>Action Required:</strong> Payment via {{ $methodLabel }}. Please present this receipt at the library counter within 3 business days.
        </div>
        @endif
        <div style="margin-top: 24px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 16px;">
            Generated on {{ now()->format('M d, Y') }} &nbsp;·&nbsp; dotLibrary
        </div>
    </div>
</div>

@push('scripts')
<script>
function downloadPdf() {
    var content = document.getElementById('print-area').innerHTML;
    var win = window.open('', '_blank', 'width=680,height=750');
    win.document.write(
        '<!DOCTYPE html><html><head>' +
        '<title>Subscription Receipt — dotLibrary</title>' +
        '<style>body{font-family:Arial,sans-serif;margin:24px;}@media print{body{margin:0;}}</style>' +
        '</head><body>' + content + '</body></html>'
    );
    win.document.close();
    win.focus();
    setTimeout(function(){ win.print(); }, 500);
}
</script>
@endpush

@endsection

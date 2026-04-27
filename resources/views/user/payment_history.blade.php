@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
    <div class="card" style="padding:28px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
            <a href="{{ route('user.dashboard') }}" style="display:inline-flex; align-items:center; gap:4px; color:var(--muted); font-size:13px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                Dashboard
            </a>
        </div>
        <h1 style="font-size:24px; font-weight:600; margin-bottom:4px;">Payment History</h1>
        <p class="muted">All your fine and subscription payments</p>
    </div>

    <div class="card" style="padding:0; overflow:hidden;">
        <table>
            <thead>
                <tr>
                    <th style="padding-left:24px;">Type</th>
                    <th>Details</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="padding-right:24px;">Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    @php $isSubscription = $payment->type === 'subscription'; @endphp
                    <tr>
                        <td style="padding-left:24px;">
                            @if($isSubscription)
                                <span class="badge" style="background:rgba(124,58,237,.12); color:#7c3aed; font-size:11px;">Subscription</span>
                            @else
                                <span class="badge" style="background:rgba(220,38,38,.12); color:#dc2626; font-size:11px;">Fine</span>
                            @endif
                        </td>
                        <td>
                            @if($isSubscription)
                                {{ $payment->subscription_plan === 'yearly' ? 'Yearly Plan' : 'Monthly Plan' }}
                            @else
                                {{ $payment->borrowRecord->book->title ?? 'Deleted Book' }}
                            @endif
                        </td>
                        <td style="font-family:var(--font-mono); font-size:14px; font-weight:500;">₱{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            @if($payment->isCompleted())
                                <span class="badge badge-green">Paid</span>
                            @elseif($payment->isPending())
                                <span class="badge badge-yellow">Pending</span>
                            @else
                                <span class="badge badge-red">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                        <td style="color:var(--muted); font-size:12px; font-family:var(--font-mono); white-space:nowrap;">{{ ($payment->paid_at ?? $payment->created_at)->format('M d, Y') }}</td>
                        <td style="padding-right:24px;">
                            @if($payment->isCompleted() || $payment->isPending())
                                <a href="{{ route('payments.receipt', $payment->id) }}" style="font-size:13px; display:inline-flex; align-items:center; gap:4px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    Receipt
                                </a>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:48px 24px;">
                            <div style="font-size:32px; margin-bottom:10px; opacity:.5;">🧾</div>
                            <p class="muted">No payments yet.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
        <div style="margin-top:16px;">
            {{ $payments->links() }}
        </div>
    @endif
@endsection


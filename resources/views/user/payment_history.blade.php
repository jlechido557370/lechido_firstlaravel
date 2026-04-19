@extends('layouts.app')
@section('title', 'Payment History')

@section('content')
<div class="card">
    <p style="margin-bottom: 8px;"><a href="{{ route('user.dashboard') }}">&larr; Back to Dashboard</a></p>
    <h1>Payment History</h1>
    <p class="muted">All your fine payments and subscription payments.</p>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Details</th>
                <th>Method</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Receipt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                @php
                    $isSubscription = $payment->type === 'subscription';
                    $methodLabels = [
                        'gcash'         => 'GCash',
                        'maya'          => 'Maya',
                        'bank_transfer' => 'Bank Transfer',
                        'cash'          => 'Cash at Counter',
                    ];
                    $methodLabel = $methodLabels[$payment->payment_method ?? ''] ?? ucfirst($payment->payment_method ?? 'N/A');
                @endphp
                <tr>
                    <td style="font-family: monospace;">#{{ $payment->id }}</td>
                    <td>
                        @if($isSubscription)
                            <span class="badge" style="background:#ede9fe; color:#7c3aed;">Subscription</span>
                        @else
                            <span class="badge" style="background:#fee2e2; color:#dc2626;">Fine</span>
                        @endif
                    </td>
                    <td>
                        @if($isSubscription)
                            {{ $payment->subscription_plan === 'yearly' ? 'Yearly Plan (12 months)' : 'Monthly Plan (1 month)' }}
                            @if($payment->subscription_expires_at)
                                <br><span class="muted" style="font-size:12px;">Valid until {{ $payment->subscription_expires_at->format('M d, Y') }}</span>
                            @endif
                        @else
                            {{ $payment->borrowRecord->book->title ?? 'Deleted Book' }}
                        @endif
                    </td>
                    <td>{{ $methodLabel }}</td>
                    <td><strong>₱{{ number_format($payment->amount, 2) }}</strong></td>
                    <td>
                        @if($payment->isCompleted())
                            <span class="badge badge-green">Paid</span>
                        @elseif($payment->isPending())
                            <span class="badge badge-yellow">Pending</span>
                        @else
                            <span class="badge badge-red">{{ ucfirst($payment->status) }}</span>
                        @endif
                    </td>
                    <td style="font-size: 13px;">{{ ($payment->paid_at ?? $payment->created_at)->format('M d, Y H:i') }}</td>
                    <td>
                        @if($payment->isCompleted() || $payment->isPending())
                            <a href="{{ route('payments.receipt', $payment->id) }}"
                               style="padding: 5px 12px; background: #111827; color: white; border-radius: 5px; font-size: 13px; text-decoration: none; white-space: nowrap;">
                                View Receipt
                            </a>
                        @else
                            <span class="muted" style="font-size: 13px;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align: center;">No payment history yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($payments->hasPages())
        <div style="margin-top: 16px;">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
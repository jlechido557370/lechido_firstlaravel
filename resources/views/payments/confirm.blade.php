@extends('layouts.app')

@section('title', 'Confirm Payment')

@section('content')
    <div class="card">
        <h1>Pay Fine</h1>
        <p class="muted">Finverse payment gateway is not configured. Use the form below to submit your payment record manually. Staff will verify and confirm your payment.</p>
    </div>

    <div class="card">
        <table>
            <tr><td><strong>Book</strong></td><td>{{ $borrowing->book->title ?? 'N/A' }}</td></tr>
            <tr><td><strong>Due Date</strong></td><td>{{ $borrowing->due_date?->format('M d, Y') }}</td></tr>
            <tr><td><strong>Fine Amount</strong></td><td><strong>₱{{ number_format($fine, 2) }}</strong></td></tr>
            <tr><td><strong>Payment ID</strong></td><td>#{{ $payment->id }}</td></tr>
        </table>

        <div class="flash warning" style="margin-top:16px;">
            Finverse API is not configured. This will record your payment intent. Contact the library staff to complete payment physically and have your fine cleared.
        </div>

        <form method="POST" action="{{ route('payments.manual_confirm', $payment->id) }}" style="margin-top:16px;">
            @csrf
            <button type="submit">Acknowledge and Submit Payment Record</button>
        </form>

        <p style="margin-top:12px;"><a href="{{ route('user.dashboard') }}">Back to Dashboard</a></p>
    </div>
@endsection
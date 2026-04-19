<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'borrow_record_id',
        'type',
        'amount',
        'status',
        'payment_method',
        'finverse_payment_id',
        'finverse_link_id',
        'payment_url',
        'finverse_response',
        'failure_reason',
        'paid_at',
        'subscription_plan',
        'subscription_expires_at',
    ];

    protected $casts = [
        'amount'                  => 'decimal:2',
        'finverse_response'       => 'array',
        'paid_at'                 => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrowRecord()
    {
        return $this->belongsTo(BorrowRecord::class, 'borrow_record_id');
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isFailed(): bool    { return $this->status === 'failed'; }
    public function isSubscription(): bool { return $this->type === 'subscription'; }
    public function isFine(): bool      { return $this->type === 'fine' || $this->type === null; }
}
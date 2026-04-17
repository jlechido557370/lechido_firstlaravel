<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'fine_amount',
        'fine_paid',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date'    => 'datetime',
        'returned_at' => 'datetime',
        'fine_amount' => 'decimal:2',
        'fine_paid'   => 'boolean',
    ];

    const FINE_PER_DAY = 5.00;

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'borrow_record_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        return is_null($this->returned_at) && $this->due_date?->isPast();
    }

    public function getIsDueSoonAttribute(): bool
    {
        if ($this->returned_at || !$this->due_date) return false;
        $hoursLeft = now()->diffInHours($this->due_date, false);
        return $hoursLeft >= 0 && $hoursLeft <= 24;
    }

    public function calculateFine(): float
    {
        if (!$this->due_date) return 0;
        $endDate  = $this->returned_at ?? now();
        $daysLate = max(0, $this->due_date->diffInDays($endDate, false));
        return $daysLate > 0 ? round($daysLate * self::FINE_PER_DAY, 2) : 0;
    }

    public function hasPendingPayment(): bool
    {
        return $this->payments()->where('status', 'pending')->exists();
    }
}
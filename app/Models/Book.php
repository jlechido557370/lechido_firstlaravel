<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'genre',
        'published_year',
        'total_copies',
        'available_copies',
        'description',
        'read_url',
        'google_books_id',
    ];

    protected $casts = [
        'published_year'   => 'integer',
        'total_copies'     => 'integer',
        'available_copies' => 'integer',
    ];

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function editHistories()
    {
        return $this->hasMany(BookEditHistory::class)->latest();
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class)->latest();
    }

    public function getStatusAttribute(): string
    {
        return $this->available_copies > 0 ? 'available' : 'unavailable';
    }

    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }
}
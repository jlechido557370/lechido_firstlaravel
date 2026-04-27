<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title', 'author', 'genre', 'genres', 'published_year',
        'isbn', 'isbn_13', 'isbn_10',
        'total_copies', 'available_copies',
        'description', 'read_url', 'google_books_id', 'cover_image',
        'book_type',
    ];

    protected $casts = [
        'published_year'   => 'integer',
        'total_copies'     => 'integer',
        'available_copies' => 'integer',
        'genres'           => 'array',
    ];

    public function borrowRecords() { return $this->hasMany(BorrowRecord::class); }
    public function bookmarks()     { return $this->hasMany(Bookmark::class); }
    public function editHistories() { return $this->hasMany(BookEditHistory::class)->latest(); }
    public function reservations()  { return $this->hasMany(Reservation::class); }
    public function reviews()       { return $this->hasMany(BookReview::class)->latest(); }

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0)->where('status', 'available');
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

    /**
     * Returns a usable cover image URL.
     */
    public function coverUrl(): string
    {
        if ($this->cover_image) {
            if (str_starts_with($this->cover_image, 'http')) {
                return $this->cover_image;
            }
            if (file_exists(storage_path('app/public/' . $this->cover_image))) {
                return asset('storage/' . $this->cover_image);
            }
        }

        if ($this->google_books_id) {
            return 'https://books.google.com/books/content?id='
                . urlencode($this->google_books_id)
                . '&printsec=frontcover&img=1&zoom=1&source=gbs_api';
        }

        $isbn  = $this->isbn_13 ?: $this->isbn_10 ?: $this->isbn;
        $clean = preg_replace('/[^0-9X]/', '', $isbn ?? '');
        if (strlen($clean) >= 10 && is_numeric(rtrim($clean, 'X'))) {
            return 'https://covers.openlibrary.org/b/isbn/' . $clean . '-L.jpg';
        }

        return $this->noImageSvg();
    }

    public function noImageSvg(): string
    {
        // Generate a pleasant, deterministic color from the title hash
        $hash = md5($this->title ?? 'book');
        $hue = hexdec(substr($hash, 0, 2)) % 360;
        $bgLight = "hsl({$hue}, 55%, 92%)";
        $bgDark  = "hsl({$hue}, 45%, 85%)";
        $iconColor = "hsl({$hue}, 50%, 55%)";

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="600" viewBox="0 0 400 600">'
             . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="' . $bgLight . '"/><stop offset="100%" stop-color="' . $bgDark . '"/></linearGradient></defs>'
             . '<rect width="400" height="600" fill="url(#g)" rx="12"/>'
             . '<g fill="none" stroke="' . $iconColor . '" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" opacity="0.55">'
             . '<path d="M120 180h160M120 230h160M120 280h100"/>'
             . '<rect x="120" y="340" width="160" height="140" rx="8"/>'
             . '<path d="M160 380l40 40 40-40"/>'
             . '</g>'
             . '<text x="200" y="560" font-family="Georgia,serif" font-size="28" fill="' . $iconColor . '" text-anchor="middle" opacity="0.7">' . htmlspecialchars($this->title ?? 'Untitled') . '</text>'
             . '</svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function getIsbnDisplayAttribute(): array
    {
        $isbn13 = $this->isbn_13;
        $isbn10 = $this->isbn_10;

        if (!$isbn13 && !$isbn10) {
            $raw = preg_replace('/[^0-9X]/', '', $this->isbn ?? '');
            if (strlen($raw) === 13 && is_numeric($raw)) {
                $isbn13 = $raw;
            } elseif (strlen($raw) === 10) {
                $isbn10 = $raw;
            }
        }

        return ['isbn_13' => $isbn13, 'isbn_10' => $isbn10];
    }
}

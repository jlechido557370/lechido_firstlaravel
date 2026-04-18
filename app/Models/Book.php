<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'isbn', 'isbn_13', 'isbn_10', 'genre', 'genres',
        'published_year', 'total_copies', 'available_copies',
        'description', 'read_url', 'google_books_id', 'cover_image',
        'book_type', 'manga_id',
    ];

    protected $casts = [
        'published_year'   => 'integer',
        'total_copies'     => 'integer',
        'available_copies' => 'integer',
        'genres'           => 'array',
    ];

    public function borrowRecords()  { return $this->hasMany(BorrowRecord::class); }
    public function bookmarks()      { return $this->hasMany(Bookmark::class); }
    public function editHistories()  { return $this->hasMany(BookEditHistory::class)->latest(); }
    public function reservations()   { return $this->hasMany(Reservation::class); }
    public function reviews()        { return $this->hasMany(BookReview::class)->latest(); }

    public function getStatusAttribute(): string
    {
        return $this->available_copies > 0 ? 'available' : 'unavailable';
    }

    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }

    public function isManga(): bool { return $this->book_type === 'manga'; }
    public function isComic(): bool { return $this->book_type === 'comic'; }

    /**
     * Whether to show a separate type badge alongside the genre badge.
     * Returns false when genre already represents the type (manga = Manga, comic = Comic).
     */
    public function showTypeBadge(): bool
    {
        if ($this->book_type === 'book') return false;
        return strtolower($this->genre ?? '') !== $this->book_type;
    }

    /**
     * Returns a usable cover image URL.
     * Priority: stored cover → Google Books → Open Library by ISBN → SVG placeholder
     */
    public function coverUrl(): string
    {
        // 1. Stored or external cover URL
        if ($this->cover_image) {
            if (str_starts_with($this->cover_image, 'http')) {
                return $this->cover_image;
            }
            if (file_exists(storage_path('app/public/' . $this->cover_image))) {
                return asset('storage/' . $this->cover_image);
            }
        }

        // 2. Google Books API thumbnail
        if ($this->google_books_id) {
            return 'https://books.google.com/books/content?id='
                . urlencode($this->google_books_id)
                . '&printsec=frontcover&img=1&zoom=1&source=gbs_api';
        }

        // 3. Open Library by real ISBN (skip fake MANGA-xxx / COMIC-xxx ISBNs)
        $isbn = $this->isbn_13 ?: $this->isbn_10 ?: $this->isbn;
        $clean = preg_replace('/[^0-9X]/', '', $isbn ?? '');
        if (strlen($clean) >= 10 && is_numeric(rtrim($clean, 'X'))) {
            return 'https://covers.openlibrary.org/b/isbn/' . $clean . '-L.jpg';
        }

        // 4. SVG "image not available" placeholder — no external request, always works
        return $this->noImageSvg();
    }

    /**
     * Returns a data URI SVG for when no cover exists.
     */
    public function noImageSvg(): string
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="190">'
             . '<rect width="128" height="190" fill="#e5e7eb"/>'
             . '<text x="64" y="80" font-family="Arial,sans-serif" font-size="11" fill="#9ca3af" text-anchor="middle">image</text>'
             . '<text x="64" y="98" font-family="Arial,sans-serif" font-size="11" fill="#9ca3af" text-anchor="middle">not</text>'
             . '<text x="64" y="116" font-family="Arial,sans-serif" font-size="11" fill="#9ca3af" text-anchor="middle">available</text>'
             . '</svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Returns both ISBN-13 and ISBN-10 for display, deriving from isbn field if needed.
     */
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBook extends Model
{
    protected $fillable = [
        'user_id', 'title', 'author', 'isbn', 'genre', 'genres',
        'published_year', 'description', 'cover_image',
        'read_url', 'status', 'reviewed_by', 'reviewed_at', 'rejection_reason',
        'book_type',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'genres'      => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

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
        return 'https://via.placeholder.com/128x180/e5e7eb/6b7280?text=' . urlencode(mb_substr($this->title, 0, 12));
    }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
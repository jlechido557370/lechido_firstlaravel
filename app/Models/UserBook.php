<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBook extends Model
{
    protected $fillable = [
        'user_id', 'title', 'author', 'isbn', 'genre',
        'published_year', 'description', 'cover_image',
        'read_url', 'status', 'reviewed_by', 'reviewed_at', 'rejection_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
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
        if ($this->cover_image && file_exists(storage_path('app/public/' . $this->cover_image))) {
            return asset('storage/' . $this->cover_image);
        }
        return 'https://via.placeholder.com/128x180/e5e7eb/6b7280?text=' . urlencode($this->title);
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}
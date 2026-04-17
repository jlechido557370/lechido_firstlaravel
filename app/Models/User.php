<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'role', 'avatar', 'bio', 'gender', 'allow_dms',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'allow_dms'         => 'boolean',
        ];
    }

    public function borrowRecords()   { return $this->hasMany(BorrowRecord::class); }
    public function bookmarks()       { return $this->hasMany(Bookmark::class); }
    public function bookReviews()     { return $this->hasMany(BookReview::class); }
    public function payments()        { return $this->hasMany(Payment::class); }
    public function userBooks()       { return $this->hasMany(UserBook::class); }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(UserNotification::class)->whereNull('read_at');
    }

    // Users this user is following
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
                    ->withTimestamps();
    }

    // Users following this user
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
                    ->withTimestamps();
    }

    public function authorFollows()
    {
        return $this->hasMany(AuthorFollow::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function isFollowingAuthor(string $authorName): bool
    {
        return $this->authorFollows()->where('author_name', $authorName)->exists();
    }

    public function unreadMessageCount(): int
    {
        return Message::where('receiver_id', $this->id)->whereNull('read_at')->count();
    }

    public function isAdmin(): bool        { return $this->role === 'admin'; }
    public function isStaff(): bool        { return $this->role === 'staff'; }
    public function isAdminOrStaff(): bool { return in_array($this->role, ['admin', 'staff']); }

    public function displayName(): string
    {
        return $this->username ?? $this->name;
    }

    public function avatarUrl(): string
    {
        if ($this->avatar && file_exists(storage_path('app/public/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->displayName()) . '&background=111827&color=fff&size=128';
    }
}
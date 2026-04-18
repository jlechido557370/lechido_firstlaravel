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
        'hide_real_name', 'is_subscribed', 'subscription_expires_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'password'                 => 'hashed',
            'allow_dms'                => 'boolean',
            'hide_real_name'           => 'boolean',
            'is_subscribed'            => 'boolean',
            'subscription_expires_at'  => 'datetime',
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

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function authorFollows()   { return $this->hasMany(AuthorFollow::class); }
    public function sentMessages()    { return $this->hasMany(Message::class, 'sender_id'); }
    public function receivedMessages(){ return $this->hasMany(Message::class, 'receiver_id'); }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function blockedBy()
    {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function isFollowingAuthor(string $authorName): bool
    {
        return $this->authorFollows()->where('author_name', $authorName)->exists();
    }

    public function isBlocking(User $user): bool
    {
        return $this->blocks()->where('blocked_id', $user->id)->exists();
    }

    public function isBlockedBy(User $user): bool
    {
        return $this->blockedBy()->where('blocker_id', $user->id)->exists();
    }

    public function unreadMessageCount(): int
    {
        return Message::where('receiver_id', $this->id)->whereNull('read_at')->count();
    }

    public function isAdmin(): bool        { return $this->role === 'admin'; }
    public function isStaff(): bool        { return $this->role === 'staff'; }
    public function isAdminOrStaff(): bool { return in_array($this->role, ['admin', 'staff']); }

    public function isSubscribed(): bool
    {
        if (!$this->is_subscribed) return false;
        if ($this->subscription_expires_at && $this->subscription_expires_at->isPast()) {
            $this->update(['is_subscribed' => false]);
            return false;
        }
        return true;
    }

    public function displayName(): string
    {
        return $this->username ?? $this->name;
    }

    // For public display: hides real name if user opted in
    public function publicDisplayName(): string
    {
        if ($this->hide_real_name) {
            return $this->username ?? 'User';
        }
        return $this->username ?? $this->name;
    }

    // Appends + for subscribers
    public function badgedName(): string
    {
        $name = $this->displayName();
        return $this->isSubscribed() ? $name . '+' : $name;
    }

    public function avatarUrl(): string
    {
        if ($this->avatar && file_exists(storage_path('app/public/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->displayName()) . '&background=111827&color=fff&size=128';
    }
}
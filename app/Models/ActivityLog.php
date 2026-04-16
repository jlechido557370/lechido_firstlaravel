<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'description', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Shorthand to record a log entry.
     */
    public static function record(string $action, string $description, array $meta = []): void
    {
        static::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'meta'        => $meta ?: null,
        ]);
    }
}
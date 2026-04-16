<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookEditHistory extends Model
{
    protected $fillable = ['book_id', 'user_id', 'field_changed', 'old_value', 'new_value', 'action'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $fillable = ['title', 'author', 'book_type', 'description', 'cover_image', 'genres'];

    protected $casts = [
        'genres' => 'array',
    ];

    public function volumes()
    {
        return $this->hasMany(Book::class)->whereNotNull('volume_number')->orderBy('volume_number');
    }

    public function chapters()
    {
        return $this->hasMany(Book::class)->whereNotNull('chapter_number')->orderBy('chapter_number');
    }

    public function allEntries()
    {
        return $this->hasMany(Book::class)->orderBy('volume_number')->orderBy('chapter_number');
    }
}
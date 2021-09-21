<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status', 'content', 'image', 'published_at'];

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', 'NOW()');
    }
}

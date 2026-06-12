<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'category', 'excerpt', 'content',
        'cover_image', 'author_name', 'author_avatar', 'author_title',
        'read_time', 'views', 'is_published', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published'  => 'boolean',
    ];

    public function comments()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }
}

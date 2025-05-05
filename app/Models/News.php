<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class News extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'source_id',
        'source_name',
        'author',
        'title',
        'description',
        'url',
        'url_to_image',
        'published_at',
        'content',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * If you need a mutator to parse incoming published_at strings via Carbon:
     */
    public function setPublishedAtAttribute($value)
    {
        // if it's already a Carbon instance or timestamp, Laravel will handle it
        $this->attributes['published_at'] = Carbon::parse($value);
    }
}

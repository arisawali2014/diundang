<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['like', 'comments'];
    protected $appends = ['love'];
    public function getCreatedAtAttribute($value)
    {
        // example
        return Carbon::parse($value)->diffForHumans();
    }
    public function like()
    {
        return $this->hasMany(Like::class);
    }

    public function getLoveAttribute()
    {
        return $this->like()->count();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'parent_id','own');
    }
}

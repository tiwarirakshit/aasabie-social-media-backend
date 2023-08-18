<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post_gallery extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'source_path'];

    public function post()
    {
        return $this->belongsTo(Post::class,'post_id','id');
    }

    protected $appends = ['full_source_path'];
    public function getFullSourcePathAttribute()
    {
        if (!empty($this->source_path)) {
        return url(Storage::url($this->source_path));
    }
     return null;
    }
}

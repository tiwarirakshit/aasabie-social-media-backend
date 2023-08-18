<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','photo','video' ,'text', 'story_time'];

    public function User()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    protected $appends = ['full_photo_path','full_video_path'];
    public function getFullPhotoPathAttribute()
    {
        if (!empty($this->photo)) {
        return url(Storage::url($this->photo));
    }
     return null;
    }
    public function getFullVideoPathAttribute()
    {
        if (!empty($this->video)) {
        return url(Storage::url($this->video));
    }
     return null;
    }
}

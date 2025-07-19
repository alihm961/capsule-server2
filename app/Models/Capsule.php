<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capsule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'message', 'image_path', 'audio_path',
        'ip_address', 'location', 'country_id', 'mood_id',
        'is_public', 'is_surprise', 'reveal_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mood()
    {
        return $this->belongsTo(Mood::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
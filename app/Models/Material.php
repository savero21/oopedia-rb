<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'created_by'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}
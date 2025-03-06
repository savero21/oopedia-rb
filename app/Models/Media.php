<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['material_id', 'media_type', 'media_url', 'media_description'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
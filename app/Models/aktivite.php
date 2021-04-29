<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class aktivite extends Model
{
    protected $fillable = ['title','done'];

    public function aktivite_images(){
        return $this->hasMany(aktivite_image::class);
    }
}

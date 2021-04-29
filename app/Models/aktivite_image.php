<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class aktivite_image extends Model
{
    protected $fillable = ['aktivite_id','img_path'];

    public function aktivite(){
        return $this->belongsTo(aktivite::class);
    }
}

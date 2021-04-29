<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class admin extends Model
{
    protected $fillable = ['username','img_path','password', 'remember_token'];

    protected $hidden = [
        'password',
    ];
}

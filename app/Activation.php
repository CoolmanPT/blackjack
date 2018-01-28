<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{

    protected $fillable = [
        'id', 'user_id', 'token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

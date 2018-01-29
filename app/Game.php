<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'id', 'status', 'total_players', 'created_by'
    ];

    public function players(){
        return $this->belongsToMany('App\User');
    }

    public function scopeTerminated($query){
        return $query->where('status', 'terminated');
    }
}

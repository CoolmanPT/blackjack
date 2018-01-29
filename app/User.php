<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'nickname',
        'admin',
        'blocked',
        'reason_blocked',
        'reason_blocked',
        'reason_reactivated'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopePlayer($query){
        return $query->where('admin', 0);
    }

    public function gameLost(){
        return $this->belongsToMany('App\Game', 'game_user', 'user_id','game_id')
            ->wherePivot('game_result',3);
    }

    public function gameTies(){
        return $this->belongsToMany('App\Game', 'game_user', 'user_id','game_id')
            ->wherePivot('game_result',2);
    }

    public function gameWins(){
        return $this->belongsToMany('App\Game', 'game_user', 'user_id','game_id')
            ->wherePivot('game_result',1);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    protected $fillable = [
        'id', 'name', 'hidden_face_image_path', 'active', 'complete'
    ];

    public function cards(){
        return $this->hasToMany('App\Card');
    }

    public function games(){
        return $this->hasMany(Game::class, 'deck_used');
    }

}

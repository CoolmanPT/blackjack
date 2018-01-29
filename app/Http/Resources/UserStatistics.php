<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserStatistics extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'nome' => $this->name,
            'email' => $this->email,
            'username' => $this->nickname,
            'jogos' => $this->total_games_played,
            'vitorias' => $this->game_wins_count,
            'empates' => $this->game_ties_count,
            'derrotas' => $this->game_lost_count,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TotalGamesPerDate extends Resource
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
            'gameCount' => $this->game_count,
            'date' => $this->date,
        ];
    }
}

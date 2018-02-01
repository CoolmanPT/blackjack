<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class DeckResource extends Resource
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
            'id' => $this->id,
            'name' => $this->name,
            'hidden_face_image_path' => $this->hidden_face_image_path,
            'active' => $this->active,
            'complete' => $this->complete,
        ];
    }
}

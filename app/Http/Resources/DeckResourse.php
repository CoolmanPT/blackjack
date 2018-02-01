<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class DeckResourse extends Resource
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
            'hiddenFaceImagePath' => $this->hidden_face_image_path,
            'complete' => $this->complete,
            'active' => $this->active,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ConfigurationResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $response = [
            'email' => $this->platform_email,
        ];
        $properties = json_decode($this->platform_email_properties, true);

        $response = array_merge($response, $properties);

        return $response;
    }
}

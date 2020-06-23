<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
            'categories' => CategoryResource::collection($this->categories),
            'genres' => GenreResource::collection($this->genres),
            'video_file_url' => $this->video_file_url,
            'tumb_file_url' => $this->tumb_file_url,
            'banner_file_url' => $this->banner_file_url,
            'trailer_file_url' => $this->trailer_file_url,
        ];
    }
}

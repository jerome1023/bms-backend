<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'what' => $this->what,
            'where' => $this->where,
            'who' => $this->who,
            'when' => $this->when,
            'details' => $this->details,
            'image' => $this->image,
            'archive_status' => $this->archive_status
        ];
    }
}

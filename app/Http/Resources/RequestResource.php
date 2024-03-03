<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'user_id' => $this->user_id,
            'fullname' => $this->fullname,
            'age' => $this->age,
            'document_id' => new DocumentResource($this->document),
            'purpose' => $this->purpose,
            'sitio_id' => new SitioResource($this->sitio),
            'income' => $this->income,
            'status' => $this->status,
            'archive_status' => $this->archive_status,
            'created_at' => $this->created_at
        ];
    }
}

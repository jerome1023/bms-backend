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
            'document' => $this->document->id,
            'document_name' => $this->document->name,
            'purpose' => $this->purpose,
            'sitio' => $this->sitio->id,
            'sitio_name' => $this->sitio->name,
            'income' => $this->income,
            'price' => $this->price,
            'status' => $this->status,
            'reason' => $this->reason,
            'date' => $this->date ? $this->date->format('Y-m-d g:i a') : null,
        ];
    }
}

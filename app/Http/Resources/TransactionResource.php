<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'fullname' => $this->fullname,
            'user_id' => $this->user_id,
            'document' => $this->document_id,
            'document_name' => $this->document->name,
            'purpose' => $this->purpose,
            'price' => $this->price,
            'archive_status' => $this->archive_status,
            'created_at' => $this->created_at->format('Y-m-d g:i a'),
        ];
    }
}

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
            'user_id' => $this->user_id,
            'document_id' => $this->ladocument_idstname,
            'purpose' => $this->purpose,
            'price' => $this->price,
            'archive_status' => $this->archive_status,
            'created_at' => $this->created_at,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlotterResource extends JsonResource
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
            'complainant' => $this->complainant,
            'complainant_age' => $this->complainant_age,
            'complainant_address' => $this->complainant_address,
            'complainant_contact_number' => $this->complainant_contact_number,
            'complainee' => $this->complainee,
            'complainee_age' => $this->complainee_age,
            'complainee_address' => $this->complainee_address,
            'complainee_contact_number' => $this->complainee_contact_number,
            'date' => $this->date,
            'complain' => $this->complain,
            'agreement' => $this->agreement,
            'namagitan' => $this->namagitan,
            'witness' => $this->witness,
            'status' => $this->status,
            'archive_status' => $this->archive_status
        ];
    }
}

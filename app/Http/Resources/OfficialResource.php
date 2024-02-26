<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficialResource extends JsonResource
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
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'lastname' => $this->lastname,
            'gender' => $this->gender,
            'position' => $this->position,
            'birthdate' => $this->birthdate,
            'sitio_id' => $this->sitio_id,
            'start_term' => $this->start_term,
            'end_term' => $this->end_term,
            'archive_status' => $this->archive_status,
        ];
    }
}

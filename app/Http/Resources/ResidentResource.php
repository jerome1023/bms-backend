<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidentResource extends JsonResource
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
            'fullname' => $this->full_name,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'birthplace' => $this->birthplace,
            'civil_status' => $this->civil_status,
            'religion' => $this->religion,
            'educational_attainment' => $this->educational_attainment,
            'sitio' => $this->sitio->id,
            'sitio_name' => $this->sitio->name,
            'house_number' => $this->house_number,
            'occupation' => $this->occupation,
            'nationality' => $this->nationality,
            'voter_status' => $this->voter_status,
            'archive_status' => $this->archive_status,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'birthdate',
        'birthplace',
        'civil_status',
        'religion',
        'educational_status',
        'sitio_id',
        'house_number',
        'occupation',
        'nationality',
        'voter_status',
        'archive_status'
    ];

    public function sitio()
    {
        return $this->belongsTo(Sitio::class);
    }
}

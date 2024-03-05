<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blotter extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'complainant',
        'complainant_age',
        'complainant_address',
        'complainant_contact_number',
        'complainee',
        'complainee_age',
        'complainee_address',
        'complainee_contact_number',
        'date',
        'complain',
        'agreement',
        'official_id',
        'witness',
        'status',
        'archive_status'
    ];
}

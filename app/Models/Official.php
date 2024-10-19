<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    use HasFactory, HasUuids;

    // protected $table = 'official';
    // protected $keyType = 'string';
    // public $incrementing = false;

    protected $fillable = [
        'id',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'position',
        'birthdate',
        // 'sitio_id',
        'start_term',
        'end_term',
        'archive_status'
    ];

    public function blotters()
    {
        return $this->hasMany(Blotter::class, 'namagitan');
    }
}

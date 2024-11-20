<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'position',
        'birthdate',
        'start_term',
        'end_term',
        'archive_status'
    ];

    public function getFullNameAttribute()
    {
        $prefix = in_array($this->position, ['Kalihim', 'Ingat Yaman']) ? null : 'Hon. ';
        return "{$prefix}{$this->firstname} {$this->middlename} {$this->lastname}";
    }

    public function blotters()
    {
        return $this->hasMany(Blotter::class, 'namagitan');
    }
}

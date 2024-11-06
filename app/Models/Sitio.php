<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sitio extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name'
    ];

    public function requests()
    {
        return $this->hasMany(Request::class, 'sitio_id');
    }

    public function residents()
    {
        return $this->hasMany(Resident::class, 'sitio_id');
    }
}

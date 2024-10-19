<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sitio extends Model
{
    use HasFactory;

    // protected $table = 'sitio';
    protected $keyType = 'string';
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

    // public function officials()
    // {
    //     return $this->hasMany(Official::class);
    // }

    public function residents()
    {
        return $this->hasMany(Resident::class, 'sitio_id');
    }
}

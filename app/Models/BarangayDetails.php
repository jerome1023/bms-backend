<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayDetails extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    
    protected $fillable = [
        'id',
        'name',
        'image',
        'logo'
    ];
}

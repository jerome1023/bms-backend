<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'price'
    ];

    public function requests()
    {
        return $this->hasMany(Request::class, 'document_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'document_id');
    }
}

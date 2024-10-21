<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'fullname',
        'user_id',
        'age',
        'document_id',
        'purpose',
        'sitio_id',
        'income',
        'price',
        'status',
        'reason',
        'archive_status',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function sitio()
    {
        return $this->belongsTo(Sitio::class, 'sitio_id');
    }
}

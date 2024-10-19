<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */

    // protected $table = 'role';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}

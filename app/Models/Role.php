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

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

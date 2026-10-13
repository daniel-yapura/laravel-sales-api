<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    protected $table = 'sale';

    use SoftDeletes;

    protected $fillable = ['email', 'total', 'sale_date'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function concepts()
    {
        return $this->hasMany(Concept::class);

    }
}

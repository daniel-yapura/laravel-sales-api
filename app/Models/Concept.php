<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concept extends Model
{
    protected $table = 'concept';

    protected $fillable = ['quantity', 'price', 'product_id', 'sale_id'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}

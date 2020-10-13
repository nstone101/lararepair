<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'sale_items';

    public function repair()
    {
        return $this->belongsTo(\App\Repair::class);
    }
}

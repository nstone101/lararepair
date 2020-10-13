<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Costing extends Model
{
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $table = 'costing';
}

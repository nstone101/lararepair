<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class Unit extends Model
{
    use Searchable;
    protected $searchables = ['name', 'code'];

    protected $fillable = [
        'code',
        'name',
        'allow_decimal',
    ];
    protected $primaryKey = 'id';
    protected $table = 'units';

}

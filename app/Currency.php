<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class Currency extends Model
{
    use Searchable;
    protected $searchables = ['name', 'code'];

    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];
    protected $primaryKey = 'id';
    protected $table = 'currencies';

}

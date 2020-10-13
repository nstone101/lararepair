<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class Tax extends Model
{
    use Searchable;
    protected $searchables = ['name', 'code'];

    protected $fillable = [
        'code',
        'name',
        'rate',
        'type',
    ];
    protected $primaryKey = 'id';
    protected $table = 'tax_rates';

}

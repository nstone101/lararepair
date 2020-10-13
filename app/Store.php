<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class Store extends Model
{
    use Searchable;
    protected $searchables = ['name', 'code'];

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
    ];
    protected $primaryKey = 'id';
    protected $table = 'stores';

}

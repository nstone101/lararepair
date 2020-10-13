<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class Category extends Model
{
    use Searchable;
    protected $searchables = ['name', 'code'];

    protected $fillable = [
        'code',
        'name',
        'image',
        'parent_id',
        'slug',
        'description'
    ];
    protected $primaryKey = 'id';
    protected $table = 'categories';

}

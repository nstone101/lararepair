<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class Customer extends Model
{
 	protected $guarded = [];

 	use Searchable;
    protected $searchables = ['name', 'company'];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class SmsGateway extends Model
{
    protected $guarded = [];

    use Searchable;
    protected $searchables = ['name', 'notes'];
}

<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class SuspendedBill extends Model
{

    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'suspended_bills';

}

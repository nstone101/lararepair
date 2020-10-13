<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


use App\Http\Traits\Searchable;




class Sale extends Model
{

    use Searchable;

    
    protected $searchables = ['created_at','reference_no','biller','customer','sale_status','grand_total','paid','payment_status'];
    
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'sales';

}

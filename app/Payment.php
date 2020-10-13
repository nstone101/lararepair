<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;


class Payment extends Model {
    use Searchable;
	
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'payments';
}

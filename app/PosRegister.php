<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;
class PosRegister extends Model
{
    use Searchable;
	
	public $timestamps = false;
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $table = 'pos_registers';
    protected $searchables = ['cash_in_hand', 'cb.first_name', 'created_at'];

}

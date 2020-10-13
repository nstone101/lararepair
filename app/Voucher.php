<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;


class Voucher extends Model
{
    use Searchable;

    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'vouchers';
    protected $searchables = ['card_no', 'customer_id'];



}

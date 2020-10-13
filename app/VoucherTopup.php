<?php
namespace App;
use Illuminate\Database\Eloquent\Model;


class VoucherTopup extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'voucher_topups';
    protected $searchables = [''];
}

<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;
use App\Http\Traits\HasCustomFieldResponses;
use App\Http\Traits\HasCustomFields;


class Repair extends Model
{
    use Searchable;
	use HasCustomFieldResponses;
    use HasCustomFields;

    
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'repairs';
    protected $searchables = ['code', 'customer', 'serial_number', 'model', 'manufacturer', 'statuses.label', 'at.first_name', 'at.last_name'];


    public function items() {
        return $this->hasMany(\App\SaleItem::class);
    }
    public function payments() {
        return $this->hasMany(\App\Payment::class);
    }

    public function customer() {
        return $this->belongsTo(\App\Company::class, 'customer_id');
    }

    public function customerData() {
        return $this->belongsTo(\App\Company::class, 'customer_id');
    }

    public function status() {
        return $this->belongsTo(\App\Status::class);
    }

    public function assigned_user() {
        return $this->belongsTo(\App\Profile::class, 'assigned_to', 'user_id');
    }

    public function created_by() {
        return $this->belongsTo(\App\Profile::class, 'created_by', 'user_id');
    }

    public function updated_by() {
        return $this->belongsTo(\App\Profile::class, 'updated_by', 'user_id');
    }
    public function created_user() {
        return $this->belongsTo(\App\Profile::class, 'created_by', 'user_id');
    }

    public function updated_user() {
        return $this->belongsTo(\App\Profile::class, 'updated_by', 'user_id');
    }

  

 

}

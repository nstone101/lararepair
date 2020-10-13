<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\Searchable;
use App\Http\Traits\HasCustomFieldResponses;
use App\Http\Traits\HasCustomFields;


class Company extends Model {
    use Searchable;
    use SoftDeletes;
    use HasCustomFieldResponses;
    use HasCustomFields;

    protected $searchables = ['name'];

    protected $fillable = [
        'type',
        'name',
        'company',
        'tax_number',
        'tax_number2',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'invoice_footer',
        'logo',
        'created_by',
        'is_default',
    ];

    protected $primaryKey = 'id';
    protected $table = 'companies';

    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    public function scopeOnlySuppliers($query)
    {
        return $query->whereIn('companies.type', ['supplier']);
    }

    public function scopeOnlyCustomers($query)
    {
        return $query->whereIn('companies.type', ['customer']);
    }

    public function scopeOnlyBillers($query)
    {
        return $query->whereIn('companies.type', ['biller']);
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;
use App\Http\Traits\HasCustomFieldResponses;
use App\Http\Traits\HasCustomFields;
class Product extends Model
{
    use Searchable;
    use HasCustomFieldResponses;
    use HasCustomFields;
    
    protected $searchables = ['name', 'code'];
    protected $guarded = ['id'];

    protected $primaryKey = 'id';
    protected $table = 'products';


    public function costing()
    {
        return $this->hasMany(\App\Costing::class)->orderBy('created_at');
    }

    /**
     * Get the products image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            $image_url = asset('/uploads/img/' . $this->image);
        } else {
            $image_url = asset('/img/default.png');
        }
        return $image_url;
    }
    
    /**
    * Get the unit associated with the product.
    */
    public function unit()
    {
        return $this->belongsTo(\App\Unit::class);
    }
    /**
     * Get category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }
    /**
     * Get sub-category associated with the product.
     */
    public function sub_category()
    {
        return $this->belongsTo(\App\Category::class, 'sub_category_id', 'id');
    }
    
    /**
     * Get the brand associated with the product.
     */
    public function sellingTax()
    {
        return $this->belongsTo(\App\Tax::class, 'tax_id', 'id');
    }

    public function purchaseTax()
    {
        return $this->belongsTo(\App\Tax::class, 'purchase_tax_id', 'id');
    }

   


    /**
     * Scope a query to only include active products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('products.is_inactive', 0);
    }


}

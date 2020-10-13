<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('id');
        if ($this->method() === 'POST') {
            return [
                'code' => 'required|unique:products,code,'.$id.',id',
                'name' => 'required',
                'image' => 'image|nullable|max:1999',
                'barcode_symbology' => [ Rule::in(['C39', 'C128', 'EAN-13', 'EAN-8', 'UPC-A', 'UPC-E', 'ITF-14']) ],
                'model_id' => 'alpha_num',
                'category_id' => 'alpha_num',
                'subcategory_id' => 'alpha_num',
                'supplier_id' => 'alpha_num',
                'price_net' => 'numeric',
                'tax_id' => 'alpha_num',
                'price_gross' => 'numeric',
                'service' => [ Rule::in(['1', '0', true, false,'true', 'false']) ],
                'purchase_price_net' => 'numeric',
                'purchase_tax_id' => 'alpha_num',
                'purchase_price_gross' => 'numeric',
                'alert_quantity' => 'numeric',
                'quantity' => 'numeric',
                'description' => 'string',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'code' => 'required',
                'name' => 'required',
                'image' => 'image|nullable|max:1999',
                'barcode_symbology' => [ Rule::in(['C39', 'C128', 'EAN-13', 'EAN-8', 'UPC-A', 'UPC-E', 'ITF-14']) ],
                'model_id' => 'alpha_num',
                'category_id' => 'alpha_num',
                'subcategory_id' => 'alpha_num',
                'supplier_id' => 'alpha_num',
                'price_net' => 'numeric',
                'tax_id' => 'alpha_num',
                'price_gross' => 'numeric',
                'service' => [ Rule::in(['1', '0', true, false,'true', 'false']) ],
                'purchase_price_net' => 'numeric',
                'purchase_tax_id' => 'alpha_num',
                'purchase_price_gross' => 'numeric',
                'alert_quantity' => 'numeric',
                'quantity' => 'numeric',
                'description' => 'string',
            ];
        }
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('product.name'),
            'code' => trans('product.code'),
            'image' => trans('product.image'),
            'barcode_symbology' => trans('product.barcode_symbology'),
            'model_id' => trans('product.model_id'),
            'category_id' => trans('product.category_id'),
            'subcategory_id' => trans('product.subcategory_id'),
            'supplier_id' => trans('product.supplier_id'),
            'price_net' => trans('product.price_net'),
            'tax_id' => trans('product.tax_id'),
            'price_gross' => trans('product.price_gross'),
            'service' => trans('product.service'),
            'purchase_price_net' => trans('product.purchase_price_net'),
            'purchase_tax_id' => trans('product.purchase_tax_id'),
            'purchase_price_gross' => trans('product.purchase_price_gross'),
            'alert_quantity' => trans('product.alert_quantity'),
            'quantity' => trans('product.quantity'),
            'description' => trans('product.description'),
            'created_by' => trans('product.created_by'),
        ];
    }
}

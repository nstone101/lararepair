<?php
namespace App\Repositories;

use App\Product;
use Illuminate\Validation\ValidationException;
use App\Repositories\TaxRepository;
use App\Repositories\UnitRepository;
use App\Repositories\CategoryRepository;

class ProductRepository
{
    protected $product;
    protected $tax;
    protected $unit;
    protected $category;

    public function __construct(Product $product, TaxRepository $tax, UnitRepository $unit, CategoryRepository $category)
    {
        $this->product = $product;
        $this->tax = $tax;
        $this->unit = $unit;
        $this->category = $category;
    }


    public function preRequisite($id = null) {

        if ( $id ) {
             $fields = \App\CustomField::selectRaw('custom_fields.*, IF(custom_field_responses.value_int IS NOT NULL, custom_field_responses.value_int, IF(custom_field_responses.value_str IS NOT NULL, custom_field_responses.value_str, custom_field_responses.value_text)) as value')->where('custom_fields.model_type', get_class($this->product))
                ->leftJoin('custom_field_responses', 'custom_fields.id', '=', 'custom_field_responses.field_id')
                ->where('custom_field_responses.model_id', $id)
                ->orWhere('custom_field_responses.id', null)
                ->get();
        }else{
            $fields = \App\CustomField::where('model_type', get_class($this->product))->get();
        }
       

        $system_variables = getVar('system');
        $barcodes = isset($system_variables['barcodes']) ? $system_variables['barcodes'] : [];
        $tax_methods = isset($system_variables['tax_methods']) ? $system_variables['tax_methods'] : [];
        $types = isset($system_variables['product_types']) ? $system_variables['product_types'] : [];
        $categories = generateSelectOption($this->category->listParentCategories());
        $taxes = $this->tax->listTaxRates();
        $units = $this->unit->listUnits();

        return (compact('categories', 'barcodes', 'taxes', 'tax_methods', 'types', 'units', 'fields'));

    }
    public function listProducts($query)
    {                
        return $this->product->where('name', 'like', $query.'%')->select('*')->get()->toArray();
    }

    public function getProductSuggestionns($query)
    {                
        $products = $this->product->where('name', 'like', $query.'%')->select('*')->get();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'product_id' => $product->id,
                'product_code' => $product->code,
                'product_name' => $product->name,
                'product_type' => @$product->service ? 'service' : 'standard',
                'net_unit_price' => $product->price_net,
                'unit_price' => $product->price_gross,
                'quantity' => $product->quantity,
                'qty' => 1,
                'item_tax' => $product->price_gross - $product->price_net,
                'taxrate_id' => $product->tax_id,
                'tax' => '',
                'discount' => '',
                'item_discount' => 0,
                'subtotal' => $product->price_gross,
                'purchase_price_gross' => $product->purchase_price_gross,
                'comment' => '',

            ];
        }
        return $data;
    }

    public function create($request, $register = 0) {
        $params = $request->except('responses');

        $params['created_by'] = \Auth::user()->id;
        $params['service'] = @$params['service'] ? 1 : 0;
        if (isset($params['image'])) {
            $imageName = md5($request->image->getClientOriginalName().time()).'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/products'), $imageName);
            $params['image'] = $imageName;
        }
        

        $product = Product::create($params);

        if ($request->input('responses')) {
            $responses = json_decode($request->input('responses'));
            \App\CustomFieldResponse::where('model_id', $product->id)->where('model_type', get_class($this->product))->delete();

            $validation = $this->product->validateCustomFields($responses, get_class($this->product));
            if ($validation->fails()) {
                $product->delete();
                throw ValidationException::withMessages((array) $validation->errors()->messages());
            }else{
                $this->product->saveCustomFields($validation->validated(), $product->id);
            }
        }


        $this->updateStock($product->id, $params['quantity']);
        return $product;
    }


    public function updateStock($id, $quantity) {
        $product = \App\Product::find($id);
        if ($product && $quantity > 0) {
            $data = array(
                'product_id'    =>  $product->id,
                'code'  =>  $product->code,
                'name'  =>  $product->name,
                'cost'  =>  $product->purchase_price_gross,
                'quantity'      =>  $quantity,
            );
            \App\Costing::create($data);
        }
    }

    public function update(Product $product, $request)
    {
        $params = $request->except('responses');
        $params['service'] = @$params['service'] ? 1 : 0;
        if (isset($params['image'])) {
            $imageName = md5($request->image->getClientOriginalName().time()).'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/products'), $imageName);
            $params['image'] = $imageName;
        }
        $product->forceFill($params, 'update')->save();

        if ($request->input('responses')) {
            $responses = json_decode($request->input('responses'));
            \App\CustomFieldResponse::where('model_id', $product->id)->where('model_type', get_class($this->product))->delete();

            $validation = $this->product->validateCustomFields($responses, get_class($this->product));
            if ($validation->fails()) {
                throw ValidationException::withMessages((array) $validation->errors()->messages());
            }else{
                $this->product->saveCustomFields($validation->validated(), $product->id);
            }
        }
        return $product;
    }
       
    public function findOrFail($id) {
        $product = $this->product->with(['sellingTax', 'purchaseTax', 'costing'])->find($id);
        if (! $product) {
            throw ValidationException::withMessages(['message' => trans('product.could_not_find')]);
        }

        $available = 0;

        for ($i=0; $i < sizeof($product->costing); $i++) { 
            $available += $product->costing[$i]->quantity;
            $product->costing[$i]->current += $available;
        }

        return $product;
    }

    public function delete(Product $product)
    {
        return $product->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->product->whereIn('id', $ids)->delete();
    }

}

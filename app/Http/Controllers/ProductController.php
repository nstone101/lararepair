<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\CostingRequest;
use App\Repositories\ProductRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\Site;
use App\Product;
use App\Http\Requests\ImportRequest;
use Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;
    protected $category;
    protected $site;

    protected $module = 'product';

    public function __construct(Request $request, ProductRepository $repo, ActivityLogRepository $activity, CategoryRepository $category, Site $site)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
        $this->category = $category;
        $this->site = $site;
    }




    public function importProducts(ImportRequest $request)
    {
        $data = $request->csv;
        
        $rules_langs = [
            'name' => trans('product.name'),
            'code' => trans('product.code'),
            'image' => trans('product.image'),
            'barcode_symbology' => trans('product.barcode_symbology'),
            'category_id' => trans('product.category_id'),
            'subcategory_id' => trans('product.subcategory_id'),
            'supplier_id' => trans('product.supplier_id'),
            'price_net' => trans('product.price_net'),
            'tax_id' => trans('product.tax_id'),
            'service' => trans('product.service'),
            'purchase_price_net' => trans('product.purchase_price_net'),
            'purchase_tax_id' => trans('product.purchase_tax_id'),
            'alert_quantity' => trans('product.alert_quantity'),
            'quantity' => trans('product.quantity'),
            'description' => trans('product.description'),
            'created_by' => trans('product.created_by'),
        ];


        $rules = [
            'name' => 'required',
            'code' => 'required|unique:products,code',
            'image' => 'string',
            'barcode_symbology' => [ Rule::in(['C39', 'C128', 'EAN-13', 'EAN-8', 'UPC-A', 'UPC-E', 'ITF-14']) ],
            'category_id' => 'alpha_num',
            'subcategory_id' => 'alpha_num',
            'supplier_id' => 'string',
            'price_net' => 'numeric',
            'tax_id' => 'alpha_num',
            'service' => [ Rule::in(['1', '0']) ],
            'purchase_price_net' => 'numeric',
            'purchase_tax_id' => 'alpha_num',
            'alert_quantity' => 'numeric',
            'quantity' => 'numeric',
            'description' => 'string',
        ];


        $import = [];
        $row_number = 1;
        foreach ($data as $row) {
            $validator = Validator::make($row, $rules, $rules_langs);
            if ($validator->passes()) {
                $category_id = isset($row['category_id']) ? $row['category_id'] : null;
                $subcategory_id = isset($row['subcategory_id']) ? $row['subcategory_id'] : null;
                $supplier_id = isset($row['supplier_id']) ? $row['supplier_id'] : null;
                $tax_id = isset($row['tax_id']) ? $row['tax_id'] : null;
                $purchase_tax_id = isset($row['purchase_tax_id']) ? $row['purchase_tax_id'] : null;
                unset($row['category_id'],$row['subcategory_id'],$row['supplier_id'],$row['tax_id'],$row['purchase_tax_id']);

                if ($category_id) {
                    $category = \App\Category::where('code', $category_id)->first();
                    if ($category) {
                        $row['category_id'] = $category->id;
                    }
                }
                
                if ($subcategory_id) {
                    $subcategory = \App\Category::where('code', $subcategory_id)->first();
                    if ($subcategory) {
                        $row['subcategory_id'] = $subcategory->id;
                    }
                }

                if ($supplier_id) {
                    $supplier = \App\Company::where('name','LIKE', "%$supplier_id%")->first();
                    if ($supplier) {
                        $row['supplier_id'] = $supplier->id;
                    }
                }

                $row['price_gross'] = $row['price_net'];
                if ($tax_id) {
                    $tax = \App\Tax::where('code', $tax_id)->first();
                    if ($tax) {
                        $row['tax_id'] = $tax->id;

                        $tax = $this->calculateTax($tax, $row['price_net']);
                        $row['price_gross'] = $row['price_net'] + $tax['amount'];
                    }
                }
                
                $row['purchase_price_net'] = $row['purchase_price_net'] > 0 ? $row['purchase_price_net'] : 0;
                $row['quantity'] = $row['quantity'] > 0 ? $row['quantity'] : 0;
                $row['purchase_price_gross'] = $row['purchase_price_net'];
                if ($purchase_tax_id) {
                    $purchase_tax = \App\Tax::where('code', $purchase_tax_id)->first();
                    if ($purchase_tax) {
                        $row['purchase_tax_id'] = $purchase_tax->id;
                        $purchase_tax = $this->calculateTax($purchase_tax, $row['purchase_price_net'] > 0 ? $row['purchase_price_net'] : 0);
                        $row['purchase_price_gross'] = ($row['purchase_price_net'] > 0 ? $row['purchase_price_net'] : 0) + ($purchase_tax['amount'] > 0 ? $purchase_tax['amount'] : 0);
                    }
                }


                $row['created_by'] = \Auth::user()->id;
                $import[$row['code']] = $row;
            } else {
                $error = [];
                foreach ($validator->errors()->messages() as $key => $validation) {
                    $error[$key] = '';
                    foreach ($validation as $msg) {
                        $error[$key] .= $msg . ' (on line ' .$row_number . ')';
                    }
                }
                return $this->error($error);
            }
            $row_number++;
        }

        if (!empty($import)) {
            foreach ($import as $product) {
                $row = \App\Product::create($product);
                if ($row) {
                    if ($row->quantity > 0) {
                        $this->repo->updateStock($row->id, $row->quantity);
                    }
                }
            }
            return $this->success(['message' => trans('product.import_completed')]);
        }
        return $this->error(['message' => trans('product.import_complete_error')]);
    }




    public function importProductStock(ImportRequest $request)
    {
        $data = $request->csv;
        
        $rules_langs = [
            'code' => trans('product.code'),
            'purchase_price_net' => trans('product.purchase_price_net'),
            'purchase_tax_id' => trans('product.purchase_tax_id'),
            'quantity' => trans('product.quantity'),
        ];


        $rules = [
            'code' => 'required',
            'purchase_price_net' => 'numeric',
            'purchase_tax_id' => 'alpha_num',
            'quantity' => 'numeric',
        ];


        $import = [];
        $row_number = 1;
        foreach ($data as $row) {
            $validator = Validator::make($row, $rules, $rules_langs);
            if ($validator->passes()) {
                $purchase_tax_id = isset($row['purchase_tax_id']) ? $row['purchase_tax_id'] : null;
                unset($row['purchase_tax_id']);

                $row['purchase_price_gross'] = $row['purchase_price_net'];
                if ($purchase_tax_id) {
                    $purchase_tax = \App\Tax::where('code', $purchase_tax_id)->first();
                    if ($purchase_tax) {
                        $row['purchase_tax_id'] = $purchase_tax->id;

                        $purchase_tax = $this->calculateTax($purchase_tax, $row['purchase_price_net']);
                        $row['purchase_price_gross'] = $row['purchase_price_net'] + $purchase_tax['amount'];
                    }
                }
                $product = \App\Product::where('code', $row['code'])->first();
                $import[] = [
                    'product_id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'cost' => $row['purchase_price_gross'],
                    'quantity' => $row['quantity'],
                ];
            } else {
                $error = [];
                foreach ($validator->errors()->messages() as $key => $validation) {
                    $error[$key] = '';
                    foreach ($validation as $msg) {
                        $error[$key] .= $msg . ' (on line ' .$row_number . ')';
                    }
                }
                return $this->error($error);
            }
            $row_number++;
        }

        if (!empty($import)) {
            http_response_code(400);
            foreach ($import as $stock) {
                \App\Costing::create($stock);
                $this->site->syncProductQty($stock['product_id']);
            }
            return $this->success(['message' => trans('product.import_completed')]);
        }
        return $this->error(['message' => trans('product.import_complete_error')]);
    }


    public function calculateTax($tax_details, $value) {
        $tax_amount = 0; $tax = 0;
        if ($tax_details && $tax_details->type == 1 && $tax_details->rate != 0) {
            $tax_amount = formatNumber((($value) * $tax_details->rate) / 100, 4);
            $tax = $tax_details->name;
        } elseif ($tax_details && $tax_details->type == 2) {
            $tax_amount = formatNumber($tax_details->rate);
            $tax = $tax_details->name;
        }
        return ['amount'=>$tax_amount, 'tax'=>$tax];
    }
    public function addStock(CostingRequest $request, $id) {
        $quantity = $request->input('quantity');
        $this->repo->updateStock($id, $quantity);
        return $this->success(['message' => trans('product.updated_stock')]);
    }


    public function getProducts() {
        $query = $this->request->input('query');
        $products = $this->repo->listProducts($query);
        return $this->success(compact('products'));
    }

    public function getProductSuggestionns() {
        $query = $this->request->input('query');
        $products = $this->repo->getProductSuggestionns($query);
        return $this->success(compact('products'));
    }
    

    public function preRequisite(Request $request) {
        $id = $request->get('id') ?? null;
        return $this->success($this->repo->preRequisite($id));
    }


    public function getSubCategory() {
        $query = $this->request->input('query');
        $category_id = $this->request->input('category_id');
        $categories = generateSelectOption($this->category->listSubCategoriesByID($category_id, $query));
        return $this->success(compact('categories'));
    }


    public function index()
    {
        $this->middleware('permission:list-product');
        $query = Product::search()
                ->selectRaw('id, code, name, quantity,purchase_price_gross, price_gross');

        return dataTable()->query($query)
            ->setFilters(
                'products.name', 'products.code'
            )
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(ProductRequest $request)
    {
        $this->middleware('permission:create-product');

        $product = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $product->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('product.added')]);
    }


    public function update(ProductRequest $request, $id) {
        $this->middleware('permission:edit-product');

        $product = $this->repo->findOrFail($id);
        $product = $this->repo->update($product, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $product->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('product.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-product');

        $product = $this->repo->findOrFail($id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $product->id,
            'activity' => 'deleted'
        ]);

        try {  
            $this->repo->delete($product);
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error(['message' => 'cannot delete']);
        } 

        

        return $this->success(['message' => trans('product.deleted')]);
    }

}

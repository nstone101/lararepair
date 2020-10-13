<?php
namespace App\Repositories;

use Illuminate\Validation\ValidationException;
use App\Company;
use App\Repair;
use App\Product;
use App\CustomField;
class CustomFieldRepository
{
    protected $company;
    protected $repair;
    protected $product;
    protected $customField;

    public function __construct(Company $company, Repair $repair, Product $product, CustomField $customField)
    {
        $this->company = $company;
        $this->repair = $repair;
        $this->product = $product;
        $this->customField = $customField;
    }

    public function create($request) {
        $params = $request->all();
        $customField = \App\CustomField::create([
            'model_type' => $params['model_type'],
            'title' => $params['title'],
            'type' => $params['type'],
            'answers' => $params['answers'],
            'required' => $params['required'] ? 1 : 0,
            'order' => 1,
        ]);
        
        return $customField;
    }

    public function update(CustomField $customField, $params) {
        $params = $params->all();
        $customField->forceFill($params, 'update')->save();
        return $customField;
    }
       
    public function findOrFail($id) {
        $customField = $this->customField->find($id);

        if (!$customField) {
            throw ValidationException::withMessages(['message' => trans('customField.could_not_find')]);
        }

        return $customField;
    }

    public function delete(CustomField $customField)
    {
        return $customField->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->customField->whereIn('id', $ids)->delete();
    }
}
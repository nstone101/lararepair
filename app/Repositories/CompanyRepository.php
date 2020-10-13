<?php
namespace App\Repositories;

use App\Company;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class CompanyRepository
{
    protected $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function preRequisite($id = null) {
        if ( $id ) {
             $cf = \App\CustomField::selectRaw('custom_fields.*, IF(custom_field_responses.value_int IS NOT NULL, custom_field_responses.value_int, IF(custom_field_responses.value_str IS NOT NULL, custom_field_responses.value_str, custom_field_responses.value_text)) as value')->where('custom_fields.model_type', get_class($this->company))
                ->leftJoin('custom_field_responses', 'custom_fields.id', '=', 'custom_field_responses.field_id')
                ->where('custom_field_responses.model_id', $id)
                ->orWhere('custom_field_responses.id', null)
                ->get();
            return $cf;
        }
       
        $cf = \App\CustomField::where('model_type', get_class($this->company))
            ->get();

        return $cf;

    }


    public function create($request, $type = 'customer') {
        $params = $request->except(['responses']);
        
        $params['created_by'] = \Auth::user()->id;
        $params['type'] = $type;
        $company = $this->company->forceCreate($params);

        if (($request->get('responses'))) {
            $responses = $request->get('responses');
            $validation = $this->company->validateCustomFields($responses, get_class($this->company));
            if ($validation->fails()) {
                $company->delete();
                throw ValidationException::withMessages((array) $validation->errors()->messages());
            }else{
                $this->company->saveCustomFields($validation->validated(), $company->id);
            }
        }

        return $company;
    }

    public function update($id, $params) {
        $company = \App\Company::find($id);
        $inputes = $params->except(['responses']);
        $company->update($inputes);


        if (($params->get('responses'))) {
            // rermove previous
            \App\CustomFieldResponse::where('model_id', $company->id)->where('model_type', get_class($this->company))->delete();
            $responses = $params->get('responses');
            $validation = $this->company->validateCustomFields($responses, get_class($this->company));
            if ($validation->fails()) {
                throw ValidationException::withMessages((array) $validation->errors()->messages());
            }else{
                $this->company->saveCustomFields($validation->validated(), $company->id);
            }
        }

        return $company;
    }
       
    public function findOrFail($id) {
        $company = $this->company->find($id);

        $company->responses = \App\CustomFieldResponse::selectRaw('field_id, value_int, value_text, value_str')->where('model_type', get_class($this->company))->where('model_id', $id)->get();

        if (! $company) {
            throw ValidationException::withMessages(['message' => trans('company.could_not_find')]);
        }

        return $company;
    }

    public function delete(Company $company)
    {
        return $company->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->company->whereIn('id', $ids)->delete();
    }
}
<?php

namespace App\Http\Traits;

use App\Exceptions\FieldDoesNotBelongToModelException;
use App\Exceptions\WrongNumberOfFieldsForOrderingException;
use App\CustomField;
use App\Validators\CustomFieldValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait HasCustomFields
{
  

    public function validateCustomFields($fields, $class)
    {

        $records = \App\CustomField::where('model_type', $class)->get();
        $validationRules = $records->mapWithKeys(function ($field) {
            return ['field_' . $field->id => $field->validationRules];
        })->toArray();
        

        $keyAdjustedFields = collect($fields)
            ->mapWithKeys(function ($field, $key) {
                return ["field_{$key}" => $field];
            })->toArray();

        return new CustomFieldValidator($keyAdjustedFields, $validationRules);
    }
    

    public function order($fields)
    {
        // Allows us to pass in either an array or collection
        $fields = collect($fields);

        if ($fields->count() !== $this->customFields()->count()) {
            throw new WrongNumberOfFieldsForOrderingException(
                $fields->count(),
                $this->customFields()->count()
            );
        }

        $fields->each(function ($id, $index) {
            $customField = $this->customFields()->find($id);

            if (!$customField) {
                throw new FieldDoesNotBelongToModelException($id, $this);
            }

            $customField->update(['order' => $index + 1]);
        });
    }
}

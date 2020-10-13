<?php

namespace App\Http\Traits;
use App\CustomField;
use App\CustomFieldResponse;

trait HasCustomFieldResponses
{
  

    public function saveCustomFields($fields, $id)
    {
        foreach ($fields as $key => $value) {
            CustomFieldResponse::create([
                'value' => $value,
                'field_id' => CustomField::find((int) str_replace('field_', '', $key))->id,
                'model_id' => $id,
                'model_type' => get_class($this),
            ]);
        }
    }

   
}

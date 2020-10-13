<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
                'code' => 'required|unique:categories,code,'.$id.',id',
                'name' => 'required',
                'parent_id' => 'integer',
                'image' => 'image|nullable|max:1999',
                'description' => 'string'
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'code' => 'required',
                'name' => 'required',
                'parent_id' => 'integer',
                'image' => 'image|nullable|max:1999',
                'description' => 'string'
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
            'name' => trans('category.name'),
            'code' => trans('category.code'),
            'parent_id' => trans('category.parent_id'),
            'image' => trans('category.image'),
            'description' => trans('category.description'),
        ];
    }
}

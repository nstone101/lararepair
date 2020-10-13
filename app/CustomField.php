<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class CustomField extends Model
{
    use Searchable;
    use SoftDeletes;
	
	protected $searchables = ['title', 'answers', 'model_type'];
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    protected $table = 'custom_fields';
    protected $casts = [
        'answers' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        $this->bootIfNotBooted();
        $this->initializeTraits();
        $this->syncOriginal();
        $this->fill($attributes);

        $this->table = 'custom_fields';
    }

     private function fieldValidationRules($required)
    {
        return [
            'text' => [
                'string',
                'max:255',
            ],
            'textarea' => [
                'string',
            ],
            'select' => [
                'string',
                'max:255',
                Rule::in($this->answers),
            ],
            'number' => [
                'integer',
            ],
            'checkbox' => $required ? ['accepted','in:0,1'] : ['in:0,1'],
            'radio' => [
                'string',
                'max:255',
                Rule::in($this->answers),
            ],
        ];
    }

    public function responses()
    {
        return $this->hasMany(CustomFieldResponse::class, 'field_id');
    }

    public function getValidationRulesAttribute()
    {
        $typeRules = $this->fieldValidationRules($this->required)[$this->type];
        array_unshift($typeRules, $this->required ? 'required' : 'nullable');
        return $typeRules;
    }

    // public static function boot()
    // {
    //     parent::boot();
    //     self::creating(function ($field) {
    //         $lastFieldOnCurrentModel = $field->model->customFields()->orderBy('order', 'desc')->first();
    //         $field->order = ($lastFieldOnCurrentModel ? $lastFieldOnCurrentModel->order : 0) + 1;
    //     });
    // }


    

}

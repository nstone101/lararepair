<?php
namespace App\Repositories;

use App\Category;
use Illuminate\Validation\ValidationException;

class CategoryRepository
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }



    public function listParentCategories()
    {
        return $this->category->where('parent_id', null)->get()->pluck('name', 'id')->all();
    }

    public function listSubCategoriesByID($id, $query)
    {
        return $this->category->where('parent_id', $id)->get()->pluck('name', 'id')->all();
    }

    
    public function create($request, $register = 0)
    {
        // Handle File Upload
        $image_path = config('system.upload_path.categories').'/';
        if($request->hasFile('image')) {
            $extension = request()->file('image')->getClientOriginalExtension();
            $filename = uniqid();
            $file = request()->file('image')->move($image_path, $filename.".".$extension);
            $img = \Image::make($image_path.$filename.".".$extension);
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($image_path.$filename.".".$extension);

            $full_image = $filename.".".$extension;
        } else {
            $full_image = 'no-image.png';
        }



        $params = $request->all();
        if (!isset($params['parent_id']) || $params['parent_id'] == '') {
            $params['parent_id'] = null;
        }

        $params['image'] = $full_image;

        $params['created_by'] = \Auth::user()->id;

        $category = $this->category->forceCreate($params);
        return $category;
    }


    public function update(Category $category, $params)
    {



        // Handle File Upload
        $image_path = config('system.upload_path.categories').'/';
        if($params->hasFile('image')) {
            $extension = $params->file('image')->getClientOriginalExtension();
            $filename = uniqid();
            $file = $params->file('image')->move($image_path, $filename.".".$extension);
            $img = \Image::make($image_path.$filename.".".$extension);
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($image_path.$filename.".".$extension);

            $full_image = $filename.".".$extension;

            $params = $params->all();
            $params['image'] = $full_image;
        }else{
            $params = $params->all();
        }



        if (isset($params['parent_id']) && !is_numeric($params['parent_id'])) {
            $params['parent_id'] = null;
        }
       
        $category->forceFill($params, 'update')->save();
        return $category;
    }

       
    public function findOrFail($id)
    {
        $category = $this->category->find($id);

        if (! $category) {
            throw ValidationException::withMessages(['message' => trans('category.could_not_find')]);
        }

        return $category;
    }

    public function delete(Category $category)
    {
        return $category->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->category->whereIn('id', $ids)->delete();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\ActivityLogRepository;
use App\Category;

class CategoryController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'category';

    public function __construct(Request $request, CategoryRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

    }
  

    public function preRequisite()
    {

        $categories = generateSelectOption($this->repo->listParentCategories());

        return $this->success(compact('categories'));
    }
    public function index()
    {
        $this->middleware('permission:list-category');
        $query = Category::search()
                ->selectRaw('categories.id, categories.image, categories.code, categories.name, (SELECT a.name FROM categories a WHERE categories.parent_id = a.id) as parent_id');

        return dataTable()->query($query)
            ->setFilters(
                'categories.name', 'categories.code'
            )
            ->configColumn('image', function ($value, $row) {
                return '/'.config('system.upload_path.categories') . '/' .$value;
            })
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(CategoryRequest $request)
    {
        $this->middleware('permission:create-category');

        $category = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $category->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('category.added')]);
    }


    public function update(CategoryRequest $request, $id) {
        $this->middleware('permission:edit-category');

        $category = $this->repo->findOrFail($id);
        $category = $this->repo->update($category, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $category->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('category.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-category');

        $category = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $category->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($category);

        return $this->success(['message' => trans('category.deleted')]);
    }

}

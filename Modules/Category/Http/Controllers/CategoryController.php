<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\CategoryTranslation;
use Modules\Core\Entities\Core;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Category\Entities\Category;

/**
 * Category Controller for the Category
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class CategoryController extends BaseController
{


    protected $pagination_limit;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
       // $this->middleware('admin');
    }

    /**
     * returns all the category
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        try {

            return $this->successResponse(200, Category::paginate($this->pagination_limit));
        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }

    }

    /**
     * Get the particular category
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            return $this->successResponse(200, Category::findOrFail($id));

        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());

        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

    /**
     * store the new category
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, Category::rules());
            //store
            $category = $this->saveCategory($request);
            $this->uploadImages($category);
            return $this->successResponse(201, $category, trans('core::app.response.create-success', ['name' => 'Category']));

        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->errors());

        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }



    private function saveCategory($request)
    {

        $category = new Category();
        $category = Category::create($request->all());

        //format data according to locales
        $locale_values = $request->get('locales');
        if(isset($locale_values) && is_array($locale_values)){
            foreach ($locale_values as $key => $item){
                $data = array_merge($item,
                    [
                        'locale' => $key,
                        'category_id' => $category->id,
                        'slug' => $request->slug
                    ]);
            }

            $category_translation = CategoryTranslation::create($data);
            $category->translations()->save($category_translation);
        }

        return $category;
    }

    public function uploadImages($category, $type = "image")
    {
        $data = request()->all();
        if (isset($data[$type])) {
            $request = request();
            $dir = 'category/' . $category->id;
            if ($request->hasFile('image')) {
                if ($category->{$type}) {
                    Storage::delete($category->{$type});
                }
                $category->{$type} = $request->file('image')->store($dir);
                $category->save();
            }

        }
    }

    /**
     * Update the category
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {

            $params = $request->all();
            $this->validate($request, Category::rules($id));
            $category = Category::findOrFail($id);
            $this->saveCategory($params);
            $this->uploadImages($category);
            return $this->successResponse(200, $category, trans('core::app.response.update-success', ['name' => 'Category']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(400, $exception->getMessage());

        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->errors());

        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }

    }

    /**
     * Remove the specified  admin resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $admin = Category::findOrFail($id);
            $admin->delete();
            return $this->successResponse(400, null, trans('core::app.response.create-success', ['name' => 'Category']));

        } catch (QueryException $exception) {
            return $this->errorResponse(400, $exception->getMessage());

        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }
}

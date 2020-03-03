<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            $categories = $this->successResponse(200, Category::paginate($this->pagination_limit));
            return $this->successResponse(200, $payload = $categories);
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
            return $this->errorResponse(500, $exception->getMessage());
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
            if ($request->image) {
                $this->uploadImage($category, $request->image);
            }

            return $this->successResponse(201, $category, trans('core::app.response.create-success', ['name' => 'Category']));

        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->errors());

        } catch (\Exception $exception) {
            return $this->errorResponse(500, $exception->getMessage());
        }
    }


    private function saveCategory($request)
    {

        $category = Category::create($request->only(['position', 'status', 'parent_id']));

        //format data according to locales
        $locale_values = $request->get('locales');
        if (isset($locale_values) && is_array($locale_values)) {
            foreach ($locale_values as $key => $item) {
                $data = array_merge($item,
                    [
                        'locale' => $key,
                        'category_id' => $category->id,
                        'slug' => $request->slug
                    ]);
            }

            if (is_array($data)) {
                $category_translation = CategoryTranslation::create($data);
                $category->translations()->save($category_translation);
            }
        }

        return $category;
    }

    public function uploadImage(Category $category, String $base64_image)
    {

        if ($category->image) {
            Storage::delete($category->image);
        }
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
            $base64_data = substr($base64_image, strpos($base64_image, ',') + 1);
            $base64_data = base64_decode($base64_data);
            $valid_path = "category/" . time() . Str::random(15) . ".png";
            Storage::disk('public')->put($valid_path, $base64_data, 'public');
            $category->image = $valid_path;
            $category->save();
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

            $this->validate($request, Category::rules($id));
            $category = Category::findOrFail($id);
            $this->updateCategory($category, $request);
            if (!$request->image) {
                $this->uploadImage($category, $request->image);
            }

            return $this->successResponse(200, $category, trans('core::app.response.update-success', ['name' => 'Category']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(400, $exception->getMessage());

        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->errors());

        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }

    }

    private function updateCategory($category, $request)
    {

        $category->update($request->only(['position', 'status', 'parent_id']));
        $category_translations = $category->translations;

        //format data according to locales
        $locale_values = $request->get('locales');

        if (isset($locale_values) && is_array($locale_values)) {
            foreach ($locale_values as $key => $item) {
                $data = array_merge($item,
                    [
                        'locale' => $key,
                        'category_id' => $category->id,
                        'slug' => $request->slug
                    ]);
                $category_translation = $category_translations->first(function ($category_translation) use ($key) {
                    return $category_translation->locale = $key;
                });
                $category_translation->update($data);

            }
        }

        return $category;
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

<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\CategoryTranslation;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Category\Entities\Category;

/**
 * Category Controller for the Category
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class CategoryController extends BaseController
{


    protected $pagination_limit, $locale, $folder_path;
    private $folder = 'category';

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('admin');
        $this->folder_path =  storage_path('app/public/images/'). $this->folder.DIRECTORY_SEPARATOR;
    }

    /**
     * returns all the category
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';

            $categories =  Category::with('translations');
            if ($request->has('q')) {
                $categories->whereLike(Category::$SEARCHABLE,$request->get('q'));
            }
            $categories->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit')? $request->get('limit'):$this->pagination_limit;
            $categories = $categories->paginate($limit);
            return $this->successResponse($payload = $categories);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Get the particular category
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $category = Category::with('translations')->findOrFail($id);
            return $this->successResponse($payload = $category);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Category']), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * store the new category
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            //validation
            $this->validate($request, Category::rules());

            //create slug if missing in input
            if(!$request->get('slug')){
                $request->merge(['slug' =>  Category::createSlug($request->get('name'))]);
            }

            //save category
            $category = Category::create(
                $request->only(['position', 'status', 'parent_id', 'slug'])
            );

            //upload image
            if ($request->image) {
                $category->image = $this->uploadImage($category, $request);
                $category->save();
            }
            //create or update related translation
            $this->createOrUpdateTranslation($category, $request);

            DB::commit();
            return $this->successResponse($category, trans('core::app.response.create-success', ['name' => 'Category']), 201);

        } catch (ValidationException $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Uploads an image
     * Uploading file with original for better seo
     * @param Category $category
     * @param $request
     */
    public function uploadImage(Category $category, $request)
    {

        if ($uploadedFile = $request->file('image')) {
            if (isset($category->image)) {
                $this->removeFile($this->folder_path.$category->image);
            }
             return $this->uploadFile($uploadedFile ,$this->folder_path);

        };

    }


    /**
     * Update the translation of category
     * Caution!!: createOrUpdate(built in laravel core) causes race condition
     * @param Category $category
     * @param Request $request
     */
    private function createOrUpdateTranslation(Category $category, Request $request)
    {
        $check_attributes = ['locale' => $this->locale, 'category_id' => $category->id];
        $request->merge($check_attributes);
        $category_translation = CategoryTranslation::firstorNew($check_attributes);
        $category_translation->fill(
            $request->only(['name', 'description', 'meta_title', 'meta_description', 'meta_keywords', 'locale', 'category_id'])
        );
        $category_translation->save();

    }

    /**
    /**


     * Update the category
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            //validate
            $this->validate($request, Category::rules($id));


            $category = Category::findOrFail($id);

            //upload image
            if ($request->file('image')) {
                $category->image = $this->uploadImage($category, $request);
                $category->save();
            }

            //update a category
            $category->update(
                $request->only(['position', 'status', 'parent_id', 'slug'])
            );

            //create or update translation
            $this->createOrUpdateTranslation($category, $request);

            DB::commit();
            return $this->successResponse($category, trans('core::app.response.update-success', ['name' => 'Category']), 200);

        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Category']), 404);

        } catch (ValidationException $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->errors(), 400);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Remove the specified  admin resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            //remove associated image file
            if (isset($category->image)) {
                $this->removeFile($this->folder_path . $category->image);
            }
            $category->delete();

            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Category']));
        }catch (ModelNotFoundException $exception){
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Category']), 404);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


}

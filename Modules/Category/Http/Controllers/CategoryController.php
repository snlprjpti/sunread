<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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


    protected $pagination_limit, $locale;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('admin');
    }

    /**
     * returns all the category
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {

            $categories = Category::paginate($this->pagination_limit);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return $this->successResponse($payload = $category);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
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

            DB::beginTransaction();

            //validation
            $this->validate($request, Category::rules());

            //save category
            $category = Category::create(
                $request->only(['position', 'status', 'parent_id', 'slug'])
            );

            //upload image
            if ($request->image) {
                $this->uploadImage($category, $request);
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
     * @param Category $category
     * @param $request
     */
    public function uploadImage(Category $category, $request)
    {

        if ($uploadedFile = $request->file('image')) {

            if (isset($category->image) && file_exists(storage_path('/app/public/'.$category->image))) {
                unlink(storage_path('/app/public/'.$category->image));
            }

            $filename = time() . Str::random(15) . ".png";
            $file_path = Storage::disk('public')->putFileAs(
                'category',
                $uploadedFile,
                $filename
            );
            $category->image = $file_path;
            $category->save();
        };


    }


    /**
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            //validate
            $this->validate($request, Category::rules($id));

            //update category
            $category = Category::findOrFail($id);

            //upload image
            if ($request->image) {
                $image_path = $this->uploadImage($category, $request);
                $request->merge(['image' => $image_path]);
            }

            $category->update(
                $request->only(['position', 'status', 'parent_id', 'slug', 'image'])
            );

            //create or update translation
            $this->createOrUpdateTranslation($category, $request);
            DB::commit();
            return $this->successResponse($category, trans('core::app.response.update-success', ['name' => 'Category']), 200);

        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 404);

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
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            if (isset($category->image) && file_exists(storage_path('/app/public/'.$category->image))) {
                unlink(storage_path('/app/public/'.$category->image));
            }
            $category->delete();
            return $this->successResponseWithMessage(trans('core::app.response.delete-success', ['name' => 'Category']));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


}

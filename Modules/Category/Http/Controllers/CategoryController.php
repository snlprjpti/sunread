<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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


    protected  $pagination_limit;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        //$this->middleware('admin');
    }

    /**
     * returns all the admins
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
     * Get the particular admin
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
     * store the new admin resource
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $this->validate(request(), [
                'slug' => ['required ','unique:category_translations,slug'],
                'name' => 'required',
                'image.*' => 'mimes:jpeg,jpg,bmp,png',
            ]);

            $categoryTransalation = new CategoryTranslation();
            $result = $categoryTransalation->where('name', request()->input('name'))->get();
            if(count($result) > 0) {
                $this->errorResponse(400, "Category with same name already exists");
            }

            $this->saveCategory($request->all());
            $this->uploadImages();

            $params = $request->all();
            $admin = Category::create($params);
            return $this->successResponse(201, $admin, trans('core::app.response.create-success', ['name' => 'Category']));
        } catch (ValidationException $exception) {
            return $this->errorResponse(400, $exception->errors());
        } catch (\Exception $exception) {
            return $this->errorResponse(400, $exception->getMessage());
        }
    }

    /**
     * Update the admin details
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {

            $params = $request->all();
            $this->validate($request, Category::rules($id));
            if (isset($params['password']) && $params['password']) {
                $params['password'] = bcrypt($params['password']);
            }
            $admin = Category::findOrFail($id);
            $admin = $admin->update($params);
            return $this->successResponse(200, $admin, trans('core::app.response.update-success', ['name' => 'Category']));
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

    private function saveCategory($params)
    {

        if (isset($data['locale']) && $data['locale'] == 'all') {


            foreach (config('locales') as $locale) {
                foreach ($model->translatedAttributes as $attribute) {
                    if (isset($data[$attribute])) {
                        $data[$locale][$attribute] = $data[$attribute];
                        $data[$locale->code]['locale_id'] = $locale->id;
                    }
                }
            }
        }


    }

}

<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\Category;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Repositories\ProductRepository;

class ProductController extends BaseController
{

    /**
     * AttributeRepository object
     */
    protected $pagination_limit,$productRepository;


    /**
     * ProductController constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        parent::__construct();
        $this->middleware('admin');
        $this->productRepository = $productRepository;
    }

    /**
     * Returns all the products
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $payload = $this->productRepository->paginate($this->pagination_limit);
            return $this->successResponse($payload);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Get the particular product
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            $product = $this->productRepository->findOrFail($id);
            $category_tree = (new Category())->getCategoryTree();
            $payload = ['product' => $product,'category_tree' => $category_tree];
            return $this->successResponse($payload);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }


    /**
     * store the particular resource
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {

            //validation
            $this->validate($request, [
                'type' => 'required',
                'attribute_family_id' => 'required|exists:attribute_families,id',
                'sku' => ['required', 'unique:products,sku'],
                'slug' => ['required','unique:products,slug']
            ]);

            //store product
            $product = $this->productRepository->store($request->all());

            return $this->successResponse($payload = $product, trans('core::app.response.create-success', ['name' => 'Product']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Update the products and variats
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {

        try{
            //fetch rules
            $rules = $this->productRepository->rules($id);

            //validate
            $this->validate($request,$rules);

            //update
            $product = $this->productRepository->update($request->all(),$id);

            return $this->successResponse($product,trans('core::app.response.update-success', ['name' => 'Product']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        }catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Delete a product item
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {

            $this->productRepository->delete($id);
            return $this->successResponseWithMessage("Product deleted success!!");

        } catch (ModelNotFoundException $exception){
            return $this->errorResponse($exception->getMessage(), 404);

        }catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}


<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\Category;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Entities\ProductFlat;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Transformers\ProductResource;

class ProductController extends BaseController
{

    /**
     * ProductRepository object
     */
    protected  $productRepository;

    /**
     * Pagination limit for resource
     */
    protected $pagination_limit;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {

            // validating data.
            $this->validate($request, [
                'limit' => 'sometimes|numeric',
                'page' => 'sometimes|numeric',
                'sort_by' => 'sometimes',
                'sort_order' => 'sometimes|in:asc,desc',
                'q' => 'sometimes|string|min:1'
            ]);

            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';
            $limit = $request->get('limit')? $request->get('limit'):$this->pagination_limit;
            $products = ProductFlat::query();
            if ($request->has('q')) {
                $products->whereLike(ProductFlat::SEARCHABLE, $request->get('q'));
            }
            $products->orderBy($sort_by, $sort_order);
            $products = $products->paginate($limit);
            return $this->successResponse($products, trans('core::app.response.fetch-list-success', ['name' => 'Product']));

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
            $product = $product->product_flats->first();
            $category_tree = (new Category())->getCategoryTree();
            $payload = ['product' => new ProductResource($product),'category_tree' => $category_tree];
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
                'type' => 'required|in:simple,configurable',
                'attribute_family_id' => 'required|exists:attribute_families,id',
                'sku' => ['required', 'unique:products,sku'],
                'slug' => ['required','unique:products,slug'],
            ]);

            //store product
            $product = $this->productRepository->store($request->all());

            return $this->successResponse($product, trans('core::app.response.create-success', ['name' => 'Product']), 201);

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


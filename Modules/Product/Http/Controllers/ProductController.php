<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Category\Entities\Category;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Product\Entities\Product;
use Modules\Product\Services\ProductImageRepository;

class ProductController extends BaseController
{

    protected $pagination_limit;
    protected $product,$productImage;

    /**
     * UserController constructor.
     * @param Product $product
     * @param ProductImageRepository $productImage
     */
    public function __construct(Product $product,ProductImageRepository $productImage)
    {
        parent::__construct();  
        $this->middleware('admin');
        $this->product = $product;
        $this->productImage = $productImage;
    }

    /**
     * returns all the admins
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $payload = Product::paginate($this->pagination_limit);
            return $this->successResponse($payload);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
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
            $product = Product::with(['variants'])->findOrFail($id);
            $category_tree = (new Category())->getCategoryTree();
            $payload = ['product' => $product,'category_tree' => $category_tree];
            return $this->successResponse($payload);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function store(Request $request)
    {

        try {

            $this->validate($request, [
                'type' => 'required',
                'attribute_family_id' => 'required',
                'sku' => ['required', 'unique:products,sku']
            ]);

            Event::dispatch('catalog.product.create.before');

            $typeInstance =  $this->product->getTypeInstance($request->get('type'));

            $product = $typeInstance->create($request->all());

            Event::dispatch('catalog.product.create.after', $product);

            return $this->successResponse($payload = $product, trans('core::app.response.create-success', ['name' => 'Product']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            dd($exception);
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function update(Request $request, $id)
    {

        try{

            $product = $this->product->findOrFail($id);

            //Event start Log
            Event::dispatch('catalog.product.update.before', $id);

            //validation
            $this->validate($request,Product::rules($id,[
                'main_image' => 'required|mimes:jpeg,jpg,bmp,png',
                'gallery_images.*' => 'nullable|mimes:jpeg,jpg,bmp,png',
                ]));

            //Get the type of product to update
            $productInstance = $product->getTypeInstance();

            //update the product according to type
            $product = $productInstance->update($request->all(), $id);


            //Event complete Log
            Event::dispatch('catalog.product.update.after', $product);

            return $this->successResponse($product,trans('core::app.response.update-success', ['name' => 'Product']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        }catch (\Exception $exception) {
            dd($exception);
            return $this->errorResponse($exception->getMessage());
        }

    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            //categories,attributes deleted at database level
            //removing image only
            $this->productImage->removeProductImages($product);

            $product->delete($id);

            return $this->successResponseWithMessage("Product deleted success!!");
        } catch (ModelNotFoundException $exception){
            return $this->errorResponse($exception->getMessage(), 404);

        }catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}


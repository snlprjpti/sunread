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
use Modules\Product\Http\Requests\ProductForm;

class ProductController extends BaseController
{

    protected $pagination_limit;
    protected $product,$category;

    /**
     * UserController constructor.
     * @param Product $product
     * @param Category $category
     */
    public function __construct(
        Product $product,
        Category $category

    )
    {
        parent::__construct();
        $this->middleware('admin');
        $this->product = $product;
        $this->category = $category;
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
            $product = $this->product->with(['variants'])->findOrFail($id);
            $category_tree = $this->category->getCategoryTree();
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

            $typeInstance = app(config('product_types.' . $request->get('type'). '.class'));

            $product = $typeInstance->create($request->all());

            Event::dispatch('catalog.product.create.after', $product);

            return $this->successResponse($product, trans('core::app.response.create-success', ['name' => 'Product']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->errors(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function update(ProductForm $request, $id)
    {
         $this->product->update(request()->all(), $id);

    }

    public function destroy($id)
    {
        try {
            $this->product->findOrFail($id);
            $this->product->delete($id);
            return $this->successResponseWithMessage("Product deleted success!!");
        } catch (\Exception $exception) {
            session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Product']));
            return $this->errorResponse($exception->getMessage());
        }


    }





}


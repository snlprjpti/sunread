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
use Modules\Product\Entities\ProductImage;
use Modules\Product\Exceptions\ProductImageDeleteException;
use Modules\Product\Services\ProductImageRepository;

class ProductImageController extends BaseController
{

    protected $pagination_limit ,$productImage;

    public function __construct(ProductImageRepository $productImage)
    {
        parent::__construct();
        $this->middleware('admin');
        $this->productImage = $productImage;
    }

    public function removeFile($productImageId)
    {
        try {
            $productImage =  ProductImage::findOrFail($productImageId);
            $isProductImageRemoved = $this->productImage->removeParticularProductImage($productImage);
            if(!$isProductImageRemoved){
                throw new ProductImageDeleteException();
            }
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Product Image']));

        } catch (ProductImageDeleteException $exception) {
            return $this->errorResponse("Image Could not be deleted", 500);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    public function uploadFile(Request $request)
    {
        try{
            $this->validate($request,[
                'product_id' => 'required|exists:products,id',
                'images.*' => 'nullable|mimes:jpeg,jpg,bmp,png',
            ]);
            $product = Product::findOrFail($request->get('product_id'));

            //check validation of image type
            //upload files here
            $this->productImage->uploadProductImages($product);
            $this->successResponse("Image changed success");

        }catch (ValidationException $exception){
            return $this->errorResponse($exception->getMessage(), 422);
        } catch (ProductImageDeleteException $exception) {
            return $this->errorResponse("Image Could not be deleted", 500);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }
    
}


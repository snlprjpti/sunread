<?php

namespace Modules\Cart\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Cart\Entities\Cart;
use Illuminate\Http\JsonResponse;
use Modules\Cart\Repositories\CartRepository;
use Modules\Cart\Exceptions\OutOfStockException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Cart\Exceptions\CartHashIdNotFoundException;
use Modules\Cart\Exceptions\ChannelDoesNotExistException;
use Elasticsearch\Common\Exceptions\Forbidden403Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Cart\Transformers\CartItemResource;
use Modules\Cart\Transformers\CartResource;

class CartController extends BaseController
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository, Cart $cart)
    {
        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');

        $this->model = $cart;
        $this->model_name = "Cart";
        $this->cartRepository = $cartRepository;

        $exception_statuses = [
            OutOfStockException::class => 404,
            ChannelDoesNotExistException::class => 404,
            CartHashIdNotFoundException::class => 404,
            Forbidden403Exception::class => 403
        ];
        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function resource(?object $data): JsonResource
    {
        return new CartResource($data);
    }

    public function collection(object $data): ResourceCollection
    {
        return CartResource::collection($data);
    }

    public function cartItemResource(object $data): JsonResource
    {
        return new CartItemResource($data);
    }

    public function cartItemCollection(object $data): ResourceCollection
    {
        return CartItemResource::collection($data);
    }

    public function addOrUpdateCart(Request $request): JsonResponse
    {
        try
        {
            $response = $this->cartRepository->addOrUpdateCart($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($response['cart']), $response['message']);
    }

    public function deleteProductFromCart(Request $request): JsonResponse
    {
        try
        {
            $response = $this->cartRepository->deleteProductFromCart($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        
        return $this->successResponse($this->resource($response['cart']), $response['message']);

    }

    public function getAllProductFromCart(Request $request): JsonResponse
    {
        try
        {
            $response = $this->cartRepository->getAllProductFromCart($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        
        return $this->successResponse($response, $this->lang('fetch-success'));

    }

    public function mergeCart(Request $request): JsonResponse
    {
        try
        {
            $response = $this->cartRepository->mergeCart($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        
        return $this->successResponse($this->resource($response['cart']), $response['message']);

    }
}

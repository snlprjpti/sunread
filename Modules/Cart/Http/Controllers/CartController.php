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

class CartController extends BaseController
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository, Cart $cart)
    {
        $this->cartRepository = $cartRepository;
        $this->model = $cart;
        $this->model_name = "Cart";

        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');

        $exception_statuses = [
            OutOfStockException::class => 404,
            ChannelDoesNotExistException::class => 404,
            CartHashIdNotFoundException::class => 404
        ];
        parent::__construct($this->model, $this->model_name, $exception_statuses);
    }

    public function addOrUpdateCart(Request $request): JsonResponse
    {
        try
        {
            $cartData = $this->cartRepository->addOrUpdateCart($request);
        } 

        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($cartData, $this->lang("create-success"));
    }

    public function deleteProductFromCart(Request $request): JsonResponse
    {
        try
        {
            $this->validate($request,[
                'product_id' => 'required|integer|min:1',
             ]);
       
            $response = $this->cartRepository->deleteProductFromCart($request);
        } 

        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($response, $this->lang("delete-success"));
    }

}

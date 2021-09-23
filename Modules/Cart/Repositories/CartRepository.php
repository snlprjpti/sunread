<?php

namespace Modules\Cart\Repositories;

use DB;
use Exception;
use Modules\Cart\Entities\Cart;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Channel;
use Modules\Cart\Entities\CartItem;

use Modules\Product\Entities\Product;
use Modules\Core\Repositories\BaseRepository;
use function GuzzleHttp\Promise\exception_for;
use Illuminate\Validation\ValidationException;
use Modules\Cart\Services\UserAuthCheckService;
use Modules\Cart\Exceptions\OutOfStockException;
use Modules\Cart\Exceptions\CartHashIdNotFoundException;
use Modules\Cart\Exceptions\ChannelDoesNotExistException;
use Modules\Product\Exceptions\ProductNotFoundIndividuallyException;

class CartRepository extends BaseRepository
{
    protected $cart, $userAuthCheckService, $cartItem, $product, $store, $responseMsg = '';

    public function __construct(Cart $cart, UserAuthCheckService $userAuthCheckService, CartItem $cartItem, Product $product, Store $store)
    {
        $this->model = $cart;
        $this->userAuthCheckService = $userAuthCheckService;
        $this->cartItem = $cartItem;
        $this->product = $product;
        $this->store = $store;
    }
    public function addOrUpdateCart(object $request)
    {
        
        DB::beginTransaction();
        try{
            
        /**
        * check if add/update on cart is by guest or logged in user
        **/ 
       $customer = $this->userAuthCheckService->validateUser($request);
       $request->merge(["customer_id" => $customer->id ?? null]);
       $productId = $request->product_id;
       if($request->type == 'update'){
        if(!array_key_exists('hc-cart', $request->header())) throw ValidationException::withMessages(["cart_hash_id" => "cart hash id is required"]);
        $cartHashId = $request->header()['hc-cart'][0];
        // if cart hash id exist in carts table 
        $cart = $this->model::where('id', $cartHashId)->select('id')->first();
        if($cart){
            $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
            // if product id already exist in the cart hash id
                $checkProductId = $this->cartItem->where($cartAndProductCheck)->select('qty')->first();
                if(!$checkProductId) throw new ProductNotFoundIndividuallyException();

                  //  check if passed qty in request 0 or not, if 0 then delete that product from cart_items table.
                    // if not 0 then update product quantity.

                    if($request->qty == 0){
                        $this->cartItem->where($cartAndProductCheck)->delete();
                        //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                        $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                        if(!$checkCartItemsExitsOnCartHashId)
                        {
                            $this->model->where('id', $cartHashId)->delete();
                        }

                    $this->responseMsg = "product removed from cart";
                    DB::commit();
                    return $this->responseMsg;
                    }
                    else{
                      // check product exist on product table, product status =1, has visibility and in stock
                    $this->checkProductConditions($productId, $request);

                    $qty =  ($checkProductId->qty)+ $request->qty ?? 1;
                    $this->cartItem->where($cartAndProductCheck)
                                    ->update([
                                        "qty" => $request->qty ?? 1
                                    ]);
                        $this->responseMsg = "product quantity updated on cart";
                        DB::commit();
                        return $this->responseMsg;
                        }

       } else{
            $this->checkProductConditions($productId, $request);
            $this->addProductOnCart($request);
        $this->responseMsg = "product added on cart";
        DB::commit();
        return $this->responseMsg;
       }
    }       
       else{

        if(isset($request->header()['hc-cart'])){
            $cartHashId = $request->header()['hc-cart'][0];

            // if cart hash id exist in carts table 
            $cart = $this->model::where('id', $cartHashId)->select('id')->first();
            if($cart){

                $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
            // if product id already exist in the cart hash id
                $checkProductId = $this->cartItem->where($cartAndProductCheck)->select('qty')->first();
                if($checkProductId){

                    //  check if passed qty in request 0 or not, if 0 then delete that product from cart_items table.
                    // if not 0 then update product quantity.

                    if($request->qty == 0){
                        $this->cartItem->where($cartAndProductCheck)->delete();
                        //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                        $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                        if(!$checkCartItemsExitsOnCartHashId)
                        {
                            $this->model->where('id', $cartHashId)->delete();
                        }

                    $this->responseMsg = "product removed from cart";
                    DB::commit();
                    return $this->responseMsg;

                    }
                    else{

                    // check product exist on product table, product status =1, has visibility and in stock
                    $this->checkProductConditions($productId, $request);

                    $qty =  ($checkProductId->qty)+ $request->qty ?? 1;
                    $this->cartItem->where($cartAndProductCheck)
                                    ->update([
                                        "qty" => $qty
                                    ]);
                        $this->responseMsg = "product quantity updated on cart";
                        DB::commit();
                        return $this->responseMsg;
                            }
                }

                if(isset($request->qty) && $request->qty == 0) throw ValidationException::withMessages(["quantity" => "product quantity must be greater than 0"]);

               // check product exist on product table, product status =1, in stock, same channel
               $this->checkProductConditions($productId, $request);
                   

            // if product does not exist on table of that cart hash id, add product on cart on that cart hash id:
                $this->cartItem::create([
                    "cart_id" => $cartHashId,
                    "product_id" => $productId,
                    "qty" => $request->qty ?? 1
                    ]);
             $this->responseMsg = "product added on cart";
             DB::commit();
             return $this->responseMsg;

            }
            // if cart hash id sent but not found on cart table
            else{
               // check product exist on product table, product status =1, in stock, same channel
               $this->checkProductConditions($productId, $request);

                $this->addProductOnCart($request);
             $this->responseMsg = "product added on cart";
             DB::commit();
             return $this->responseMsg;
             
            }
        }

        // if cart hash id is not sent, then create new cart 
        else{

            // check product exist on product table, product status =1, has visibility and in stock
            $this->checkProductConditions($productId, $request);
            $this->addProductOnCart($request);
            $this->responseMsg = "product added on cart";
            DB::commit();
            return $this->responseMsg;
        }

       }
    }
        catch(Exception $exception)
        {
            DB::rollback();
            $this->responseMsg = 'exception on add/update cart';
            throw $exception;
        }

        DB::commit();
        return $this->responseMsg;

    }

    private function addProductOnCart(object $request)
    {
        if(isset($request->qty) && $request->qty == 0) throw ValidationException::withMessages(["quantity" => "product quantity must be greater than 0"]);

            $checkIfUserHasCartAlready = $this->model::where('customer_id', $request->customer_id)->select('id')->first();
            if(isset($request->customer_id) && $checkIfUserHasCartAlready){

                // if product id already exist in that logged in user id
                $checkProductId = $this->cartItem->where('product_id', $request->product_id)->where('cart_id', $checkIfUserHasCartAlready->id)->select('qty')->first();
                if($checkProductId){
                 $qty =  ($checkProductId->qty)+ $request->qty ?? 1;
                 $this->cartItem->where('product_id', $request->product_id)->where('cart_id', $checkIfUserHasCartAlready->id)
                                 ->update([
                                     "qty" => $qty
                                 ]);
                                 $this->responseMsg = "Prodocut quantity updated on cart";
                                }
                else{
                $this->cartItem::create([
                    "cart_id" => $checkIfUserHasCartAlready->id,
                    "product_id" => $request->product_id,
                    "qty" => $request->qty ?? 1
                    ]);
                    $this->responseMsg = "Prodocut add on cart";

                }
            }
            
        else 
        {
        $cart = $this->model::create([
            "customer_id" => $request->customer_id,
            "channel_code" => $request->header()['hc-channel'][0],
            "store_code" => $request->header()['hc-store'][0],
            ]);
        $this->cartItem::create([
            "cart_id" => $cart->id,
            "product_id" => $request->product_id,
            "qty" => $request->qty ?? 1
            ]);
            $this->responseMsg = "Prodocut add on cart";
        }

        return true;
    }

    private function checkProductConditions(string $productId, object $request):bool
    {
          // check if product exist on product table
          $product = $this->checkProductStatus($productId);

        //   // check product visibility
        //   $this->productVisibility($product, $request);

        // check if product exits on channel
        $this->checkIfProductExistOnChannel($product, $request);

          // check stock of product
          $this->checkProductStock($product, $request);

          return true;
    }

    private function checkProductStatus(string $productId):object
    {
        $product = $this->product::where('id', $productId)->where('status', 1)->select('id')->first();
        if(!$product) throw new ProductNotFoundIndividuallyException();
        return $product;
    }

    // private function productVisibility(object $product, object $request)
    // {
    //     $store = $this->store->where('code', $request->header()['hc-store'][0])->select('id')->firstOrFail();
    //     $is_visibility = $product->value([
    //         "scope" => "store",
    //         "scope_id" => $store->id,
    //         "attribute_slug" => "visibility"
    //     ]);
    //     if($is_visibility?->name != "Not Visible Individually") throw new ProductNotFoundIndividuallyException();
    //     else true;
    // }

    private function checkIfProductExistOnChannel(object $product, object $request)
    {
        $channel = Channel::where('code', $request->header()['hc-channel'][0])->select('id')->first();
        if(!$channel) throw new ChannelDoesNotExistException('channel not found');
        $checkProductOnChannel = DB::table('channel_product')->where('channel_id', $channel->id)
                                            ->where('product_id', $request->product_id)->select('channel_id')
                                            ->first();
        if($checkProductOnChannel) throw new ProductNotFoundIndividuallyException(); 
        
        return true;
    }

    private function checkProductStock(object $product, object $request):mixed
    {
        $productStock = $product->catalog_inventories()->first();
        $qty = $request->qty ?? 1;
        if($productStock && $productStock->manage_stock && $productStock->is_in_stock && $qty > $productStock->quantity){
                throw new OutOfStockException('not enough quantity in stock');
            }
        return true;
    }

    public function deleteProductFromCart(object $request)
    {
        try{
        /**
        * check if user is on guest or logged mode
        **/ 
       $customer = $this->userAuthCheckService->validateUser($request);
       $request->merge(["customer_id" => $customer->id ?? null]);
        $productId = $request->product_id;

        if(isset($request->header()['hc-cart'])){
          $cartHashId = $request->header()['hc-cart'][0];

          // if cart hash id exist in carts table
          $cart = $this->model::where('id', $cartHashId)->select('id')->first();
          if($cart){
            $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
            // if product id exist in the cart hash id, delete that product
                $checkProductId = $this->cartItem->where($cartAndProductCheck)->first();
                if($checkProductId){
                    $this->cartItem->where($cartAndProductCheck)->delete();
                     //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                     $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                     if(!$checkCartItemsExitsOnCartHashId)
                     {
                         $this->model->where('id', $cartHashId)->delete();
                     }
                    $this->responseMsg = 'product deleted';
                    DB::commit();
                    return $this->responseMsg;
                } 
                else{
                    $this->responseMsg = 'product not found';
                    throw new ProductNotFoundIndividuallyException;
                }
          }    
          // if cart hash id is sent but not found on cart table case:
          else {
                $this->deleteProductAsPerCustomerMode($request);
                DB::commit();
                return $this->responseMsg;
          }
        }
        // if cart hash id is not sent case:
            else {
                $this->deleteProductAsPerCustomerMode($request);
                DB::commit();
                return $this->responseMsg;
            }
    }
    
    catch(Exception $exception)
    {
        DB::rollback();
        $this->responseMsg = 'exception on product delete';
        throw $exception;
    }

    DB::commit();
    return $this->responseMsg;
}

private function deleteProductAsPerCustomerMode(object $request)
{  
     $customerId = $request->customer_id;
    if($customerId){
   $checkIfUserHasCartAlready = $this->model::where('customer_id', $customerId)->select('id')->first();
        if($checkIfUserHasCartAlready){
             // if product id exist in that logged in user id
             $cartItem = $this->cartItem->where('product_id', $request->product_id)->where('cart_id', $checkIfUserHasCartAlready->id)->select('id')->first();
             if($cartItem){
                $this->cartItem->where('id', $cartItem->id)->delete();
                //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $checkIfUserHasCartAlready->id)->select('id')->first();
                if(!$checkCartItemsExitsOnCartHashId)
                {
                    $this->model->where('id', $checkIfUserHasCartAlready->id)->delete();
                }
                $this->responseMsg = "product deleted from cart";
                return true;
                }
                else
                {
                    $this->responseMsg = 'product not found';
                    throw new ProductNotFoundIndividuallyException;
                }
        } 
        else
        {
            $this->responseMsg = 'cart not found';
            throw new CartHashIdNotFoundException('cart not found');
        }
    } else{
        $this->responseMsg = 'cart not found';
        throw new CartHashIdNotFoundException('cart not found');
    }
}
}





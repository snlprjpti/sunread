<?php

namespace Modules\Cart\Repositories;

use DB;
use Exception;
use Modules\Cart\Entities\Cart;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Cart\Entities\CartItem;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;
use Modules\Cart\Services\UserAuthCheckService;
use Modules\Cart\Exceptions\OutOfStockException;
use Modules\Cart\Exceptions\CartHashIdNotFoundException;
use Modules\Cart\Exceptions\ChannelDoesNotExistException;
use Elasticsearch\Common\Exceptions\Forbidden403Exception;
use Modules\Product\Exceptions\ProductNotFoundIndividuallyException;

class CartRepository extends BaseRepository
{
    protected $cart, $userAuthCheckService, $cartItem, $product, $store, $responseMsg = '', $website, $channel;
    protected $responseData = [];

    public function __construct(Cart $cart, UserAuthCheckService $userAuthCheckService, CartItem $cartItem, Product $product, Store $store, Website $website, Channel $channel)
    {
        $this->model = $cart;
        $this->userAuthCheckService = $userAuthCheckService;
        $this->cartItem = $cartItem;
        $this->product = $product;
        $this->store = $store;
        $this->website = $website;
        $this->channel = $channel;

        $this->rules = [
            'product_id' => 'required|integer|min:1|exists:products,product_id'
        ];
    }

    public function addOrUpdateCart(object $request)
    {

        DB::beginTransaction();
        try {

            $this->validateData( $request, $this->rules);

            /**
             * check if add/update on cart is by guest or logged in user
             **/
            $customer = $this->userAuthCheckService->validateUser($request);
            $request->merge(["customer_id" => $customer->id ?? null]);
            $productId = $request->product_id;

            if ($request->type == 'update') {
                if (!array_key_exists('hc-cart', $request->header())) throw ValidationException::withMessages(["cart_hash_id" => "cart hash id is required"]);
                $cartHashId = $request->header()['hc-cart'][0];
                // if cart hash id exist in carts table 
                $cart = $this->model::where('id', $cartHashId)->select('id')->first();
                if ($cart) {

                    $this->updateHeaderOnCart($cart, $request);

                    $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
                    // if product id already exist in the cart hash id
                    $checkProductId = $this->cartItem->where($cartAndProductCheck)->select('qty')->first();
                    if (!$checkProductId) throw new ProductNotFoundIndividuallyException();

                    //  check if passed qty in request 0 or not, if 0 then delete that product from cart_items table.
                    // if not 0 then update product quantity.

                    if ($request->qty == 0) {
                        $this->cartItem->where($cartAndProductCheck)->delete();
                        //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                        $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                        if (!$checkCartItemsExitsOnCartHashId) {
                            $this->model->where('id', $cartHashId)->delete();
                        }

                        $this->responseMsg = "product removed from cart";
                        DB::commit();
                        return $this->responseData["message"] = $this->responseMsg;
                    } else {
                        // check product exist on product table, product status =1, has visibility and in stock
                        $this->checkProductConditions($productId, $request);

                        $qty =  ($checkProductId->qty) + $request->qty ?? 1;
                        $this->cartItem->where($cartAndProductCheck)
                            ->update([
                                "qty" => $request->qty ?? 1
                            ]);
                        $this->responseMsg = "product quantity updated on cart";
                        DB::commit();
                        return $this->responseData["message"] = $this->responseMsg;
                    }
                } else {

                    $this->checkProductConditions($productId, $request);
                    $this->addProductOnCart($request);
                    $this->responseData['message'] = $this->responseMsg;
                    DB::commit();
                    return $this->responseData;
                }
            } else {

                if (isset($request->header()['hc-cart'])) {
                    $cartHashId = $request->header()['hc-cart'][0];

                    // if cart hash id exist in carts table 
                    $cart = $this->model::where('id', $cartHashId)->select('id')->first();
                    if ($cart) {

                        $this->updateHeaderOnCart($cart, $request);
                        $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
                        // if product id already exist in the cart hash id
                        $checkProductId = $this->cartItem->where($cartAndProductCheck)->select('qty')->first();
                        if ($checkProductId) {

                            //  check if passed qty in request 0 or not, if 0 then delete that product from cart_items table.
                            // if not 0 then update product quantity.

                            if ($request->qty == 0) {
                                $this->cartItem->where($cartAndProductCheck)->delete();
                                //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                                $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                                if (!$checkCartItemsExitsOnCartHashId) {
                                    $this->model->where('id', $cartHashId)->delete();
                                }

                                $this->responseMsg = "product removed from cart";
                                DB::commit();
                                return $this->responseData['message'] = $this->responseMsg;
                            } else {

                                // check product exist on product table, product status =1, has visibility and in stock
                                $this->checkProductConditions($productId, $request);

                                $qty =  ($checkProductId->qty) + $request->qty ?? 1;
                                $this->cartItem->where($cartAndProductCheck)
                                    ->update([
                                        "qty" => $qty
                                    ]);
                                $this->responseMsg = "product quantity updated on cart";
                                DB::commit();
                                return $this->responseData['message'] = $this->responseMsg;
                            }
                        }

                        if (isset($request->qty) && $request->qty == 0) throw ValidationException::withMessages(["quantity" => "product quantity must be greater than 0"]);

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
                        return $this->responseData['message'] = $this->responseMsg;
                    }
                    // if cart hash id sent but not found on cart table
                    else {
                        // check product exist on product table, product status =1, in stock, same channel
                        $this->checkProductConditions($productId, $request);

                        $this->addProductOnCart($request);
                        $this->responseData['message'] = $this->responseMsg;
                        DB::commit();
                        return $this->responseData;
                    }
                }

                // if cart hash id is not sent, then create new cart 
                else {

                    // check product exist on product table, product status =1, has visibility and in stock
                    $this->checkProductConditions($productId, $request);
                    $this->addProductOnCart($request);
                    $this->responseData['message'] = $this->responseMsg;
                    DB::commit();
                    return $this->responseData;
                }
            }
        } catch (Exception $exception) {
            DB::rollback();
            $this->responseMsg = 'exception on add/update cart';
            $this->responseData['message'] = $this->responseMsg;
            throw $exception;
        }

        DB::commit();
        return $this->responseData;
    }

    public function deleteProductFromCart(object $request)
    {
        try {

            $this->validateData( $request, $this->rules);

            /**
             * check if user is on guest or logged mode
             **/
            $customer = $this->userAuthCheckService->validateUser($request);
            $request->merge(["customer_id" => $customer->id ?? null]);
            $productId = $request->product_id;

            if (isset($request->header()['hc-cart'])) {
                $cartHashId = $request->header()['hc-cart'][0];

                // if cart hash id exist in carts table
                $cart = $this->model::where('id', $cartHashId)->select('id')->first();
                if ($cart) {
                    $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
                    // if product id exist in the cart hash id, delete that product
                    $checkProductId = $this->cartItem->where($cartAndProductCheck)->first();
                    if ($checkProductId) {
                        $this->cartItem->where($cartAndProductCheck)->delete();
                        //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                        $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                        if (!$checkCartItemsExitsOnCartHashId) {
                            $this->model->where('id', $cartHashId)->delete();
                        }
                        $this->responseMsg = 'product deleted';
                        DB::commit();
                        return $this->responseMsg;
                    } else {
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
        } catch (Exception $exception) {
            DB::rollback();
            $this->responseMsg = 'exception on product delete';
            throw $exception;
        }

        DB::commit();
        return $this->responseMsg;
    }

    public function getAllProductFromCart(object $request)
    {
        DB::beginTransaction();
        try {
            $products = [];
            if (!array_key_exists('hc-channel', $request->header())) throw ValidationException::withMessages(["channel_code" => "channel code is required"]);
            $checkChannel = $this->channel::where('code', $request->header()['hc-channel'][0])->select('id')->firstOrFail();


            // if cart hash id is sent
            if (isset($request->header()['hc-cart'])) {
                $cartHashId = $request->header()['hc-cart'][0];

                // if cart hash id exist in carts table
                $cart = $this->model::where('id', $cartHashId)->select('id')->first();

                if ($cart) {

                    $this->updateHeaderOnCart($cart, $request);

                    $coreCache = $this->getCoreCache($request);
                    $relations = [
                        "catalog_inventories",
                        "images",
                        "images.types",
                        "product_attributes",
                        "product_attributes.attribute",
                    ];
                    foreach ($cart->cartItems as $item) {

                        $getProductWebsiteId = $this->product::where('id', $item->product_id)->whereStatus(1)->with($relations)->firstOrFail();
                        $websiteChannels = $this->channel::where('website_id', $getProductWebsiteId->website_id)->pluck('id')->toArray();
                        if (!in_array($checkChannel->id, $websiteChannels)) {
                            $item->delete();
                            //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                            $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                            if (!$checkCartItemsExitsOnCartHashId) {
                                $this->model->where('id', $cartHashId)->delete();
                            }
                            $message = 'product(s) removed due to change in channel';
                        };

                        $products[] = $this->getProductDetail($getProductWebsiteId, $item, $coreCache);
                    }
                }
            }

            $items = [
                "items" => $products,
                "count" => count($products),
                "message" => $message ?? ''
            ];
        } catch (Exception $exception) {
            $this->responseMsg = 'exception on product delete';
            DB::rollback();
            throw $exception;
        }

        DB::commit();
        return $items;
    }

    public function mergeCart(object $request)
    {
        DB::beginTransaction();
        try {
            $customerId = auth('customer')->user()->id;
            // check if there is cart hash id
            if (isset($request->header()['hc-cart'])) {
                $cartHashId = $request->header()['hc-cart'][0];

                // if cart hash id exist in carts table 
                $cart = $this->model::where('id', $cartHashId)->select('id', 'customer_id')->first();
                if ($cart) {
                    if ($cart->customer_id != null) {
                        throw new Forbidden403Exception('cart must be of type guest');
                    } else {
                        // get customer ID
                        $checkCartOfUser = $this->model::where('customer_id', $customerId)->first();
                        if ($checkCartOfUser) {
                            $checkCartOfUser->delete();  
                        }

                        $this->updateHeaderOnCart($cart, $request);
                        $cart->update(["customer_id" => $customerId]);
                        DB::commit();
                        return true;
                    }
                } else {
                    throw new CartHashIdNotFoundException('cart not found');
                }
            } else {
                return true;
            }
        } catch (Exception $exception) {
            DB::rollback();
            $this->responseMsg = 'exception on merge cart';
            throw $exception;
        }

        DB::commit();
        return true;
    }

    private function getProductDetail($product, $cartItem, $coreCache)
    {
        $data = [];
        $data["id"] = $product->id;
        $data["sku"] = $product->sku;
        $store = $coreCache->store;

        $product_details = $product->product_attributes->mapWithKeys(function ($product_attribute) use ($store, $product) {
            $match = [
                "scope" => "store",
                "scope_id" => $store->id,
                "attribute_id" => $product_attribute->attribute->id
            ];
            if ($product_attribute->attribute->slug == "visibility") {
                if ($product->value($match)?->name == "Not Visible Individually") throw new ProductNotFoundIndividuallyException();
            }
            return (!$product_attribute->attribute->is_user_defined) ? [$product_attribute->attribute->slug => ($product_attribute->attribute->type == "select") ? $product->value($match)?->name : $product->value($match)] : [];
        })->toArray();

        $data['name'] = $product_details['name'];

        /*---------- PRODUCT PRICE------------- */
        $special_from_date = $product_details['special_from_date'];
        $special_to_date = $product_details['special_to_date'];
        $special_price = $product_details['special_price'];
        $price = $product_details['price'];
        if (!$special_from_date || !$special_to_date) {
            $data['price'] = $special_price ?? $price;
        } else {
            $currentDate = now()->toDateTimeString();
            if ($special_from_date <= $currentDate && $currentDate <= $special_to_date) {
                $data['price'] = $special_price;
            } else {
                $data['price'] = $price;
            }
        }

        $data['price_formatted'] = '';
        $data['tax_amout'] = '';
        $data['tax_amout_formatted'] = '';
        $data['total_amount'] = '';
        $data['total_amount_formatted'] = '';
        $data['total_tax_amount'] = '';
        $data['total_tax_amount_formatted'] = '';
        /*---------- /.PRODUCT PRICE------------- */

        if ($product->type == "simple" && $product->parent_id) {
            $configurable_attributes = [];
            $product->product_attributes->filter(function ($product_attribute) {
                return ($product_attribute->attribute->slug == "color") || ($product_attribute->attribute->slug == "size");
            })->map(function ($product_attribute) use ($store, $product, &$configurable_attributes) {
                $match = [
                    "scope" => "store",
                    "scope_id" => $store->id,
                    "attribute_id" => $product_attribute->attribute_id
                ];
                $configurable_attributes[] = [
                    "label" => $product_attribute->attribute->name,
                    "value" => $product->value($match)?->name,
                ];
            })->toArray();

            $data['configurable_attributes'] = $configurable_attributes;
        }

        $data['product_type'] = $product->parent_id ? 'configurable' : 'simple';


        // Product Stock
        $productStock = $product->catalog_inventories->first();
        $stock_status = true;
        if ($productStock && $productStock->manage_stock && $productStock->is_in_stock && $cartItem->qty > $productStock->quantity) {
            $stock_status = false;
        }
        $data["stock_status"] = $stock_status;

        $data['qty'] = $cartItem->qty;

        // Product Single Image
        $data['image'] = '';
        foreach($product->images as $image){
            $imageTypes = $image->types->pluck('slug')->toArray();
            if(in_array('small_image', $imageTypes) && $image->path){
                $data['image'] = Storage::url($image->path);
                break;
            } else{
                $data['image'] = in_array('base_image', $imageTypes) ? Storage::url($image->path): '';
            }
        }
        
        return $data;
    }

    private function addProductOnCart(object $request)
    {
        if (isset($request->qty) && $request->qty == 0) throw ValidationException::withMessages(["quantity" => "product quantity must be greater than 0"]);

        $checkIfUserHasCartAlready = $this->model::where('customer_id', $request->customer_id)->select('id')->first();
        if (isset($request->customer_id) && $checkIfUserHasCartAlready) {

            $this->updateHeaderOnCart($checkIfUserHasCartAlready, $request);

            // if product id already exist in that logged in user id
            $checkProductId = $this->cartItem->where('product_id', $request->product_id)->where('cart_id', $checkIfUserHasCartAlready->id)->select('qty')->first();
            if ($checkProductId) {
                $qty =  ($checkProductId->qty) + $request->qty ?? 1;
                $this->cartItem->where('product_id', $request->product_id)->where('cart_id', $checkIfUserHasCartAlready->id)
                    ->update([
                        "qty" => $qty
                    ]);
                $this->responseMsg = "product quantity updated on cart";
            } else {
                $this->cartItem::create([
                    "cart_id" => $checkIfUserHasCartAlready->id,
                    "product_id" => $request->product_id,
                    "qty" => $request->qty ?? 1
                ]);
                $this->responseMsg = "Product add on cart";
            }
        } else {
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
            $this->responseMsg = "product add on cart";
            $this->responseData['cart_id'] = $cart->id;
        }

        return true;
    }

    private function checkProductConditions(string $productId, object $request): bool
    {
        // check if product exist on product table
        $product = $this->checkProductStatus($productId);

        // check if product exits on channel
        $this->checkIfProductExistOnChannel($product, $request);

        // check stock of product
        $this->checkProductStock($product, $request);

        return true;
    }

    private function checkProductStatus(string $productId): object
    {
        $product = $this->product::where('id', $productId)->where('status', 1)->select('id')->first();
        if (!$product) throw new ProductNotFoundIndividuallyException();
        return $product;
    }

    private function checkIfProductExistOnChannel(object $product, object $request)
    {
        $channel = Channel::where('code', $request->header()['hc-channel'][0])->select('id')->first();
        if (!$channel) throw new ChannelDoesNotExistException;
        $checkProductOnChannel = DB::table('channel_product')->where('channel_id', $channel->id)
            ->where('product_id', $request->product_id)->select('channel_id')
            ->first();
        if ($checkProductOnChannel) throw new ProductNotFoundIndividuallyException();

        return true;
    }

    private function checkProductStock(object $product, object $request): mixed
    {
        $productStock = $product->catalog_inventories()->first();
        $qty = $request->qty ?? 1;
        if ($productStock && $productStock->manage_stock && $productStock->is_in_stock && $qty > $productStock->quantity) {
            throw new OutOfStockException;
        }
        return true;
    }

    private function deleteProductAsPerCustomerMode(object $request)
    {
        $customerId = $request->customer_id;
        if ($customerId) {
            $checkIfUserHasCartAlready = $this->model::where('customer_id', $customerId)->select('id')->first();
            if ($checkIfUserHasCartAlready) {
                // if product id exist in that logged in user id
                $cartItem = $this->cartItem->where('product_id', $request->product_id)->where('cart_id', $checkIfUserHasCartAlready->id)->select('id')->first();
                if ($cartItem) {
                    $this->cartItem->where('id', $cartItem->id)->delete();
                    //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                    $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $checkIfUserHasCartAlready->id)->select('id')->first();
                    if (!$checkCartItemsExitsOnCartHashId) {
                        $this->model->where('id', $checkIfUserHasCartAlready->id)->delete();
                    }
                    $this->responseMsg = "product deleted from cart";
                    return true;
                } else {
                    $this->responseMsg = 'product not found';
                    throw new ProductNotFoundIndividuallyException;
                }
            } else {
                $this->responseMsg = 'cart not found';
                throw new CartHashIdNotFoundException;
            }
        } else {
            $this->responseMsg = 'cart not found';
            throw new CartHashIdNotFoundException;
        }
    }

    private function updateHeaderOnCart(object $cart, object $request): bool
    {
        if ($cart->channel_code != $request->header()['hc-channel'][0] || $cart->store_code != $request->header()['hc-store'][0]) {
            $cart->update([
                "channel_code" => $request->header()['hc-channel'][0],
                "store_code" => $request->header()['hc-store'][0]
            ]);
        }
        return true;
    }
}

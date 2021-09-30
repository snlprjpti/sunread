<?php

namespace Modules\Cart\Repositories;

use DB;
use Exception;
use Modules\Cart\Entities\Cart;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Cart\Entities\CartItem;
use Modules\Core\Facades\PriceFormat;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;
use Modules\Cart\Services\UserAuthCheckService;
use Modules\Cart\Exceptions\OutOfStockException;
use Modules\Cart\Repositories\CartItemRepository;
use Modules\Cart\Exceptions\CartHashIdNotFoundException;
use Modules\Cart\Exceptions\ChannelDoesNotExistException;
use Elasticsearch\Common\Exceptions\Forbidden403Exception;
use Modules\Product\Exceptions\ProductNotFoundIndividuallyException;

class CartRepository extends BaseRepository
{
    protected $cart, $userAuthCheckService, $cartItem, $product, $store, $cartStatus = [], $website, $channel, $cartItemRepo;
    protected $responseData = [];

    public function __construct(Cart $cart, UserAuthCheckService $userAuthCheckService, CartItem $cartItem, Product $product, Store $store, Website $website, Channel $channel, CartItemRepository $cartItemRepo)
    {
        $this->model = $cart;
		$this->model_key = "carts";
        $this->userAuthCheckService = $userAuthCheckService;
        $this->cartItem = $cartItem;
        $this->product = $product;
        $this->store = $store;
        $this->website = $website;
        $this->channel = $channel;
        $this->cartItemRepo = $cartItemRepo;

        $this->rules = [
            'product_id' => 'required|integer|min:1|exists:products,id'
        ];

        $this->cartStatus = [
            "product_removed"                      => __("core::app.cart.product-removed"),
            "product_qty_updated"                  => __("core::app.cart.product-qty-updated"),
            "product_added"                        => __("core::app.cart.product-added"),
            "product_remove_due_to_channel_change" => __("core::app.cart.product-remove-due-to-channel-change"),
            "cart_merged"                          => __("core::app.response.cart-merged"),
        ];
    }

    public function addOrUpdateCart(object $request): mixed
    {
        DB::beginTransaction();
        try
        {

            $this->validateData($request, $this->rules);

            /**
             * check if add/update on cart is by guest or logged in user
             **/
            $customer = $this->userAuthCheckService->validateUser($request);
            $request->merge(["customer_id" => $customer->id ?? null]);
            $productId = $request->product_id;

            if ($request->type == 'update')
            {
                if (!array_key_exists('hc-cart', $request->header())) throw ValidationException::withMessages(["cart_hash_id" => __("core::app.exception_message.cart-id-required")]);

                $cartHashId = getCartHashIdFromHeader($request, 'hc-cart');

                // if cart hash id exist in carts table 
                $cart = $this->model::where('id', $cartHashId)->select('id', 'channel_code', 'store_code')->first();
                if ($cart)
                {

                    $this->updateHeaderOnCart($cart, $request);

                    $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
                    // if product id already exist in the cart hash id
                    $checkProductId = $this->cartItem->where($cartAndProductCheck)->select('qty')->first();
                    if (!$checkProductId) throw new ProductNotFoundIndividuallyException;

                    //  check if passed qty in request 0 or not, if 0 then delete that product from cart_items table.
                    // if not 0 then update product quantity.

                    if ($request->qty == 0)
                    {
                        $this->cartItemRepo->deleteCartItem($cartAndProductCheck);
                        //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                        $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                        if (!$checkCartItemsExitsOnCartHashId)
                        {
                            $this->delete($cartHashId);
                        }

                        $this->responseData['message'] = $this->cartStatus['product_removed'];
                        $this->responseData["cart_id"] = $checkCartItemsExitsOnCartHashId ? $cartHashId : '';
                    } 
                    else
                    {
                        // check product exist on product table, product status =1, has visibility and in stock
                        $this->checkProductConditions($productId, $request);

                        $qty =  ($checkProductId->qty) + $request->qty ?? 1;
                        $this->cartItemRepo->updateItemWithConditions($cartAndProductCheck, ["qty" => $request->qty ?? 1]);
                      
                        $this->responseData['message'] = $this->cartStatus['product_qty_updated'];
                        $this->responseData["cart_id"] = $cartHashId;
                    }
                }
                else
                {

                    $this->checkProductConditions($productId, $request);
                    $this->addProductOnCart($request);   
                }
            }
            else
            {

                if (isset($request->header()['hc-cart']))
                {
                    $cartHashId = getCartHashIdFromHeader($request, 'hc-cart');

                    // if cart hash id exist in carts table 
                    $cart = $this->model::where('id', $cartHashId)->select('id', 'channel_code', 'store_code')->first();
                    if ($cart)
                    {

                        $this->updateHeaderOnCart($cart, $request);
                        $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
                        // if product id already exist in the cart hash id
                        $checkProductId = $this->cartItem->where($cartAndProductCheck)->select('qty')->first();
                        if ($checkProductId)
                        {

                            //  check if passed qty in request 0 or not, if 0 then delete that product from cart_items table.
                            // if not 0 then update product quantity.

                            if ($request->qty == 0)
                            {
                                $this->cartItemRepo->deleteCartItem($cartAndProductCheck);

                                //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                                $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                                if (!$checkCartItemsExitsOnCartHashId)
                                {
                                    $this->delete($cartHashId);
                                }

                                // $this->responseMsg = "product removed from cart";
                                // DB::commit();
                                // return $this->responseData['message'] = $this->responseMsg;
                                $this->responseData['message'] = $this->cartStatus['product_removed'];
                                $this->responseData["cart_id"] = $checkCartItemsExitsOnCartHashId ? $cartHashId : '';
                            } else
                            {

                                // check product exist on product table, product status =1, has visibility and in stock
                                $this->checkProductConditions($productId, $request);

                                $qty =  ($checkProductId->qty) + $request->qty ?? 1;
                                $this->cartItemRepo->updateItemWithConditions($cartAndProductCheck, ["qty" => $qty]);
                                $this->responseData['message'] = $this->cartStatus['product_qty_updated'];
                                $this->responseData["cart_id"] = $cartHashId;
                            }
                        }

                        if (isset($request->qty) && $request->qty == 0) throw ValidationException::withMessages(["quantity" => __("core::app.exception_message.product-qty-must-be-above-0")]);

                        // check product exist on product table, product status =1, in stock, same channel
                        $this->checkProductConditions($productId, $request);


                        // if product does not exist on table of that cart hash id, add product on cart on that cart hash id:
                        $cartItemCreateData = [
                            "cart_id" => $cartHashId,
                            "product_id" => $productId,
                            "qty" => $request->qty ?? 1
                        ];
                        $this->cartItemRepo->createCartItem($cartItemCreateData);

                        $this->responseData['message'] = $this->cartStatus['product_added'];
                        $this->responseData["cart_id"] = $cartHashId;
                    }
                    // if cart hash id sent but not found on cart table
                    else
                    {
                        // check product exist on product table, product status =1, in stock, same channel
                        $this->checkProductConditions($productId, $request);
                        $this->addProductOnCart($request);
                    }
                }

                // if cart hash id is not sent, then create new cart 
                else
                {

                    // check product exist on product table, product status =1, has visibility and in stock
                    $this->checkProductConditions($productId, $request);
                    $this->addProductOnCart($request);
                }
            }
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();
        return $this->responseData;
    }

    public function deleteProductFromCart(object $request): mixed
    {
        DB::beginTransaction();
        try
        {

            $this->validateData($request, $this->rules);

            /**
             * check if user is on guest or logged mode
             **/
            $customer = $this->userAuthCheckService->validateUser($request);
            $request->merge(["customer_id" => $customer->id ?? null]);
            $productId = $request->product_id;

            if (isset($request->header()['hc-cart']))
            {
                $cartHashId = getCartHashIdFromHeader($request, 'hc-cart');

                // if cart hash id exist in carts table
                $cart = $this->model::where('id', $cartHashId)->select('id', 'customer_id', 'channel_code', 'store_code')->first();

                if ($cart)
                {
                    if ($request->customer_id != $cart->customer_id) throw new Forbidden403Exception(__("core::app.exception_message.not-allowed"));

                    $this->updateHeaderOnCart($cart, $request);

                    $cartAndProductCheck = ['cart_id' => $cartHashId, 'product_id' => $productId];
                    // if product id exist in the cart hash id, delete that product
                    $checkProductId = $this->cartItem->where($cartAndProductCheck)->first();
                    if ($checkProductId)
                    {
                        $this->cartItemRepo->deleteCartItem($cartAndProductCheck);

                        //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                        $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                        if (!$checkCartItemsExitsOnCartHashId)
                        {
                            $this->delete($cartHashId);
                        }
                        $this->responseData['message'] = $this->cartStatus['product_removed'];
                        $this->responseData["cart_id"] = $checkCartItemsExitsOnCartHashId ? $cartHashId : '';
                    } 
                    else
                    {
                        throw new ProductNotFoundIndividuallyException;
                    }
                }
                // if cart hash id is sent but not found on cart table case:
                else
                {
                    $this->deleteProductAsPerCustomerMode($request);
                }
            }
            // if cart hash id is not sent case:
            else
            {
                $this->deleteProductAsPerCustomerMode($request);
            }
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();
        return $this->responseData;
    }

    public function getAllProductFromCart(object $request): array
    {
        DB::beginTransaction();
        try
        {
            $products = [];
            if (!array_key_exists('hc-channel', $request->header())) throw ValidationException::withMessages(["channel_code" => __("core::app.response.channel-code-required")]);
            $headerChannel = getCartHashIdFromHeader($request, 'hc-channel');
            $checkChannel = $this->channel::where('code', $headerChannel)->select('id')->firstOrFail();

            // if cart hash id is sent
            if (isset($request->header()['hc-cart']))
            {
                $cartHashId = getCartHashIdFromHeader($request, 'hc-cart');

                // if cart hash id exist in carts table
                $cart = $this->model::where('id', $cartHashId)->select('id', 'channel_code', 'store_code')->first();

                if ($cart)
                {

                    $this->updateHeaderOnCart($cart, $request);

                    $coreCache = $this->getCoreCache($request);
                    $relations = [
                        "catalog_inventories",
                        "images",
                        "images.types",
                        "product_attributes",
                        "product_attributes.attribute",
                    ];
                    foreach ($cart->cartItems as $item)
                    {
                        $getProductWebsiteId = $this->product::where('id', $item->product_id)->whereStatus(1)->with($relations)->firstOrFail();
                        $websiteChannels = $this->channel::where('website_id', $getProductWebsiteId->website_id)->pluck('id')->toArray();
                        if (!in_array($checkChannel->id, $websiteChannels)) {
                            $item->delete();
                            //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                            $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $cartHashId)->select('id')->first();
                            if (!$checkCartItemsExitsOnCartHashId) {
                                $this->delete($cartHashId);
                            }
                            $message = $this->cartStatus['product_remove_due_to_channel_change'];
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
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();
        return $items;
    }

    public function mergeCart(object $request): mixed
    {
        DB::beginTransaction();
        try
        {
            $customerId = auth('customer')->user()->id;
            // check if there is cart hash id
            if (isset($request->header()['hc-cart']))
            {
                $cartHashId = getCartHashIdFromHeader($request, 'hc-cart');

                // if cart hash id exist in carts table 
                $cart = $this->model::where('id', $cartHashId)->select('id', 'customer_id', 'channel_code', 'store_code')->first();
                if ($cart)
                {
                    if ($cart->customer_id != null)
                    {
                        throw new Forbidden403Exception(__("core::app.exception_message.not-allowed"));
                    } 
                    else
                    {
                        // get customer ID
                        $checkCartOfUser = $this->model::where('customer_id', $customerId)->first();
                        if ($checkCartOfUser)
                        {
                            $checkCartOfUser->delete();
                        }

                        $this->updateHeaderOnCart($cart, $request);

                        $this->update(["customer_id" => $customerId], $cart->id);

                        $this->responseData['message'] = $this->cartStatus['cart_merged'];
                        $this->responseData["cart_id"] = $cartHashId;
                    }
                } 
                else
                {
                    throw new CartHashIdNotFoundException;
                }
            }
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();
        return $this->responseData;
    }

    private function getProductDetail($product, $cartItem, $coreCache): mixed
    {
        try
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
                    if ($product->value($match)?->name == "Not Visible Individually") throw new ProductNotFoundIndividuallyException;
                }
                return (!$product_attribute->attribute->is_user_defined) ? [$product_attribute->attribute->slug => ($product_attribute->attribute->type == "select") ? $product->value($match)?->name : $product->value($match)] : [];
            })->toArray();

            $data['name'] = $product_details['name'];

            /*---------- PRODUCT PRICE------------- */
            $special_from_date = $product_details['special_from_date'] ?? '';
            $special_to_date = $product_details['special_to_date'] ?? '';
            $price = $product_details['price'] ?? 0;
            if (!$special_from_date || !$special_to_date)
            {
                $data['price'] = $product_details['special_price'] ?? $price;
            } 
            else 
            {
                $currentDate = now()->toDateTimeString();
                ($special_from_date <= $currentDate && $currentDate <= $special_to_date) ? ($data['price'] = $product_details['special_price'] ?? $price) : $data['price'] = $price;
            }
            $data['price_formatted'] = PriceFormat::get($data['price'], $store->id, "store");
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
            if ($productStock && $productStock->manage_stock && $productStock->is_in_stock && $cartItem->qty > $productStock->quantity)
            {
                $stock_status = false;
            }
            $data["stock_status"] = $stock_status;

            $data['qty'] = $cartItem->qty;

            // Product Single Image
            $data['image'] = '';
            foreach ($product->images as $image)
            {
                $imageTypes = $image->types->pluck('slug')->toArray();
                if (in_array('small_image', $imageTypes) && $image->path)
                {
                    $data['image'] = Storage::url($image->path);
                    break;
                } else
                {
                    $data['image'] = in_array('base_image', $imageTypes) ? Storage::url($image->path) : '';
                }
            }
        } 
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    private function addProductOnCart(object $request): mixed
    {
        try
        {
            if (isset($request->qty) && $request->qty == 0) throw ValidationException::withMessages(["quantity" => __("core::app.exception_message.product-qty-must-be-above-0")]);

            $checkIfUserHasCartAlready = $this->model::where('customer_id', $request->customer_id)->select('id', 'channel_code', 'store_code')->first();
            if (isset($request->customer_id) && $checkIfUserHasCartAlready)
            {

                $this->updateHeaderOnCart($checkIfUserHasCartAlready, $request);

                // if product id already exist in that logged in user id
                $checkProductId = $this->cartItem->where('product_id', $request->product_id)->where('cart_id', $checkIfUserHasCartAlready->id)->select('qty')->first();
                if ($checkProductId)
                {
                    $qty =  ($checkProductId->qty) + $request->qty ?? 1;

                    $whereConditionOfCartAndProduct = [
                        "product_id" => $request->product_id,
                        "cart_id" => $checkIfUserHasCartAlready->id,
                    ];

                    $this->cartItemRepo->updateItemWithConditions($whereConditionOfCartAndProduct, ["qty" => $qty]);

                    $this->responseData['message'] = $this->cartStatus['product_qty_updated'];
                    $this->responseData["cart_id"] = $$checkIfUserHasCartAlready->id;
                } 
                else
                {
                    $cartItemCreateData = [
                        "cart_id" => $checkIfUserHasCartAlready->id,
                        "product_id" => $request->product_id,
                        "qty" => $request->qty ?? 1
                    ];

                    $this->cartItemRepo->createCartItem($cartItemCreateData);
                    $this->responseData['message'] = $this->cartStatus['product_added'];
                    $this->responseData["cart_id"] = $$checkIfUserHasCartAlready->id;
                }
            } 
            else
            {
                $cart = $this->create([
                    "customer_id" => $request->customer_id,
                    "channel_code" => getCartHashIdFromHeader($request, 'hc-channel'),
                    "store_code" => getCartHashIdFromHeader($request, 'hc-store')
                ]);
                $cartItemCreateData = [
                    "cart_id" => $cart->id,
                    "product_id" => $request->product_id,
                    "qty" => $request->qty ?? 1
                ];

                $this->cartItemRepo->createCartItem($cartItemCreateData);
                $this->responseData['message'] = $this->cartStatus['product_added'];
                $this->responseData['cart_id'] = $cart->id;
            }
        } 
        catch (Exception $exception)
        {
            throw $exception;
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
        try
        {
            $product = $this->product::where('id', $productId)->where('status', 1)->select('id')->first();
            if (!$product) throw new ProductNotFoundIndividuallyException();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $product;
    }

    private function checkIfProductExistOnChannel(object $product, object $request): bool
    {
        try
        {
            $headerChannel = getCartHashIdFromHeader($request, 'hc-channel');
            $channel = Channel::where('code', $headerChannel)->select('id')->first();
            if (!$channel) throw new ChannelDoesNotExistException;
            $checkProductOnChannel = DB::table('channel_product')->where('channel_id', $channel->id)
                ->where('product_id', $request->product_id)->select('channel_id')
                ->first();
            if ($checkProductOnChannel) throw new ProductNotFoundIndividuallyException;
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }

    private function checkProductStock(object $product, object $request): bool
    {
        try
        {
            $productStock = $product->catalog_inventories()->first();
            $qty = $request->qty ?? 1;
            if ($productStock && $productStock->manage_stock && $productStock->is_in_stock && $qty > $productStock->quantity)
            {
                throw new OutOfStockException;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }

    private function deleteProductAsPerCustomerMode(object $request): bool
    {
        try
        {
            $customerId = $request->customer_id;
            if ($customerId)
            {
                $checkIfUserHasCartAlready = $this->model::where('customer_id', $customerId)->select('id', 'channel_code', 'store_code')->first();
                if ($checkIfUserHasCartAlready)
                {
                    // if product id exist in that logged in user id
                    $cartItem = $this->cartItem->where('product_id', $request->product_id)->where('cart_id', $checkIfUserHasCartAlready->id)->select('id')->first();
                    if ($cartItem)
                    {

                        $this->updateHeaderOnCart($checkIfUserHasCartAlready, $request);

                        $this->cartItemRepo->deleteCartItem(["id" => $cartItem->id]);

                        //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                        $checkCartItemsExitsOnCartHashId = $this->cartItem->where('cart_id', $checkIfUserHasCartAlready->id)->select('id')->first();
                        if (!$checkCartItemsExitsOnCartHashId)
                        {
                            $this->delete($$checkIfUserHasCartAlready->id);
                        }

                        $this->responseData['message'] = $this->cartStatus['product_removed'];
                        $this->responseData["cart_id"] = $checkCartItemsExitsOnCartHashId ? $checkIfUserHasCartAlready->id : '';
                    } 
                    else
                    {
                        throw new ProductNotFoundIndividuallyException;
                    }
                }
                else 
                {
                    throw new CartHashIdNotFoundException;
                }
            } 
            else
            {
                throw new CartHashIdNotFoundException;
            }
        } 
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }

    private function updateHeaderOnCart(object $cart, object $request): bool
    {
        try
        {
            $headerChannel = getCartHashIdFromHeader($request, 'hc-channel');
            $headerStore = getCartHashIdFromHeader($request, 'hc-store');
            if ($cart->channel_code != $headerChannel || $cart->store_code != $headerStore)
            {
                $data = [
                        "channel_code" => $headerChannel,
                        "store_code" => $headerStore
                    ];
                $this->update($data, $cart->id);
            }
        } 
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }
}

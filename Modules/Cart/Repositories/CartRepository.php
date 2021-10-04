<?php

namespace Modules\Cart\Repositories;

use Exception;
use Modules\Cart\Entities\Cart;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Cart\Entities\CartItem;
use Illuminate\Support\Facades\Event;
use Modules\Core\Facades\PriceFormat;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;
use Modules\Cart\Services\UserAuthCheckService;
use Modules\Cart\Exceptions\OutOfStockException;
use Modules\Cart\Repositories\CartItemRepository;
use Modules\Cart\Exceptions\CartHashIdNotFoundException;
use Elasticsearch\Common\Exceptions\Forbidden403Exception;
use Illuminate\Support\Facades\DB;
use Modules\Core\Facades\CoreCache;
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
            "product_id" => "required|exists:products,id",
            "qty" => "sometimes|numeric|min:0",
            "type" => "sometimes|in:create,update"
        ];

        $this->cartStatus = [
            "product_removed" => __("core::app.cart.product-removed"),
            "product_qty_updated" => __("core::app.cart.product-qty-updated"),
            "product_added" => __("core::app.cart.product-added"),
            "product_remove_due_to_channel_change" => __("core::app.cart.product-remove-due-to-channel-change"),
            "cart_merged" => __("core::app.response.cart-merged"),
        ];
    }

    public function addOrUpdateCart(object $request): mixed
    {

        Event::dispatch("{$this->model_key}.add.update.before");

        DB::beginTransaction();
        try
        {
            $this->validateData($request);
            
            //check if add/update on cart is by guest or logged in user
            $customer = $this->userAuthCheckService->validateUser($request);
            
            $request->merge(["customer_id" => $customer->id ?? null, "type" => $request->type ?? "create"]);

            if (!array_key_exists("hc-cart", $request->header()) && $request->type == "update") throw ValidationException::withMessages(["cart_hash_id" => __("core::app.exception_message.cart-id-required")]);
             
            elseif (empty($request->header()["hc-cart"]) && $request->type == "create") {
                $this->checkProductConditions($request->product_id, $request);
                $this->addProductOnCart($request);
            }
            else {
                $cartHashId = $this->getCartHashIdFromHeader($request, "hc-cart");
                // if cart hash id exist in carts table 
                $cart = $this->model::whereId($cartHashId)->first();
                if ($cart) $this->whenCartFoundInTable($cart, $request, $request->product_id);
                // if cart hash id sent but not found on cart table
                else {
                    // check product exist on product table, product status =1, in stock, same channel
                    $this->checkProductConditions($request->product_id, $request);
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
        Event::dispatch("{$this->model_key}.add.update.after", $this->responseData);
        
        return $this->responseData;
    }

    public function deleteProductFromCart(object $request): mixed
    {

        DB::beginTransaction();
        try
        {
            $this->validateData($request);

            //check if user is on guest or logged mode
            $customer = $this->userAuthCheckService->validateUser($request);
            $request->merge(["customer_id" => $customer->id ?? null]);

            if (isset($request->header()["hc-cart"])) {
                $cartId = $this->getCartHashIdFromHeader($request, "hc-cart");

                // if cart hash id exist in carts table
                $cart = $this->model::whereId($cartId)->firstOrFail();

                if ($cart) {
                    if ($request->customer_id != $cart->customer_id) throw new Forbidden403Exception(__("core::app.exception_message.not-allowed"));

                    $this->updateHeaderOnCart($cart, $request);

                    $conditions = ["cart_id" => $cartId, "product_id" => $request->product_id];
                    // if product id exist in the cart hash id, delete that product
                    $this->cartItem->where($conditions)->firstOrFail();
                    $this->itemClearFromCart($conditions, $cartId);
                }
                // if cart hash id is sent but not found on cart table case:
                else {
                    $this->deleteProductAsPerCustomerMode($request);
                }
            }
            // if cart hash id is not sent case:
            else {
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
            $coreCache = $this->getCoreCache($request);
            $checkChannel = $coreCache?->channel;

            // if cart hash id is sent
            if (isset($request->header()["hc-cart"]))
            {
                $cartHashId = $this->getCartHashIdFromHeader($request, "hc-cart");

                // if cart hash id exist in carts table
                $cart = $this->model::whereId($cartHashId)->firstOrFail();

                $this->updateHeaderOnCart($cart, $request);

                $this->mergeGuestCart($request, $cart);


                $coreCache = $this->getCoreCache($request);
                $relations = [
                    "catalog_inventories",
                    "images",
                    "images.types",
                    "product_attributes",
                    "product_attributes.attribute",
                    "website.channels"
                ];
                $products = [];

                foreach ($cart->cartItems as $item)
                {
                    $product = $this->product::whereId($item->product_id)->whereStatus(1)->with($relations)->firstOrFail();
                    $channel_ids = $product->website->channels->pluck("id")->toArray();
                    if (!in_array($checkChannel->id, $channel_ids)) {
                        $item->delete();
                        //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
                        $checkCartItemsExitsOnCartHashId = $this->cartItem->where("cart_id", $cartHashId)->first();
                        if (!$checkCartItemsExitsOnCartHashId) {
                            $this->delete($cartHashId);
                        }
                        $message = $this->cartStatus["product_remove_due_to_channel_change"];
                    };

                    $products[] = $this->getProductDetail($product, $item, $coreCache);
                }
            }

            $items = [
                "items" => $products,
                "count" => count($products),
                "message" => $message ?? ""
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

    private function mergeGuestCart(object $request, object $cart): bool
    {
        try
        {
            $customerId = auth("customer")->id();
            if ($customerId) {

                if ($cart->customer_id != null) throw new Forbidden403Exception(__("core::app.exception_message.not-allowed")); 
                $checkCartOfUser = $this->model::whereCustomerId($customerId)->first();
                if ($checkCartOfUser) $checkCartOfUser->delete();

                $this->updateHeaderOnCart($cart, $request);
                $this->update(["customer_id" => $customerId], $cart->id);

                $this->responseData["message"] = $this->cartStatus["cart_merged"];
                $this->responseData["cart_id"] = $cart->id;
            }
        }

        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;

    }

    public function mergeCart(object $request): mixed
    {
        DB::beginTransaction();
        try
        {
            // check if there is cart hash id
            if (isset($request->header()["hc-cart"])) {
                $cartHashId = $this->getCartHashIdFromHeader($request, "hc-cart");
                // if cart hash id exist in carts table 
                $cart = $this->model::where("id", $cartHashId)->firstOrFail();
                $this->mergeGuestCart($request, $cart);
            }
               
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();
        return count($this->responseData) > 0 ? $this->responseData : null; 
    }

    private function getProductDetail($product, $cartItem, $coreCache): mixed
    {
        try
        {
            $data = [];
            $data["id"] = $product->id;
            $data["sku"] = $product->sku;
            $data["product_type"] = $product->parent_id ? "configurable" : "simple";

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

            $data["name"] = $product_details["name"];

            $special_from_date = $product_details["special_from_date"] ?? "";
            $special_to_date = $product_details["special_to_date"] ?? "";
            $price = $product_details["price"] ?? 0;
            
            $currentDate = now()->toDateTimeString();
            if (!$special_from_date || !$special_to_date) $data["price"] = $product_details["special_price"] ?? $price;
            else ($special_from_date <= $currentDate && $currentDate <= $special_to_date) ? ($data["price"] = $product_details["special_price"] ?? $price) : $data["price"] = $price;

            $data["price_formatted"] = PriceFormat::get($data["price"], $store->id, "store");
            
            $data["tax_amout"] = "";
            $data["tax_amout_formatted"] = "";
            $data["total_amount"] = "";
            $data["total_amount_formatted"] = "";
            $data["total_tax_amount"] = "";
            $data["total_tax_amount_formatted"] = "";

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

                $data["configurable_attributes"] = $configurable_attributes;
            }

            // Product Stock
            $productStock = $product->catalog_inventories->first();
            $data["stock_status"] = ($productStock?->manage_stock && $productStock?->is_in_stock && $cartItem?->qty > $productStock?->quantity) ? false : true;
            $data["qty"] = $cartItem->qty;

            // Product Single Image
            $data["image"] = "";
            foreach ($product->images as $image)
            {
                $imageTypes = $image->types->pluck("slug")->toArray();
                if (in_array("small_image", $imageTypes) && $image->path) {
                    $data["image"] = Storage::url($image->path);
                    break;
                }
                else {
                    $data["image"] = in_array("base_image", $imageTypes) ? Storage::url($image->path) : "";
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
            $this->quantityValidation($request->qty);
            $checkIfUserHasCartAlready = $this->model::whereCustomerId($request->customer_id)->first();
            
            if (isset($request->customer_id) && $checkIfUserHasCartAlready) {

                $this->updateHeaderOnCart($checkIfUserHasCartAlready, $request);

                // if product id already exist in that logged in user id
                $cartItem = $this->cartItem->whereProductId($request->product_id)->whereCartId($checkIfUserHasCartAlready->id)->first();
                if ($cartItem) {
                    $qty =  ($cartItem->qty) + ($request->qty ?? 1);
                    
                    $whereConditions = [
                        "product_id" => $request->product_id,
                        "cart_id" => $checkIfUserHasCartAlready->id,
                    ];

                    $this->cartItemRepo->updateItemWithConditions($whereConditions, ["qty" => $qty]);

                    $this->responseData["message"] = $this->cartStatus["product_qty_updated"];
                    $this->responseData["cart_id"] = $checkIfUserHasCartAlready->id;
                } 
                else {
                    $cartItemCreateData = [
                        "cart_id" => $checkIfUserHasCartAlready->id,
                        "product_id" => $request->product_id,
                        "qty" => $request->qty ?? 1
                    ];

                    $this->cartItemRepo->create($cartItemCreateData);

                    $this->responseData['message'] = $this->cartStatus["product_added"];
                    $this->responseData["cart_id"] = $checkIfUserHasCartAlready->id;
                }
            } 
            else {
                $cartData = [
                    "customer_id" => $request->customer_id,
                    "channel_code" => $this->getCartHashIdFromHeader($request, "hc-channel"),
                    "store_code" => $this->getCartHashIdFromHeader($request, "hc-store")
                ];
                $cart = $this->create($cartData);

                $cartItemCreateData = [
                    "cart_id" => $cart->id,
                    "product_id" => $request->product_id,
                    "qty" => $request->qty ?? 1
                ];
                $this->cartItemRepo->create($cartItemCreateData);


                $this->responseData["message"] = $this->cartStatus["product_added"];
                $this->responseData["cart_id"] = $cart->id;
            }
        } 
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }

    private function checkProductConditions(string $productId, object $request): void
    {
        // check if product exist on product table
        $product = $this->getProduct($productId);
        // check if product exits on channel
        $this->checkIfProductExistOnChannel($product, $request);
        // check stock of product
        $this->checkProductStock($product, $request);
    }

    private function getProduct(?int $id): object
    {
        try
        {
            $product = $this->product::whereId($id)->whereStatus(1)->firstOrFail();
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
            $cacheData = $this->getCoreCache($request);
            $website_ids = $product->website->channels->pluck("id")->toArray();            
            if (!in_array($cacheData?->channel?->id, $website_ids)) throw new ProductNotFoundIndividuallyException();
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
            if ($productStock?->manage_stock && $productStock?->is_in_stock && $qty > $productStock?->quantity) {
                throw new OutOfStockException();
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
            if(!$request->customer_id) throw new CartHashIdNotFoundException();
            $checkIfUserHasCartAlready = $this->model::whereCustomerId($request->customer_id)->firstOrFail();

            // if product id exist in that logged in user id
            $cartItem = $this->cartItem->whereProductId($request->product_id)->whereCartId($checkIfUserHasCartAlready->id)->firstOrFail();

            $this->updateHeaderOnCart($checkIfUserHasCartAlready, $request);
            $this->itemClearFromCart(["id" => $cartItem->id], $checkIfUserHasCartAlready->id);
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
            $headerChannel = $this->getCartHashIdFromHeader($request, "hc-channel");
            $headerStore = $this->getCartHashIdFromHeader($request, "hc-store");
            $data = [ "channel_code" => $headerChannel, "store_code" => $headerStore ];
            if ($cart->channel_code != $headerChannel || $cart->store_code != $headerStore) $this->update($data, $cart->id);
        } 
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }

    private function getCartHashIdFromHeader($request, $headerName): mixed
    {
        return is_array($request->header()["{$headerName}"]) ? $request->header()["{$headerName}"][array_key_first($request->header()["{$headerName}"])] : '';
    }


    private function whenCartFoundInTable(object $cart, object $request, string $productId): bool
    {
        $this->updateHeaderOnCart($cart, $request);

        $cartHashId = $cart->id;

        $cartAndProductCheck = ["cart_id" => $cartHashId, "product_id" => $productId];

        // if product id already exist in the cart hash id
        $checkProductId = $this->cartItem->where($cartAndProductCheck)->select("qty")->first();
        if ($request->type == "update" && !$checkProductId) throw new ProductNotFoundIndividuallyException();

         if ($checkProductId)
         {
        //  check if passed qty in request 0 or not, if 0 then delete that product from cart_items table.
        // if not 0 then update product quantity.

         if ($request->qty == 0)
         {
            $this->cartItemRepo->deleteCartItem($cartAndProductCheck);
            //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
            $checkCartItemsExitsOnCartHashId = $this->cartItem->where("cart_id", $cartHashId)->select("id")->first();
            if (!$checkCartItemsExitsOnCartHashId)
            {
                $this->delete($cartHashId);
            }

            $this->responseData["message"] = $this->cartStatus["product_removed"];
            $this->responseData["cart_id"] = $checkCartItemsExitsOnCartHashId ? $cartHashId : '';
         }
         else
         {
            // check product exist on product table, product status =1, has visibility and in stock
            $this->checkProductConditions($productId, $request);

            $qty =  ($checkProductId->qty) + $request->qty ?? 1;
            if($request->type == "update")
            {
                $qty = $request->qty ?? 1;
            }
            $this->cartItemRepo->updateItemWithConditions($cartAndProductCheck, ["qty" => $qty]);
          
            $this->responseData["message"] = $this->cartStatus["product_qty_updated"];
            $this->responseData["cart_id"] = $cartHashId;
        }
    }
    else
    {

        $this->quantityValidation($request->qty);

        // check product exist on product table, product status =1, in stock, same channel
        $this->checkProductConditions($productId, $request);


        // if product does not exist on table of that cart hash id, add product on cart on that cart hash id:
        $cartItemCreateData = [
            "cart_id" => $cartHashId,
            "product_id" => $productId,
            "qty" => $request->qty ?? 1
        ];
        $this->cartItemRepo->create($cartItemCreateData);

        $this->responseData["message"] = $this->cartStatus["product_added"];
        $this->responseData["cart_id"] = $cartHashId;
    }
        return true;
    }

    private function quantityValidation(?int $qty): void
    {
        if (isset($qty) && $qty <= 0) throw ValidationException::withMessages(["quantity" => __("core::app.exception_message.product-qty-must-be-above-0")]);
    }

    private function itemClearFromCart(array $cartAndProductCheck, string $cartHashId): void
    {
        try
        {
            $this->cartItemRepo->deleteCartItem($cartAndProductCheck);
            //  if there is no cart items on cart_items table then delete that cart hash id row from carts table
            $checkCartItemsExitsOnCartHashId = $this->cartItem->whereCartId($cartHashId)->first()?->id;
            if (!$checkCartItemsExitsOnCartHashId) $this->model::whereId($cartHashId)->delete();
            
            $this->responseData["message"] = $this->cartStatus["product_removed"];
            $this->responseData["cart_id"] = $checkCartItemsExitsOnCartHashId ? $cartHashId : "";
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}

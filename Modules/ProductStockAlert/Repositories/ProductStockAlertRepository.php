<?php

namespace Modules\ProductStockAlert\Repositories;

use Exception;
use Modules\Core\Entities\Store;
use Modules\Product\Entities\Product;
use Modules\Core\Repositories\BaseRepository;
use Modules\ProductStockAlert\Entities\ProductAlertStock;

class ProductStockAlertRepository extends BaseRepository
{
    protected $productAlertStock;

    public function __construct(ProductAlertStock $productAlertStock, Product $product, Store $store)
    {
        $this->model = $productAlertStock;
        $this->model_key = "productAlertStock";
        $this->product = $product;
        $this->store = $store;
       
        $this->rules = [
            "product_id" => "required|exists:products,id",
            "email_address" => "sometimes|email"
        ];
    }

    public function createProductStockAlert(object $request): mixed
    {
        try
        {
            $merge = auth("customer")->id() ? [] : ["email_address" => "required|email"]; 
             
            $this->validateData($request, $merge);

            $coreCache = $this->getCoreCache($request);
            
            $data = [
                "customer_id" => auth("customer")->id(),
                "email_address" => auth("customer")->id() ? null : $request->email_address,
                "product_id" => $request->product_id,
                "store_id" => $coreCache->store->id,
            ];

           $created = $this->create($data);
  
        } 
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return $created;
    }

}

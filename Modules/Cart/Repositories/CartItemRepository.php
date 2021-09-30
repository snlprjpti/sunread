<?php

namespace Modules\Cart\Repositories;

use Exception;
use Modules\Cart\Entities\CartItem;
use Modules\Core\Repositories\BaseRepository;


class CartItemRepository extends BaseRepository
{
    public function __construct(CartItem $cartItem)
    {
        $this->model = $cartItem;
        $this->model_key = "cart_items";
    }

    public function createCartItem(array $data): object
    {
        try
        {
            $created =  $this->create($data);
        } 
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $created;
    }

    public function deleteCartItem(array $whereCondition): bool
    {
        try
        {
            $this->model->where($whereCondition)->delete();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }

    public function updateItemWithConditions(array $whereCondition, array $data): bool
    {
        try
        {
            $this->model::where($whereCondition)->update($data);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return true;
    }
}

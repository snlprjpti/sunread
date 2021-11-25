<?php

namespace Modules\Sales\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Sales\Entities\OrderComment;

class OrderCommentRepository extends BaseRepository
{   
    public function __construct(OrderComment $order_comment)
    {
        $this->model = $order_comment;
        $this->model_key = "OrderComment";
        $this->rules = [
            "comment" => "required",
            "order_id" => "required|exists:orders,id",
            "is_customer_notified" => "sometimes|boolean",
            "is_visible_on_storefornt" => "sometimes|boolean"
        ];
    }

}

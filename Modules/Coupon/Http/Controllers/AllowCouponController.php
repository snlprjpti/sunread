<?php

namespace Modules\Coupon\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Coupon\Entities\AllowCoupon;
use Exception;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Repositories\AllowCouponRepository;
use Modules\Coupon\Transformers\AllowCouponResource;

class AllowCouponController extends BaseController
{
    private $coupon;
    private $repository;

    public function __construct(AllowCoupon $allowCoupon, Coupon $coupon, AllowCouponRepository $allowCouponRepository)
    {
        $this->model = $allowCoupon;
        $this->model_name = "Allow Coupon";
        $this->coupon = $coupon;
        $this->repository = $allowCouponRepository;
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return AllowCouponResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new AllowCouponResource($data);
    }

    public function allowCoupon(Request $request, int $coupon_id): JsonResponse
    {
        try
        {
            $this->coupon->where('id',$coupon_id)->where('status',1)->firstOrFail();
            foreach($request->all() as $key=>$value){
                $model_type = $value['model_type'];
                $status = $value['status'];
                foreach($value['model_id'] as $model_id){
                    $request->request->add(['coupon_id' => $coupon_id, 'model_type'=>$model_type, 'model_id'=>$model_id, 'status'=>$status]);
                    $allow_exist = $this->repository->allowedCouponExist($request);
                    if ($allow_exist < 1) {
                        $data = $this->repository->validateData($request);
                        $created = $this->repository->create($data);
                    }
                }
            }
            if(empty($created)){
                return $this->successResponseWithMessage($this->lang('already-created'));
            }
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }
}

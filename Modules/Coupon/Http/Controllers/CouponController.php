<?php

namespace Modules\Coupon\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Repositories\CouponRepository;
use Modules\Coupon\Transformers\CouponResource;
use Exception;

class CouponController extends BaseController
{
    private $coupon;
    private $repository;

    public function __construct(Coupon $coupon, CouponRepository $couponRepository)
    {
        $this->model = $coupon;
        $this->model_name = "Coupon";
        $this->repository = $couponRepository;
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return CouponResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new CouponResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function create()
    {
        return view('coupon::create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('coupon::show');
    }

    public function edit($id)
    {
        return view('coupon::edit');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}

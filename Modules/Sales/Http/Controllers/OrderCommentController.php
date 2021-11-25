<?php

namespace Modules\Sales\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Sales\Entities\Order;
use Modules\Sales\Entities\OrderComment;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Sales\Transformers\OrderCommentResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;
use Modules\Sales\Repositories\OrderCommentRepository;

class OrderCommentController extends BaseController
{
    protected $repository;

    public function __construct(OrderComment $orderComment, OrderCommentRepository $orderCommentRepository)
    {
        $this->model = $orderComment;
        $this->model_name = "Order Comment";
        $this->repository = $orderCommentRepository;
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return OrderCommentResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new OrderCommentResource($data);
    }

    public function index(Request $request, int $order_id): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request, callback:function() use ($order_id) {
                return $this->model->where("order_id", $order_id);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    public function store(Request $request, int $order_id): JsonResponse
    {
        try
        {
            $request->merge(["order_id" => $order_id]);
            $data = $this->repository->validateData($request, callback:fn() => [ "user_id" => auth('admin')->id() ]);
            $created = $this->repository->create($data);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang("create-success"), 201);
    }

    public function show(int $order_id, int $comment_id): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetch($comment_id, callback:function () use ($order_id) {
                return $this->model->where("order_id", $order_id);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($fetched), $this->lang("fetch-success"));
    }

    public function update(Request $request, int $order_id, int $comment_id): JsonResponse
    {
        try
        {
            $request->merge(["order_id" => $order_id]);
            $data = $this->repository->validateData($request, callback:fn() => [ "user_id" => auth('admin')->id() ] );
            $updated = $this->repository->update($data, $comment_id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

    public function destroy(int $order_id, int $comment_id): JsonResponse
    {
        try
        {
            $this->repository->delete($comment_id, callback:function () use ($order_id) {
                return $this->model->where("order_id", $order_id);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang("delete-success"));
    }
}

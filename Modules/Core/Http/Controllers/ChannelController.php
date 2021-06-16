<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Channel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Repositories\ChannelRepository;
use Modules\Core\Transformers\ChannelResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChannelController extends BaseController
{
    protected $repository;

    public function __construct(Channel $channel, ChannelRepository $channelRepository)
    {
        $this->model = $channel;
        $this->model_name = "Channel";
        $this->repository = $channelRepository;

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return ChannelResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new ChannelResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request, callback: function() use ($request) {
                $request->validate([
                    "website_id" => "sometimes|exists:websites,id"
                ]);
                return $request->website_id ? $this->model->whereWebsiteId($request->website_id) : $this->model;
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);

            foreach(["logo", "favicon"] as $file_type) {
                if ( !$request->file($file_type) ) continue;
                $data[$file_type] = $this->storeImage($request, $file_type, strtolower($this->model_name));
            }

            $created = $this->repository->create($data);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetch($id, ["default_store", "default_category", "stores", "website"]);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request, [
                "code" => "required|unique:channels,code,{$id}",
                "hostname" => "required|unique:channels,hostname,{$id}",
                "logo" => "nullable|mimes:bmp,jpeg,jpg,png,webp",
                "favicon" => "nullable|mimes:bmp,jpeg,jpg,png,webp"
            ]);

            foreach(["logo", "favicon"] as $file_type) {
                if ( !$request->file($file_type) ) {
                    unset($data[$file_type]);
                    continue;
                }
                $data[$file_type] = $this->storeImage($request, $file_type, strtolower($this->model_name));
            }

            $updated = $this->repository->update($data, $id);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id, function($deleted) {
                foreach(["logo", "favicon"] as $file_type) {
                    if ( !$deleted->{$file_type} ) continue;
                    Storage::delete($deleted->{$file_type});
                }
            });
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try
        {
            $updated = $this->repository->updateStatus($request, $id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("status-updated"));
    }
}

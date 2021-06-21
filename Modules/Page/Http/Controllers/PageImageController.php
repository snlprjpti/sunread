<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;
use Exception;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\PageImage;
use Modules\Page\Repositories\PageImageRepository;
use Modules\Page\Transformers\PageImageResource;

class PageImageController extends BaseController
{
    private $repository;

    public function __construct(PageImageRepository $pageImageRepository, PageImage $pageImage)
    {
        $this->repository = $pageImageRepository;
        $this->model = $pageImage;
        $this->model_name = "Page Image";
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return PageImageResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new PageImageResource($data);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->repository->validateData($request);
            foreach($request->file("image") as $file){
                $image = $this->repository->createImage($file);
                $data = array_merge($data,$image);
                $created = $this->repository->create($data);
            }
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id, function ($deleted) {
                if ($deleted->path){
                    Storage::delete($deleted->path);
                }
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }
}

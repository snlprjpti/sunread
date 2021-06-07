<?php

namespace Modules\Page\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTranslation;
use Modules\Page\Repositories\PageRepository;
use Modules\Page\Repositories\PageTranslationRepository;
use Modules\Page\Transformers\PageResource;
use Exception;

class PageController extends BaseController
{
    protected $repository, $translation;

    private $pageTranslationRepository;

    public function __construct(Page $page, PageRepository $pageRepository, PageTranslationRepository $pageTranslationRepository)
    {
        $this->model = $page;
        $this->model_name = "Page";
        $this->repository = $pageRepository;
        $this->pageTranslationRepository = $pageTranslationRepository;
        parent::__construct($this->model, $this->model_name);
    }
    public function collection(object $data): ResourceCollection
    {
        return PageResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new PageResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request);
        }
        catch (Exception $exception)
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
            $data["slug"] = $data["slug"] ?? $this->model->createSlug($request->title);
            $created = $this->repository->create($data, function($created) use($request){
                $this->pageTranslationRepository->updateOrCreate($request->translation, $created);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->model->with(["translations"])->findOrFail($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request,[
                "slug" => "nullable|unique:pages,slug,{$id}",
            ]);
            if ( $request->slug == null ) $data["slug"] = $this->model->createSlug($request->title);

            $updated = $this->repository->update($data, $id, function($updated) use($request){
                $this->pageTranslationRepository->updateOrCreate($request->translations, $updated);
            });
            $updated->translations = $updated->translations()->get();
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id, function($deleted){
                $deleted->translations()->each(function($translation){
                    $translation->delete();
                });
            });
        }
        catch (Exception $exception)
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

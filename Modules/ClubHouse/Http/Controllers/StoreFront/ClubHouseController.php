<?php

namespace Modules\ClubHouse\Http\Controllers\StoreFront;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\ClubHouse\Entities\ClubHouse;
use Illuminate\Contracts\Support\Renderable;
use Modules\Core\Http\Controllers\BaseController;
use Modules\ClubHouse\Transformers\ClubHouseResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\ClubHouse\Repositories\ClubHouseRepository;

class ClubHouseController extends BaseController
{
    protected $repository;

    public function __construct(ClubHouseRepository $club_house_repository, ClubHouse $clubHouse)
    {
        $this->repository = $club_house_repository;
        $this->model = $clubHouse;
        $this->model_name = "Clubhouse";

        $this->middleware('validate.website.host')->only(['index']);
        $this->middleware('validate.channel.code')->only(['index']);
        $this->middleware('validate.store.code')->only(['index']);

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return ClubHouseResource::collection($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try
        {
            $club_house = $this->model->findOrFail($id);

            $data = $this->repository->fetchWithAttributes($request, $club_house);
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($data, $this->lang('fetch-success'));
    }
}

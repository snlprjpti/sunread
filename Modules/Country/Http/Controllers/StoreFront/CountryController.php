<?php

namespace Modules\Country\Http\Controllers\StoreFront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Country\Entities\Country;
use Modules\Country\Repositories\CountryRepository;
use Modules\Country\Transformers\CountryResource;
use Exception;

class CountryController extends BaseController
{
    private $repository;

    public function __construct(Country $country, CountryRepository $countryRepository)
    {
        $this->model = $country;
        $this->model_name = "Country";
        parent::__construct($this->model, $this->model_name);
        $this->repository = $countryRepository;
    }

    public function collection(object $data): ResourceCollection
    {
        return CountryResource::collection($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $request->without_pagination = true;
            $request->sort_by = "name";
            $request->sort_order = "ASC";
            $fetched = $this->repository->fetchAll($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }
}

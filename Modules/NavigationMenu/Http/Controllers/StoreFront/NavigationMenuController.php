<?php

namespace Modules\NavigationMenu\Http\Controllers\StoreFront;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Modules\NavigationMenu\Entities\NavigationMenu;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\NavigationMenu\Repositories\NavigationMenuRepository;
use Modules\NavigationMenu\Transformers\StoreFront\NavigationMenuResource;

class NavigationMenuController extends BaseController
{
    protected $repository;

    public function __construct(NavigationMenuRepository $navigation_menu_repository, NavigationMenu $navigation_menu)
    {
        $this->repository = $navigation_menu_repository;
        $this->model = $navigation_menu;
        $this->model_name = "Navigation Menu";

        $this->middleware('validate.website.host');
        $this->middleware('validate.channel.code');
        $this->middleware('validate.store.code');

        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return NavigationMenuResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new NavigationMenuResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchItemsFromCache($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        return $this->successResponse($fetched, $this->lang("fetch-list-success"));

    }
}

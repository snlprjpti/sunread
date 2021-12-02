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
use Modules\NavigationMenu\Repositories\NavigationMenuItemRepository;
use Modules\NavigationMenu\Transformers\StoreFront\NavigationMenuResource;

class NavigationMenuController extends BaseController
{
    protected $repository, $navigation_menu_item_repository;

    public function __construct(NavigationMenuRepository $navigation_menu_repository, NavigationMenuItemRepository $navigation_menu_item_repository, NavigationMenu $navigation_menu)
    {
        $this->repository = $navigation_menu_repository;
        $this->navigation_menu_item_repository = $navigation_menu_item_repository;
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
            $fetched = $this->navigation_menu_item_repository->fetchItemsFromCache($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return response()->json([
            "status" => "success",
            "payload" => ['data' => $fetched],
            "message" => "Navigation Menu Fetched Successfully"
        ]);
    }
}

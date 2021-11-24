<?php

namespace Modules\NavigationMenu\Http\Controllers\StoreFront;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Facades\CoreCache;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Modules\NavigationMenu\Entities\NavigationMenu;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Redis;
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
            $coreCache = $this->repository->getCoreCache($request);
            $website = $coreCache->website;
            $channel = $coreCache->channel;
            $store = $coreCache->channel;

            $redis_nav_menu_key = "sf_nav_menu_website_{$website->hostname}_channel_{$channel->code}_store_{$store->code}";

            if($this->repository->checkIfRedisKeyExists($redis_nav_menu_key)) {
                $navigation_menu = collect(json_decode(Redis::get($redis_nav_menu_key)));
            } else {
                $fetched = $this->navigation_menu_item_repository->fetchWithItems($request, ["navigationMenuItems"], callback:function() use($website){
                    return $this->model->where('status', 1)->whereNotNull('location')->where('website_id', $website->id);
                });
                $navigation_menu = $fetched;
                $this->repository->storeCache($redis_nav_menu_key, $navigation_menu);
            }
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return response()->json([
            "status" => "success",
            "payload" => ['data' => $navigation_menu],
            "message" => "Navigation Menu Fetched Successfully"
        ]);
    }
}

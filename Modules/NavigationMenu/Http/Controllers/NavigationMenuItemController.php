<?php

namespace Modules\NavigationMenu\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Core\Rules\ScopeRule;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\NavigationMenu\Entities\NavigationMenuItem;
use Modules\NavigationMenu\Rules\NavigationMenuItemScopeRule;
use Modules\NavigationMenu\Transformers\NavigationMenuItemResource;
use Modules\NavigationMenu\Repositories\NavigationMenuItemRepository;
use Modules\NavigationMenu\Repositories\NavigationMenuItemValueRepository;

class NavigationMenuItemController extends BaseController
{
    // Protected properties
    protected $repository, $navigation_menu_item_value_repository;

    /**
     * NavigationMenuItemController Class constructor
     */
    public function __construct(NavigationMenuItemRepository $navigation_menu_item_repository, NavigationMenuItem $navigation_menu_item, NavigationMenuItemValueRepository $navigation_menu_item_value_repository)
    {
        $this->repository = $navigation_menu_item_repository;
        $this->navigation_menu_item_value_repository = $navigation_menu_item_value_repository;

        $this->model = $navigation_menu_item;
        $this->model_name = "Navigation Menu Item";

        // Calling Parent Constructor of BaseController
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Returns NavigationMenuItemResource Collection
     */
    public function collection(object $data): ResourceCollection
    {
        return NavigationMenuItemResource::collection($data);
    }

    /**
     * Returns NavigationMenuItemResource
     */
    public function resource(object $data): JsonResource
    {
        return new NavigationMenuItemResource($data);
    }

    /**
     * Fetches and returns the list of NavigationMenuItem
     */
    public function index(Request $request): JsonResponse
    {
        try
        {
            $request->validate([
                "scope" => "sometimes|in:website,channel,store",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request->scope), new NavigationMenuItemScopeRule($request)],
            ]);
            $fetched = $this->repository->fetchAll($request, ["values", "navigationMenu"]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    /**
     * Validates and Creates NavigationMenuItem with NavigationMenuItemValue
     */
    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->navigation_menu_item_value_repository->validateWithValuesCreate($request);

            $created = $this->repository->create($data, function ($created) use ($data) {
                $this->navigation_menu_item_value_repository->createOrUpdate($data, $created);
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang("create-success"), 201);
    }

    /**
     * Fetches and returns the NavigationMenuItem by Id
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try
        {
            $request->validate([
                "scope" => "sometimes|in:website,channel,store",
                "scope_id" => [ "sometimes", "integer", "min:1", new ScopeRule($request->scope), new NavigationMenuItemScopeRule($request, $id)]
            ]);

            $fetched = $this->repository->fetch($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    /**
     * Validates and Updates NavigationMenuItem with NavigationMenuItem values
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $navigation_menu_item = $this->model->findOrFail($id);

            $data = $this->navigation_menu_item_value_repository->validateWithValuesUpdate($request, $navigation_menu_item);

            $updated = $this->repository->update($data, $id, function ($updated) use ($data) {
                $this->navigation_menu_item_value_repository->createOrUpdate($data, $updated);
                $updated->load("values");
            });
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang("update-success"));
    }

    /**
     * Finds and Deletes NavigationMenuItem
     */
    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->model->findOrFail($id);

            $this->repository->delete($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }

    /**
     * Updates the Status of NavigationMenuItem with given Id
     */
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

    /**
     * Fetches and returns Attributes for NavigationMenuItem Values
     */
    public function attributes(Request $request): JsonResponse
    {
        try
        {
            $request->validate([
                "scope" => "sometimes|in:website,channel,store"
            ]);

            $fetched = $this->repository->getConfigData([
                "scope" => $request->scope ?? "website"
            ]);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($fetched, $this->lang("fetch-success"));
    }
}

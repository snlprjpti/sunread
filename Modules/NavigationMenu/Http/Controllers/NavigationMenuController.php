<?php

namespace Modules\NavigationMenu\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Http\Controllers\BaseController;
use Modules\NavigationMenu\Entities\NavigationMenu;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\NavigationMenu\Transformers\NavigationMenuResource;
use Modules\NavigationMenu\Repositories\NavigationMenuRepository;

class NavigationMenuController extends BaseController
{
     // Protected properties
     protected $repository;

     /**
      * ClubHouseController Class constructor
      */
     public function __construct(NavigationMenuRepository $navigationMenuRepository, NavigationMenu $navigationMenu)
     {
         $this->repository = $navigationMenuRepository;

         $this->model = $navigationMenu;
         $this->model_name = "Navigation Menu";

         // Calling Parent Constructor of BaseController
         parent::__construct($this->model, $this->model_name);
     }

     /**
      * Returns ClubHouseResource in Collection
      */
     public function collection(object $data): ResourceCollection
     {
         return NavigationMenuResource::collection($data);
     }

     /**
      * Returns ClubHouseResource
      */
     public function resource(object $data): JsonResource
     {
         return new NavigationMenuResource($data);
     }

     /**
      * Fetches and returns the list of ClubHouse
      */
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

     /**
      * Validates and Creates Clubhouse with Clubhouse values
      */
     public function store(Request $request): JsonResponse
     {
         try
         {
             $data = $this->repository->validateData($request);

             $created = $this->repository->create($data);
         }
         catch (Exception $exception)
         {
             return $this->handleException($exception);
         }

         return $this->successResponse($this->resource($created), $this->lang("create-success"), 201);
     }

     /**
      * Fetches and returns the ClubHouse by Id
      */
     public function show(Request $request, int $id): JsonResponse
     {
         try
         {
             $fetched = $this->repository->fetch($id);
         }
         catch (Exception $exception)
         {
             return $this->handleException($exception);
         }

         return $this->successResponse($fetched, $this->lang('fetch-success'));
     }

     /**
      * Validates and Updates Clubhouse with Clubhouse values
      */
     public function update(Request $request, int $id): JsonResponse
     {
         try
         {
            $data = $this->repository->validateData($request);

             $updated = $this->repository->update($data, $id);
         }
         catch (Exception $exception)
         {
             return $this->handleException($exception);
         }

         return $this->successResponse($this->resource($updated), $this->lang("update-success"));
     }

     /**
      * Finds and Deletes Clubhouse
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
      * Updates the Status of Clubhouse with given Id
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

}

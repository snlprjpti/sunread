<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\ActivityLog;
use Modules\Core\Transformers\ActivityResource;

class ActivityController extends BaseController
{
    protected $pagination_limit;
    protected $model_name = "Activity Log";

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $activity_log = ActivityLog::first();
            dd(class_basename($activity_log->causer));
            $this->validate($request, [
                'limit' => 'sometimes|numeric',
                'page' => 'sometimes|numeric',
                'sort_by' => 'sometimes',
                'sort_order' => 'sometimes|in:asc,desc',
                'q' => 'sometimes|string|min:1'
            ]);

            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';

            $activity_logs = ActivityLog::query();

            if ($request->has('q')) {
                $activity_logs->whereLike(ActivityLog::$SEARCHABLE, $request->get('q')) ;
            }
            $activity_logs = $activity_logs->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit') ? $request->get('limit') : $this->pagination_limit;

            $activity_logs = $activity_logs->paginate($limit);
            return $this->successResponse(ActivityResource::collection($activity_logs), trans('core::app.response.fetch-list-success', ['name' => $this->model_name]));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $this->validate($request, [
                'name' => 'required',
                'permission_type' => 'required|in:all,custom',
                'permissions' => 'sometimes'
            ]);

            $role = ActivityLog::create(
                $request->only('name', 'description', 'permissions', 'permission_type', 'slug')
            );
            return $this->successResponse($payload = $role, trans('core::app.response.create-success', ['name' => 'Role']), 201);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $activity_log = ActivityLog::findOrFail($id);
            return $this->successResponse($activity_log, trans('core::app.response.fetch-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]));

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $activity_log = ActivityLog::findOrFail($id);
            $activity_log->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    public function bulkDelete(Request $request)
    {
        try {
            $this->validate($request,[
                'ids' => 'array|required',
                'ids.*' => 'required|exists:activity_logs',
            ]);
            $ids = $request->get('ids');

            //ActivityLog::destroy($ids);
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

}

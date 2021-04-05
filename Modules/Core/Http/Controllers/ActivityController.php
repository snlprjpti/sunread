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
    public function __construct(ActivityLog $activity_log)
    {
        $this->model = $activity_log;
        $this->model_name = "Activity Log";
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try
        {
            $this->validateListFiltering($request);
            $activity_logs = $this->getFilteredList($request);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(ActivityResource::collection($activity_logs), $this->lang('fetch-list-success'));
    }

    /**
     * Show the specified resource.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try
        {
            $activity_log = $this->model->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new ActivityResource($activity_log), $this->lang('fetch-success'));
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $activity_log = $this->model->findOrFail($id);
            $activity_log->delete();
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new ActivityResource($activity_log), $this->lang('delete-success'));
    }

    /**
     * Remove the specified resource in bullk.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        try
        {
            $this->validate($request, [
                'ids' => 'array|required',
                'ids.*' => 'required|exists:activity_logs,id',
            ]);

            $activity_logs = $this->model->whereIn('id', $request->ids);
            $activity_logs->delete();
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}

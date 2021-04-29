<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\ActivityLog;
use Modules\Core\Repositories\ActivityRepository;
use Modules\Core\Transformers\ActivityResource;

class ActivityController extends BaseController
{
    private $activityRepository;

    public function __construct(ActivityLog $activity_log, ActivityRepository $activityRepository)
    {
        $this->model = $activity_log;
        $this->model_name = "Activity Log";
        parent::__construct($this->model, $this->model_name);
        $this->activityRepository = $activityRepository;
    }

    public function collection(object $data): ResourceCollection
    {
        return ActivityResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new ActivityResource($data);
    }

    public function index(Request $request)
    {
        try {
            $this->validateListFiltering($request);
            $activity_logs = $this->getFilteredList($request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($activity_logs), $this->lang('fetch-list-success'));
    }

    /**
     * Show the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $activity_log = $this->model->findOrFail($id);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($activity_log), $this->lang('fetch-success'));
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
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'), 204);
    }

    /**
     * Remove the specified resource in bullk.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        try {
            $this->validate($request, [
                'ids' => 'array|required',
                'ids.*' => 'required|exists:activity_logs,id',
            ]);

            $activity_logs = $this->model->whereIn('id', $request->ids);
            $activity_logs->delete();
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }
        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}

<?php


namespace Modules\Core\Repositories;


use Modules\Core\Entities\ActivityLog;

class ActivityRepository extends BaseRepository
{
    /**
     * @var ActivityLog
     */
    private $activityLog;

    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
        $this->model_key = "core.activity";
        $this->rules = [
            "description" => "required",
        ];
    }
}

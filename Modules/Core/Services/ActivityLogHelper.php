<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Entities\ActivityLog;

class ActivityLogHelper {

    private $activityLog;
    private $user;
    private $log;

    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;

        $this->user = Auth::guard('admin')->user() ? Auth::guard('admin')->user() : null;

        if($this->user != null) {
            $this->log['causer_id'] = $this->user->id;
            $this->log['causer_type'] = get_class($this->user);
        }

    }

    public function log($model, $event) {

        $this->log['log_name'] = 'default';
        $this->log['description'] = $event;
        $this->log['subject_id'] = $model->id;
        $this->log['subject_type'] = get_class($model);

        if($event == 'updated') {
            $newValues = $model->getChanges();
            $oldValues = [];
            foreach($newValues as $key=>$value) {
                $oldValues[$key] = $model->getOriginal($key) ;
            }
            $this->log['properties'] = [
                'from' => $oldValues,
                'to' => $newValues
            ];
        }
        elseif($event == 'created') {
            $newValues = $model->toArray();
            $this->log['properties'] = $newValues;
        }

        $this->activityLog->create($this->log);

    }

}

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
        $this->user = Auth::user() ? Auth::user() : null;

        if($this->user != null) {
            $this->log['causer_id'] = $this->user->id;
            $this->log['causer_type'] = get_class($this->user);
        }

    }

    public function log($model, $event) {
        $model_name = class_basename($model);
        $action = $model_name." ". $event;
        $this->log['log_name'] = 'default';
        $this->log['description'] = $event;
        $this->log['subject_id'] = $model->id;
        $this->log['subject_type'] = get_class($model);
        $this->log['action'] = $action;
        $this->log['activity'] = $action;

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
            $this->log['activity'] = $action. " for properties: ". implode(',', array_keys($oldValues));
        }
        elseif($event == 'created') {
            $newValues = $model->toArray();
            $this->log['properties'] = $newValues;
        }
        $this->activityLog->create($this->log);

    }

}

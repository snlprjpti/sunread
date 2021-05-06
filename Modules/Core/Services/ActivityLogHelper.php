<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\ActivityLog;
use Modules\Review\Entities\ReviewVote;

class ActivityLogHelper {

    private $activityLog, $user, $log;

    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
        $this->user = Auth::user() ?? null;
        $this->log = [];

        if($this->user) {
            $this->log = [
                "causer_id" => $this->user->id,
                "causer_type" => get_class($this->user)
            ];
        }
    }

    public function log($model, $event, $action = null, $activity = null) {
        $model_name = class_basename($model);
        $default_action = $model_name." ".$event;
        $properties = [];

        if($model_name == "ReviewVote")
        {
           $this->reviewVoteCache($model); 
        }
        
        if(Cache::get($model::class))
        {
            $this->modelCache($model);
        }
        
        if ( $event == "updated" ) {
            $newValues = $model->getChanges();
            $oldValues = collect($newValues)->mapWithKeys(function($value, $key) use ($model) {
                return [$key => $model->getOriginal($key)];
            })->toArray();

            $properties = [
                'from' => $oldValues,
                'to' => $newValues
            ];
            $activity = "{$default_action} for properties: ".implode(',', array_keys($oldValues));
        }

        if( $event == "created" ) {
            $newValues = $model->toArray();
            $properties = $newValues;
        }

        $log = [
            "log_name" => 'default',
            "description" => $event,
            "subject_id" => $model->id,
            "subject_type" => get_class($model),
            "action" => $action ?? $default_action,
            "activity" => $activity ?? $default_action,
            "properties" => $properties
        ];

        $this->activityLog->create( array_merge($this->log, $log) );
    }

    public function reviewVoteCache(object $model)
    {
        Cache::forget('positive_vote_count-'.$model->review_id);
        Cache::rememberForever('positive_vote_count-'.$model->review_id, function() use($model){
            return ReviewVote::where('review_id', $model->review_id)->where('vote_type', 0)->count();
        });
        
        Cache::forget('negative_vote_count-'.$model->review_id);
        Cache::rememberForever('negative_vote_count-'.$model->review_id, function() use($model){
            return ReviewVote::where('review_id', $model->review_id)->where('vote_type', 1)->count();
        });
    }

    public function modelCache(object $model)
    {
        Cache::forget($model::class);
        Cache::rememberForever($model::class, function() use ($model){
            return $model->get();
        });  
    }
}

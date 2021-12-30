<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Core\Entities\ActivityLog;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Product\Jobs\UpdateProductInventoryJob;
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

        if($model_name == "ReviewVote") $this->reviewVoteCache($model);

        if ($model_name == "Attribute" || $model_name == "AttributeSet" || $model_name == "AttributeOption") $this->attributeCache($model);

        if ($event !== "deleted" && $model_name == "Product" && isset($model->parent)) {
            $this->updateProductInventory($model, $event);
        }

        if(Cache::get($model::class)) $this->modelCache($model);

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

    public function reviewVoteCache(object $model): void
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

    public function attributeCache(): void
    {
        Cache::forget("attributes_attribute_set");
        Cache::remember("attributes_attribute_set", Carbon::now()->addDays(2), function () {
            return AttributeSet::with([ "attribute_groups.attributes" ])->get();     
        });

        Cache::forget("attribute_options");
        Cache::remember("attribute_options", Carbon::now()->addDays(2) ,function () {
            return AttributeOption::with([ "attribute" ])->get();
        });

        Cache::forget("attributes");
        Cache::remember("attributes", Carbon::now()->addDays(2) ,function () {
            return Attribute::with([ "attribute_options" ])->get();
        });
    }

    public function modelCache(object $model): void
    {
        Cache::forget($model::class);
        // Cache::rememberForever($model::class, function() use ($model){
        //     return $model->get();
        // });  
    }

    public function updateProductInventory(object $product, string $event): void
    {
        try
        {
            if ( $product->parent )
            {
                $other_variants = $product->parent->variants;
                $other_variant_quantities = $other_variants->map( function ($other_variant) {
                    return $other_variant->catalog_inventories()->first()?->quantity;
                })->toArray();

                $value = array_sum($other_variant_quantities); 

                $data = [
                    "quantity" => $value,
                    "use_config_manage_stock" => 1,
                    "product_id" => $product->parent->id,
                    "website_id" => $product->parent->website_id,
                    "manage_stock" =>  0,
                    "is_in_stock" => ($value > 0) ? 1 : 0,
                ];

                $match = [
                    "product_id" => $product->parent->id,
                    "website_id" => $product->parent->website_id
                ];

                CatalogInventory::updateOrCreate($match, $data);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

    }
}

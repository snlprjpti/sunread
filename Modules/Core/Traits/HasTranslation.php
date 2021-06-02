<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

trait HasTranslation 
{
    protected $translationStoreID;

	public function allTranslations(?int $storeID = null, ?array $with = []): collection
	{
		$this->translationStoreID = (!$storeID) ? $this->getStore() : $storeID;
        
        if ($with != []) {   
            $model = $this->with($with)->get();

            $relation_translation = [];
            foreach ($model as $data) 
            {
                foreach ($data->relations as $relation)
                {
                    $relation_translation[] = $this->translation($relation);
                }
            }
        }
        else {
            $model = $this->all();
        }
        if (!$this->translationStoreID) return $model;

		$translated_data = [];
		foreach($model as $data) $translated_data[] = $this->translation($data);
		return new Collection($translated_data);
	}

    public function paginate(collection $items, int $perPage = 15, string $page = null, array $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

	public function translation(object $data): object
    {
        $translation = $this->getTranslateData($data);
        if($translation) 
        {
            array_map(function($attribute) use($data, $translation) {
                return $data->$attribute = $translation->$attribute;
            }, $this->translatedAttributes);
        }
        return $data;
    }
 
    public function getTranslateData(object $data)
    {
        return $data->translations()->where('store_id', $this->translationStoreID)->first(); 
    }

    public function firstTranslation(int $storeID = null)
    {
        $this->translationStoreID = (!$storeID) ? $this->getStore() : $storeID;
        return (!$this->translationStoreID) ? $this : $this->translation($this);
    }

	public function getStore(): int
	{
		return array_key_exists("store_id", getallheaders()) ? (int) getallheaders()["store_id"] : 0;
	}
 }
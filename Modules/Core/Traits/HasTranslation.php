<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Modules\Attribute\Entities\AttributeTranslation;

trait HasTranslation 
{
    public function getAttribute($name)
    {
        $storeID = $this->getStoreId();
        if($storeID != 0)
        {
            $translation = $this->getTranslateData($storeID);
            if($translation) 
            {
                array_map(function($attribute) use($translation) {
                    parent::setAttribute($attribute, $translation->$attribute);
                    return $this->$attribute = $translation->$attribute;
                }, $this->translatedAttributes);
            }        
        }
        return parent::getAttribute($name);
    }

    public function getTranslateData($storeID)
    {
        $translationModel = new $this->translatedModels[0]();
        $relation = $translationModel::where($this->translatedModels[1], $this->attributes["id"])
        ->where('store_id', $storeID)->first();
        return $relation; 
    }
    
    public function getStoreId(): int
    {
        return array_key_exists("store_id", getallheaders()) ? (int) getallheaders()["store_id"] : 0;
	}

    public function firstTranslation($store_id)
    {
        $translation = $this->translations()->where('store_id', $store_id)->first(); 
        if($translation) 
        {
            $item = $this->toArray();
            foreach($this->translatedAttributes as $attribute) $item[$attribute] = $translation->$attribute;
            return $item;
        }
        return $this->toArray();
    }

}
<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Core\Facades\CoreCache;

trait HasTranslation 
{
    public function getAttribute($name)
    {
        $store_code = $this->getStoreId();

        if($store_code)
        {
            $store = CoreCache::getStoreWithCode($store_code);
            $translation = $this->getTranslateData($store->id);
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

    public function getTranslateData(int $storeID)
    {
        $translationModel = new $this->translatedModels[0]();
        $relation = $translationModel::where($this->translatedModels[1], $this->attributes["id"])
        ->where('store_id', $storeID)->first();
        return $relation; 
    }
    
    public function getStoreId(): ?string
    {
        return array_key_exists("hc-store", getallheaders()) ? getallheaders()["hc-store"] : null;
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
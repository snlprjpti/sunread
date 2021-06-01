<?php

namespace Modules\Core\Traits;

use Modules\Core\Entities\Store;

trait HasTranslation 
{
	public function allTranslations()
	{
		$store = $this->getStore();
		$model = $this->all();
		if (!$store) return $model;

		$translated_data = [];
		foreach($model as $data) $translated_data[] = $this->translation($data);

		return collect($translated_data);
	}

	public function translation($data, $storeID = null)
    {
        $translation = $this->getTranslationData($data, $storeID ?? $storeID);
        if($translation) 
        {
            array_map(function($attribute) use($data, $translation) {
                return $data->$attribute = $translation->$attribute;
            }, $this->translatedAttributes);
        }
        return $data;
    }
 
    public function getTranslationData($data, $storeID = null)
    {
        if(!$storeID) $storeID =  $this->getStore();
        return  $data->translations()->where('store_id', $storeID)->first(); 
    }

    public function firstTranslation()
    {
        return (!$this->getStore()) ? $this : $this->translation($this);
    }

	public function getStore()
	{
		return array_key_exists("store_id", getallheaders()) ? getallheaders()["store_id"] : 0;
	}
 }
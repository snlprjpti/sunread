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

	public function translation($data)
    {
        $translation = $this->getTranslationData($data);
        if($translation) 
        {
            array_map(function($attribute) use($data, $translation) {
                return $data->$attribute = $translation->$attribute;
            }, $this->translatedAttributes);
        }
        return $data;
    }
 
    public function getTranslationData($data)
    {
       return  $data->translations()->where('store_id', $this->getStore()->id)->first(); 
    }

    public function firstTranslation()
    {
		$store = $this->getStore();
        if(!$store) return $this;
        return $this->translation($this);
    }

	public function getStore()
	{
		$store_id = array_key_exists("store_id", getallheaders()) ? getallheaders()["store_id"] : 0;
		return Store::whereId($store_id)->first();
	}
 
   


}
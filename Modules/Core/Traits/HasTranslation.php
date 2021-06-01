<?php

namespace Modules\Core\Traits;

trait HasTranslation 
{
    protected $translationStoreID;

	public function allTranslations($storeID = null)
	{
		$this->translationStoreID = (!$storeID) ? $this->getStore() : $storeID;
        $model = $this->all();
        
		if (!$this->translationStoreID) return $model;

		$translated_data = [];
		foreach($model as $data) $translated_data[] = $this->translation($data);

		return collect($translated_data);
	}

	public function translation($data)
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
 
    public function getTranslateData($data)
    {
        return  $data->translations()->where('store_id', $this->translationStoreID)->first(); 
    }

    public function firstTranslation($storeID = null)
    {
        $this->translationStoreID = (!$storeID) ? $this->getStore() : $storeID;
        return (!$this->translationStoreID) ? $this : $this->translation($this);
    }

	public function getStore()
	{
		return array_key_exists("store_id", getallheaders()) ? getallheaders()["store_id"] : 0;
	}
 }
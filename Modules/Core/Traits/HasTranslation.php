<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Modules\Attribute\Entities\AttributeTranslation;

trait HasTranslation 
{
    protected $translationStoreID;

    public $translationRelationFn;

    public function getAttribute($name)
    {
        $translation = $this->getTranslateData();
        if($translation) 
        {
            array_map(function($attribute) use($translation) {
                parent::setAttribute($this->attributes["$attribute"], $translation->$attribute);
                return $this->$attribute = $translation->$attribute;
            }, $this->translatedAttributes);
        }        
        return parent::getAttribute($name);
    }

    public function getTranslateData()
    {
        $relation = AttributeTranslation::where("attribute_id", $this->attributes["id"])
        ->where('store_id', $this->getStoreId())->first();
        return $relation; 
    }

	public function getStoreId(): int
	{
		return array_key_exists("store_id", getallheaders()) ? (int) getallheaders()["store_id"] : 0;
	}
































 }
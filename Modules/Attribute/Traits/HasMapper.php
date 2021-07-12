<?php

namespace Modules\Attribute\Traits;


trait HasMapper
{
    public $mapper; 

    public function getMapperData(): void
    {
        $this->mapper = config('mapper');
    }

    public function getConfigOption(): ?object
    {
        $fetched = null;
            
        if($this->checkOption())
        {
            $configSlug = $this->mapper[$this->slug];
            $model = new $configSlug["module"];
            $fetched =  $model->select($configSlug["pluck"][0], $configSlug["pluck"][1])->get();
        }
        return $fetched;
    }

    public function checkMapper(): bool
    {
        $this->getMapperData();
        return in_array($this->slug, array_keys($this->mapper)); 
    }

    public function checkOption(): bool
    {
        if($this->checkMapper()){
            return ($this->mapper[$this->slug]["options"] == 1);
        }
        return false;
    }

    public function checkCreateOrUpdate(): bool
    {
        if($this->checkMapper()){
            return ($this->mapper[$this->slug]["create-update"] == 1);
        }
        return false;
    }

    public static function attributeMapper(): array
    {
        $attribute_mapper_ids = []; 
        foreach ( (new self)->attributeMapperSlug as $map)
        {
            $attribute = (new self)::whereSlug($map)->first();
            $attribute_mapper_ids[$attribute->slug] = $attribute->id;
        }

        return $attribute_mapper_ids;
    }

    public function getMapperModule()
    {
        return $this->mapper[$this->slug]["module"];
    }

    public function getMapperField()
    {
        return $this->mapper[$this->slug]["field"];
    }

    public function getMapperAttribute()
    {
        return $this->mapper[$this->slug]["attribute"];
    }
}

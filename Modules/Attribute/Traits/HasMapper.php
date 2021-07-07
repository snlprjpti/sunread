<?php

namespace Modules\Attribute\Traits;

use Exception;

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

}

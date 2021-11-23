<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\CacheManagement;

class CacheManagementRepository extends BaseRepository
{
    public function __construct(CacheManagement $cacheManagement)
    {
        $this->model = $cacheManagement;
        $this->model_key = "Cache Management";
    }
}

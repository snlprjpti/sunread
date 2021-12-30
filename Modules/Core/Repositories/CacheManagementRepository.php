<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\CacheManagement;
use Exception;

class CacheManagementRepository extends BaseRepository
{
    public function __construct(CacheManagement $cacheManagement)
    {
        $this->model = $cacheManagement;
        $this->model_key = "core.cache_management";
    }

    public function clearCustomCache(object $request): bool
    {
        try
        {
            $request->validate([
                "ids" => "array|required",
                "ids.*" => "required|exists:cache,id",
            ]);

            foreach ($request->ids as $id) {
                $fetch = $this->fetch($id);
                if (count(Redis::keys("{$fetch->key}*")) > 0) Redis::del(Redis::keys("{$fetch->key}*"));
            }
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }
}

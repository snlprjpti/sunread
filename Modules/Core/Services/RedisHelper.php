<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Redis;

class RedisHelper {

    /**
     * Store Redis Cache
     */
    public function storeCache(string $key, object $data)
    {
        try {

            Redis::SETNX($key, $data);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Check if Redis Key Exists
     */
    public function checkIfRedisKeyExists(string $key)
    {
        try {

            return Redis::exists($key);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get Redis Data
     */
    public function getRedisData(string $key)
    {
        try {

            return json_decode(Redis::get($key));

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Delete Redis Cache through key
     */
    public function deleteCache(string $key)
    {
        try {

            if(Redis::keys($key))
            {
                Redis::del(Redis::keys($key));
            }

        } catch (Exception $exception) {
            throw $exception;
        }

    }
}

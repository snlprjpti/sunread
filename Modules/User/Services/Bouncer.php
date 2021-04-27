<?php

namespace Modules\User\Services;

class  Bouncer
{
    /**
     * Checks if admin is allowed or not for certain action
     *
     * @param  String $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->role->permission_type == 'all') {
            return true;
        } else {
            if (! auth()->guard('admin')->check() || ! auth()->guard('admin')->user()->hasPermission($permission))
                return false;
        }

        return true;
    }

    /**
     * Checks if admin is  allowed or not for certain action
     *
     * @param $route
     * @return bool
     */
    public static function allow($route)
    {
        if (!auth()->guard('admin')->check())
            return false;
        $acl = config('acl');
        $key_for_route = array_search($route, array_column($acl, 'route'),true);
        if($key_for_route === false){
            return  false;
        }
        $permission = $acl[$key_for_route]['key'];

        if(auth()->guard('admin')->user()->hasPermission($permission))
        {
            return true;
        }

//
        $keys = explode('.', $permission);

        $value = '';
        for ($i = 0; $i < (count($keys)-1); $i++) {
            $value .= $keys[$i] . '.';
            $index = $value . 'all';
            if (auth()->guard('admin')->user()->hasPermission($index)) {
                return true;
            }
        }
        return false;
    }
}

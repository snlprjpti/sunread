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
     * @param  String $permission
     * @return bool
     */
    public static function allow($permission)
    {
        if (!auth()->guard('admin')->check())
            return false;
        if( auth()->guard('admin')->user()->hasPermission($permission))
            return true;
        return false;
    }
}

<?php


namespace Tests;

use Modules\User\Entities\Admin;

trait WithStubUser
{

    public function deleteStubUser()
    {
        $this->user->forceDelete();
    }


}

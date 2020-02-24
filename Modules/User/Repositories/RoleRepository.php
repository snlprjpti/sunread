<?php

namespace Modules\User\Repositories;

use App\Core\BaseModule\BaseRepository;
use Modules\User\Entities\Role;
use Modules\User\Repositories\Contracts\RoleRepositoryInterface;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{

    /**
     * CategoryRepository constructor.
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        parent::__construct($role);
        $this->model = $role;
    }

}
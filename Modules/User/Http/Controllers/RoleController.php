<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;

use Illuminate\Support\Facades\Validator;
use Modules\User\Repositories\RoleRepository;


/**
 * Admin user role controller
 */
class RoleController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * RoleRepository object
     *
     * @var array
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Modules\User\Repositories\RoleRepository $roleRepository
     * @return void
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }


    public function index()
    {
        $roles = $this->roleRepository->all();
        return $roles;

    }

    public function show($id)
    {
        return $this->roleRepository->findOneOrFail($id);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request(), [
            'name' => 'required',
            'permission_type' => 'required',
        ]);
        dd($validator);
        $role = $this->roleRepository->create(request()->all());
        return response()->json($role, 201);
    }


    public function edit($id)
    {
        return $this->roleRepository->findOrFail($id);

    }

    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required',
            'permission_type' => 'required',
        ]);
        $role = $this->roleRepository->find($id);
        $this->roleRepository = new RoleRepository($role);
        $this->roleRepository->update(request()->all());
        return response()->json($role, 200);

    }

    public function destroy($id)
    {
        $role = $this->roleRepository->findOrFail($id);
        dd($role);
    }

}
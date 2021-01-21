<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\CustomerAddress;
use Modules\Customer\Entities\CustomerGroup;

/**
 * CustomerAddress controller for the Admin
 */
class CustomerGroupController extends BaseController
{

    protected $pagination_limit;

    /**
     * CustomerAddressController constructor.
     * Admin middleware checks the admin against admins table
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns all the customer groups
     * @param $customer_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $customer_groups = CustomerGroup::all();
            return $this->successResponse($customer_groups, trans('core::app.response.fetch-list-success', ['name' => 'Customer Group']));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Get the particular customer group
     * @param $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($group_id)
    {
        try {
            $customer_address = CustomerGroup::findOrFail($group_id);
            return $this->successResponse($customer_address, trans('core::app.response.fetch-success', ['name' => 'Customer Group']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponseForMissingModel($exception);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Stores new customer address
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|min:2|max:100',
                'slug' => 'required|unique:customer_groups,slug',
            ]);
            $request->merge([
                'is_user_defined' =>  1
            ]);
            $customer_groups = CustomerGroup::create($request->only('name', 'slug','is_user_defined'));
            return $this->successResponse($customer_groups, trans('core::app.response.create-success', ['name' => 'Customer Group']), 201);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponseForMissingModel($exception);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Updates the customer address
     * @param Request $request
     * @param $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $group_id)
    {
        try {
            $customer_group = CustomerGroup::findOrFail($group_id);
            $this->validate($request,
                [
                    'name' => 'required|min:2|max:100',
                    'slug' => 'required|unique:customer_groups,slug,'.$group_id,
                ]);
            $customer_group->fill($request->only('name', 'slug'));
            $customer_group->save();
            return $this->successResponse($customer_group, trans('core::app.response.update-success', ['name' => 'Customer Group']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponseForMissingModel($exception);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Destroys the particular customer address
     * @param $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($group_id)
    {
        try {
            $address = CustomerGroup::findOrFail($group_id);
            $address->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Customer Group']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponseForMissingModel($exception);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}

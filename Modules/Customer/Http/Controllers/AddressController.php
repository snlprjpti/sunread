<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;


/**
 * CustomerAddress controller for the Admin
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft Pvt Ltd
 */
class AddressController extends BaseController
{
    protected $pagination_limit;

    /**
     * CustomerAddressController constructor.
     * Admin middleware checks the admin against admins table
     */

    public function __construct()
    {
        $this->middleware('admin');
        parent::__construct();
    }

    /**
     * Returns all the roles
     * @param $customer_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($customer_id)
    {
        try {
            $customer = Customer::findorFail($customer_id);
            $customer_address = $customer->addresses;
            return $this->successResponse($customer_address, trans('core::app.response.fetch-list-success', ['name' => 'Customer Address']));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Get the particular role
     * @param $customer_id
     * @param $address_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($customer_id,$address_id)
    {
        try {
            $customer_address = CustomerAddress::with('customer')->findOrFail($address_id);
            return $this->successResponse($customer_address);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Stores new role
     * @param Request $request
     * @param $customer_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $customer_id)
    {
        try {
            $customer = Customer::findOrFail($customer_id);
            $this->validate($request,
                [
                    'address1' => 'required|min:2|max:500',
                    'address2' => 'sometimes',
                    'country' => 'required',
                    'state' => 'required',
                    'city' => 'required',
                    'postcode' => 'required',
                    'phone' => 'required',
                    'default_address' => 'required'
                ]);
            $request->merge([
               'customer_id' => $customer_id
            ]);
            $customer_address = CustomerAddress::create($request->only('customer_id','address1' ,'address2','country','state','city','postcode','phone','default_address'));
            return $this->successResponse($customer_address, trans('core::app.response.create-success', ['name' => 'Customer Address']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(),404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


    /**
     * Updates the role details
     * @param Request $request
     * @param $customer_id
     * @param $address_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $customer_id ,$address_id)
    {
        try {
            $customer = Customer::findOrFail($customer_id);
            $customer_address = CustomerAddress::findOrFail($address_id);
            $this->validate($request,
                [
                    'address1' => 'required|min:2|max:500',
                    'address2' => 'sometimes',
                    'country' => 'required',
                    'state' => 'required',
                    'city' => 'required',
                    'postcode' => 'required',
                    'phone' => 'required',
                    'default_address' => 'required'
                ]);
            $customer_address->fill($request->only('customer_id', 'address1', 'address2', 'country', 'state', 'city', 'postcode', 'phone', 'default_address'));
            $customer_address->save();
            return $this->successResponse($customer_address, trans('core::app.response.update-success', ['name' => 'Customer Address']));

        }catch (ModelNotFoundException $exception) {
            return $this->errorResponseForMissingModel($exception);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Destroys the particular role
     * @param $customer_id
     * @param $address_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($customer_id,$address_id)
    {
        try {
            $customer = Customer::findOrFail($customer_id);
            $address = CustomerAddress::findOrFail($address_id);
            $address->delete();
            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Customer Address']));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse($exception->getMessage(), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

}

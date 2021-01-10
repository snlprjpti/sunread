<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Controllers\BaseController;
use Modules\Customer\Emails\NewCustomerNotification;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerGroup;

class CustomerController extends BaseController
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Fetch all the customers
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';

            $customers = Customer::query();
            if ($request->has('q')) {
                $customers->whereLike(Customer::$SEARCHABLE, $request->get('q'));
            }
            $customers->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit') ? $request->get('limit') : $this->pagination_limit;
            $customers = $customers->paginate($limit);
            return $this->successResponse($payload = $customers);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {

            DB::beginTransaction();
            $this->validate(request(), [
                'first_name' => 'string|required',
                'last_name' => 'string|required',
                'gender' => 'required||in:male,female',
                'email' => 'required|email|unique:customers,email',
                'date_of_birth' => 'date|before:today',
                'password' => 'required|min:6|max:100|confirmed',
                'status' => 'required|boolean',
                'customer_group_id' => 'sometimes|exists:customer_groups,id',
                'subscribed_to_news_letter' => 'sometimes|boolean'
            ]);

            Event::dispatch('customer.registration.before');

            //Hashing password
            $password = $request->get('password');
            $request->merge([
               'password' => bcrypt($password)
            ]);


            //Assigning customer groups
            $customer_group = CustomerGroup::where('customer_group_id' ,$request->get('customer_group_id'))->first();
            if(is_null($customer_group))
                $customer_group = CustomerGroup::where('slug' ,'guest')->first();
            $request->merge([
                'customer_group_id' => $customer_group->id
            ]);

            $customer = Customer::create(
                $request->only(['first_name', 'last_name', 'gender', 'email', 'date_of_birth', 'status', 'password', 'customer_group_id', 'subscribed_to_news_letter'])
            );

            Event::dispatch('customer.registration.after', $customer);

            try {
                Mail::queue(new NewCustomerNotification($customer, $password));
            } catch (\Exception $e) {
                report($e);
            }
            DB::commit();

            return $this->successResponse($customer, trans('core::app.response.create-success', ['name' => 'Customer']));

        } catch (ValidationException $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Get the particular customer
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $category = Customer::with('group')->findOrFail($id);
            return $this->successResponse($category);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Customer']), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $this->validate(request(), [
                'first_name' => 'sometimes|string|required',
                'last_name' => 'sometimes|string|required',
                'gender' => 'sometimes|required|in:male,female',
                'email' => 'sometimes|required|unique:customers,email,' . $id,
                'date_of_birth' => 'sometimes|date|before:today',
                'customer_group_id' => 'sometimes|exists:customer_groups,id',
                'subscribed_to_news_letter' => 'sometimes|boolean',
                'status' => 'sometimes|boolean',
                'password' => 'sometimes|min:6|max:12|confirmed',
            ]);

            Event::dispatch('customer.update.before');

            $customer = Customer::findOrFail($id);

            //Hashing password

            if($password = $request->get('password')){
                $request->merge([
                    'password' => bcrypt($password)
                ]);
            }


            //Assigning customer groups
            $customer_group = CustomerGroup::where('customer_group_id' ,$request->get('customer_group_id'))->first();
            if(is_null($customer_group))
                $customer_group = CustomerGroup::where('slug' ,'guest')->first();
            $request->merge([
                'customer_group_id' => $customer_group->id
            ]);


            $customer = $customer->fill(
                $request->only(['first_name', 'last_name', 'gender', 'email', 'date_of_birth', 'status', 'password', 'customer_group_id', 'subscribed_to_news_letter'])
            );

            $customer->save();

            Event::dispatch('customer.update.after', $customer);

            DB::commit();
            return $this->successResponse($customer, trans('core::app.response.update-success', ['name' => 'Customer']), 200);

        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Category']), 404);

        } catch (ValidationException $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->errors(), 400);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse($exception->getMessage());
        }


    }

    /**
     * Remove the specified resource from storage.
     * @param  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $category = Customer::findOrFail($id);
            $category->delete();

            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Customer']));
        }catch (ModelNotFoundException $exception){
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Category']), 404);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


}

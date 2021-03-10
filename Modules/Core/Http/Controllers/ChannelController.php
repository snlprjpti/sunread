<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Repositories\ChannelRepository;
use Modules\Core\Transformers\ChannelResource;


class ChannelController extends BaseController
{
    /**
     * Contains model name for
     *
     * @var array
     */
    protected $model_name = 'Channel';

    /**
     * ChannelRepository object
     *
     * @var ChannelRepository
     */
    protected $channelRepository;

    /**
     * Create a new controller instance.
     *
     * @param  ChannelRepository  $channelRepository
     * @return void
     */
    public function __construct(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }
    /**
     * Returns all the roles
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {

            $this->validate($request, [
                'limit' => 'sometimes|numeric',
                'page' => 'sometimes|numeric',
                'sort_by' => 'sometimes',
                'sort_order' => 'sometimes|in:asc,desc',
                'q' => 'sometimes|string|min:1'
            ]);
            $channels =  $this->channelRepository->index($request);

            return $this->successResponse(ChannelResource::collection($channels), trans('core::app.response.fetch-list-success', ['name' => $this->model_name]));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validate(request(), [
                /* general */
                'code' => ['required', 'unique:channels,code'],
                'name' => 'required',
                'description' => 'nullable',
                'hostname' => 'unique:channels,hostname',

                /* currencies and locales */
                'locales' => 'required|array|min:1|exists:locales,id',
                'default_locale_id' => 'required|in_array:locales.*|exists:locales,id',
                'currencies' => 'required|array|min:1|exists:currencies,id',
                'base_currency_id' => 'required|in_array:currencies.*|exists:currencies,id',

                'logo' => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
                'favicon' => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
            ]);

            Event::dispatch('core.channel.create.before');
            $channel = $this->channelRepository->create($data);
            Event::dispatch('core.channel.create.after', $channel);

            return $this->successResponse(new ChannelResource($channel), trans('core::app.response.create-success', ['name' => 'Channel']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try{
            dd($this->channelRepository);
            $channel = $this->channelRepository->with(['locales', 'currencies'])->findOrFail($id);
            return $this->successResponse(new ChannelResource($channel), trans('core::app.response.fetch-success', ['name' => $this->model_name]));

        }catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        }catch (\Exception $exception){
            dd($exception);
            return  $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $id
     * @return JsonResponse
     */
    public function update($id)
    {
        try {
            $locale = request()->get('locale') ?: app()->getLocale();

            $data = $this->validate(request(), [
                'code' => ['required', 'unique:channels,code'],
                'name' => 'required',
                'description' => 'nullable',
                'hostname' => 'unique:channels,hostname',

                /* currencies and locales */
                'locales' => 'required|array|min:1|exists:locales,id',
                'default_locale_id' => 'required|in_array:locales.*|exists:locales,id',
                'currencies' => 'required|array|min:1|exists:currencies,id',
                'base_currency_id' => 'required|in_array:currencies.*|exists:currencies,id',

                'logo' => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
                'favicon' => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
            ]);


            Event::dispatch('core.channel.update.before', $id);
            $channel = $this->channelRepository->update($data, $id);
            Event::dispatch('core.channel.update.after', $channel);

            return $this->successResponse(new ChannelResource($channel), trans('core::app.response.create-success', ['name' => $this->model_name]), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $channel = $this->channelRepository->findOrFail($id);

        if ($channel->code == config('app.channel')) {
            return $this->errorResponse(trans('admin::app.response.last-delete-error',['name' => $this->model_name]));
        }

        try {
            Event::dispatch('core.channel.delete.before', $id);

            $this->channelRepository->delete($id);

            Event::dispatch('core.channel.delete.after', $id);

            return $this->errorResponse(trans('admin::app.response.delete-failed', ['name' => $this->model_name]));

        }catch (ModelNotFoundException $exception){
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch(\Exception $e) {
            return $this->errorResponse(trans('admin::app.response.delete-failed', ['name' => $this->model_name]));
        }
    }

}

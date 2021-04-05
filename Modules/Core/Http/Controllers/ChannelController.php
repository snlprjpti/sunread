<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Channel;
use Modules\Core\Transformers\ChannelResource;

class ChannelController extends BaseController
{
    public function __construct(Channel $channel)
    {
        $this->model = $channel;
        $this->model_name = "Channel";
        parent::__construct($this->model, $this->model_name);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try
        {
            $this->validateListFiltering($request);
            $channels = $this->getFilteredList($request);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(ChannelResource::collection($channels), $this->lang('fetch-list-success'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try
        {
            $data = $this->validateData($request);

            //Event::dispatch('core.channel.create.before');

            $channel = $this->model->create($data);

            // Sync relations
            $channel->locales()->sync($data['locales']);
            $channel->currencies()->sync($data['currencies']);

            // Upload files
            $channel->logo = $this->storeImage($request, 'logo', 'channel');
            $channel->favicon = $this->storeImage($request, 'favicon', 'channel');
            $channel->save();

            Event::dispatch('core.channel.create.after', $channel);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new ChannelResource($channel), $this->lang('create-success'), 201);
    }

    /**
     * Show the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try
        {
            $channel = $this->model->with(['locales', 'currencies'])->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new ChannelResource($channel), $this->lang('fetch-success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try
        {
            $data = $this->validateData($request, $id);
            $channel = $this->model->findOrFail($id);

            // Save basic data
            $channel = $channel->fill($request->only(['code', 'name', 'description', 'hostname', 'theme']));
            $channel->save();

            // Sync relations
            if ($request->has('locales')) $channel->locales()->sync($request->get('locales'));
            if ($request->has('currencies')) $channel->currencies()->sync($request->get('currencies'));

            // Upload Files
            foreach (['logo', 'favicon'] as $file_name) {
                if (!isset($data[$file_name])) continue;
                $channel->{$file_name} = $this->storeImage($request, $file_name, 'channel', $channel->{$file_name});
                $channel->save();
            }
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (QueryException $exception)
        {
            return $this->errorResponse($exception->getMessage(), 400);
        }
        catch(ValidationException $exception)
        {
            return $this->errorResponse($exception->errors(), 422);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponse(new ChannelResource($channel), $this->lang('update-success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try
        {
            $channel = $this->model->findOrFail($id);
            // Cannot delete if accessed via same channel
            if ($channel->code === config('app.channel')) return $this->errorResponse($this->lang('last-delete-error'));
            // Delete the channel
            $channel->delete($id);
        }
        catch (ModelNotFoundException $exception)
        {
            return $this->errorResponse($this->lang('not-found'), 404);
        }
        catch (\Exception $exception)
        {
            return $this->errorResponse($exception->getMessage());
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }

    /**
     * Custom Validation for Store/Update
     * 
     * @param Request $request
     * @param int $id
     * @return Array
     */
    private function validateData($request, $id=null)
    {
        $id = $id ? ",{$id}" : null;
        $sometimes = $id ? "sometimes" : "required";
        $mimes = "bmp,jpeg,jpg,png,webp";

        return $this->validate($request, [
            /* General */
            "code" => "required|unique:channels,code{$id}",
            "name" => "required",
            "description" => "required",
            "hostname" => "required|unique:channels,hostname{$id}",

            /* Currencies and Locales */
            "locales" => "required|array|min:1|exists:locales,id",
            "default_locale_id" => "required|in_array:locales.*|exists:locales,id",
            "currencies" => "required|array|min:1|exists:currencies,id",
            "base_currency_id" => "required|in_array:currencies.*|exists:currencies,id",

            /* Branding */
            "logo" => "{$sometimes}|mimes:{$mimes}",
            "favicon" => "{$sometimes}|mimes:{$mimes}",
            "theme" => "required|in:default"
        ]);
    }
}

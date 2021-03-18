<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Channel;
use Modules\Core\Transformers\ChannelResource;


class ChannelController extends BaseController
{
    /**
     * Contains model name for
     *
     * @var array
     */
    protected $model_name = 'Channel';
    private $folder = 'channel';
    protected $folder_path;

    public function __construct()
    {
        parent::__construct();
        $this->folder_path = storage_path('app/public/images/') . $this->folder . DIRECTORY_SEPARATOR;;
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
            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';
            $channels = Channel::query();
            if ($request->has('q')) {
                $channels->whereLike(Channel::$SEARCHABLE, $request->get('q'));
            }
            $channels = $channels->orderBy($sort_by, $sort_order);
            $limit = $request->get('limit') ? $request->get('limit') : 25;
            $channels = $channels->paginate($limit);
            return $this->successResponse(ChannelResource::collection($channels), trans('core::app.response.fetch-list-success', ['name' => $this->model_name]));

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validate($request, [
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

            //Event::dispatch('core.channel.create.before');
            $channel = Channel::create($data);

            $channel->locales()->sync($data['locales']);
            $channel->currencies()->sync($data['currencies']);

            //upload logo image
            if (isset($data['logo'])) {
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalName();
                $channel->logo = $file->storeAs('images/channel', $filename, ['disk' => 'public']);
                $channel->save();
            }

            if (isset($data['favicon'])) {
                $file = $request->file('favicon');
                $filename = time() . '.' . $file->getClientOriginalName();
                $channel->favicon = $file->storeAs('images/channel', $filename, ['disk' => 'public']);
                $channel->save();
            }

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
        try {
            $channel = Channel::with(['locales', 'currencies'])->findOrFail($id);
            return $this->successResponse(new ChannelResource($channel), trans('core::app.response.fetch-success', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * @param array $data
     * @param Channel $channel
     * @param string $type
     * @return string
     */
    public function uploadImages(array $data, Channel $channel, $type = "logo")
    {

        if (isset($data[$type]) && $data[$type] instanceof UploadedFile) {
            if (isset($channel->{$type})) {
                Storage::disk('public')->delete($channel->{$type});
            }
            $file = $data[$type];
            $filename = time() . '.' . $file->getClientOriginalName();
            $channel->{$type} = $file->storeAs('images/channel', $filename, ['disk' => 'public']);
            $channel->save();
        };
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
            $channel = Channel::findOrFail($id);

            $this->validate(request(), [
                'code' => 'sometimes|required|unique:channels,code,' . $id,
                'name' => 'sometimes|required',
                'description' => 'sometimes|required',
                'hostname' => 'sometimes|required|unique:channels,hostname',

                /* currencies and locales */
                'locales' => 'sometimes|required|array|min:1|exists:locales,id',
                'default_locale_id' => 'sometimes|required|in_array:locales.*|exists:locales,id',
                'currencies' => 'sometimes|required|array|min:1|exists:currencies,id',
                'base_currency_id' => 'sometimes|required|in_array:currencies.*|exists:currencies,id',

                'logo' => 'sometimes|required|mimes:bmp,jpeg,jpg,png,webp',
                'favicon' => 'sometimes|required|mimes:bmp,jpeg,jpg,png,webp',
            ]);

            $channel = $channel->fill($request->only(['code', 'name', 'description', 'theme', 'hostname']));
            $channel->save();

            if ($request->has('locales'))
                $channel->locales()->sync($request->only('locales'));

            if ($request->has('currencies'))
                $channel->locales()->sync($request->only('currencies'));

            //upload logo image
            if (isset($data['logo'])) {
                $channel->logo = $this->uploadImages($data, $channel);
                $channel->save();
            }

            //upload favicon image
            if (isset($data['favicon'])) {
                $channel->logo = $this->uploadImages($data, $channel, 'favicon');
                $channel->save();
            }

            return $this->successResponse(new ChannelResource($channel), trans('core::app.response.create-success', ['name' => $this->model_name]), 201);

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

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
     * @param  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {

            $channel = Channel::findOrFail($id);

            if ($channel->code == config('app.channel')) {
                return $this->errorResponse(trans('admin::app.response.last-delete-error', ['name' => $this->model_name]));
            }

            $channel = Channel::findOrFail($id);
            $channel->delete($id);
            return $this->errorResponse(trans('admin::app.response.deleted-failed', ['name' => $this->model_name]));

        } catch (ModelNotFoundException $exception) {
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => $this->model_name]), 404);

        } catch (\Exception $e) {
            return $this->errorResponse(trans('admin::app.response.delete-failed', ['name' => $this->model_name]));
        }
    }

}

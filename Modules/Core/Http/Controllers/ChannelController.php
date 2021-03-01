<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Repositories\ChannelRepository;

class ChannelController extends BaseController
{
    /**
     * Contains route related configuration.
     *
     * @var array
     */
    protected $_config;

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

            return $this->successResponse($channel, trans('core::app.response.create-success', ['name' => 'Channel']), 201);

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
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $channel = $this->channelRepository->with(['locales', 'currencies'])->findOrFail($id);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $locale = request()->get('locale') ?: app()->getLocale();

        $data = $this->validate(request(), [
            /* general */
            'code'                             => ['required', 'unique:channels,code,' . $id],
            $locale . '.name'                  => 'required',
            $locale . '.description'           => 'nullable',
            'inventory_sources'                => 'required|array|min:1',
            'root_category_id'                 => 'required',
            'hostname'                         => 'unique:channels,hostname,' . $id,

            /* currencies and locales */
            'locales'                          => 'required|array|min:1',
            'default_locale_id'                => 'required|in_array:locales.*',
            'currencies'                       => 'required|array|min:1',
            'base_currency_id'                 => 'required|in_array:currencies.*',

            /* design */
            'theme'                            => 'nullable',
            $locale . '.home_page_content'     => 'nullable',
            $locale . '.footer_content'        => 'nullable',
            'logo.*'                           => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
            'favicon.*'                        => 'nullable|mimes:bmp,jpeg,jpg,png,webp',

            /* seo */
            $locale . '.seo_title'             => 'nullable',
            $locale . '.seo_description'       => 'nullable',
            $locale . '.seo_keywords'          => 'nullable',

            /* maintenance mode */
            'is_maintenance_on'                => 'boolean',
            $locale . '.maintenance_mode_text' => 'nullable',
            'allowed_ips'                      => 'nullable'
        ]);

        $data = $this->setSEOContent($data, $locale);

        Event::dispatch('core.channel.update.before', $id);

        $channel = $this->channelRepository->update($data, $id);

        if ($channel->base_currency->code !== session()->get('currency')) {
            session()->put('currency', $channel->base_currency->code);
        }

        Event::dispatch('core.channel.update.after', $channel);

        session()->flash('success', trans('admin::app.settings.channels.update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $channel = $this->channelRepository->findOrFail($id);

        if ($channel->code == config('app.channel')) {
            session()->flash('error', trans('admin::app.settings.channels.last-delete-error'));
        } else {
            try {
                Event::dispatch('core.channel.delete.before', $id);

                $this->channelRepository->delete($id);

                Event::dispatch('core.channel.delete.after', $id);

                session()->flash('success', trans('admin::app.settings.channels.delete-success'));

                return response()->json(['message' => true], 200);
            } catch(\Exception $e) {
                session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Channel']));
            }
        }

        return response()->json(['message' => false], 400);
    }


    /**
     * Unset keys.
     *
     * @param  array  $keys
     * @return array
     */
    private function unsetKeys($data, $keys)
    {
        foreach ($keys as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}

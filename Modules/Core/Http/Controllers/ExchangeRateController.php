<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Modules\Core\Repositories\ExchangeRateRepository;
use Modules\Core\Repositories\CurrencyRepository;

class ExchangeRateController extends BaseController
{


    /**
     * ExchangeRateRepository instance
     *
     * @var ExchangeRateRepository
     */
    protected $exchangeRateRepository;

    /**
     * CurrencyRepository object
     *
     * @var CurrencyRepository
     */
    protected $currencyRepository;

    /**
     * Create a new controller instance.
     *
     * @param ExchangeRateRepository $exchangeRateRepository
     * @param CurrencyRepository $currencyRepository
     * @return void
     */
    public function __construct(
        ExchangeRateRepository $exchangeRateRepository,
        CurrencyRepository $currencyRepository
    )
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $currencies = $this->currencyRepository->with('CurrencyExchangeRate')->all();


    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'target_currency' => ['required', 'unique:currency_exchange_rates,target_currency'],
            'rate'            => 'required|numeric',
        ]);

        Event::dispatch('core.exchange_rate.create.before');

        $exchangeRate = $this->exchangeRateRepository->create(request()->all());

        Event::dispatch('core.exchange_rate.create.after', $exchangeRate);

        session()->flash('success', trans('admin::app.settings.exchange_rates.create-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $currencies = $this->currencyRepository->all();

        $exchangeRate = $this->exchangeRateRepository->findOrFail($id);

        return view($this->_config['view'], compact('currencies', 'exchangeRate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'target_currency' => ['required', 'unique:currency_exchange_rates,target_currency,' . $id],
            'rate'            => 'required|numeric',
        ]);

        Event::dispatch('core.exchange_rate.update.before', $id);

        $exchangeRate = $this->exchangeRateRepository->update(request()->all(), $id);

        Event::dispatch('core.exchange_rate.update.after', $exchangeRate);

        session()->flash('success', trans('admin::app.settings.exchange_rates.update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Update Rates Using Exchange Rates API
     *
     * @param  string  $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRates($service)
    {
        $exchangeService = config('services.exchange-api')[$service];

        if (is_array($exchangeService)) {
            if (! array_key_exists('class', $exchangeService)) {
                return response()->json([
                    'success' => false,
                    'rates'   => null,
                    'error'   => trans('admin::app.exchange-rate.exchange-class-not-found', [
                        'service' => $service,
                    ]),
                ], 400);
            }

            $exchangeServiceInstance = new $exchangeService['class'];
            $updatedRates = $exchangeServiceInstance->fetchRates();

            return response()->json([
                'success' => true,
                'rates'   => 'rates',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'rates'   => null,
                'error'   => trans('admin::app.exchange-rate.invalid-config'),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $exchangeRate = $this->exchangeRateRepository->findOrFail($id);

        if ($this->exchangeRateRepository->count() == 1) {
            session()->flash('error', trans('admin::app.settings.exchange_rates.last-delete-error'));
        } else {
            try {
                Event::dispatch('core.exchange_rate.delete.before', $id);

                $this->exchangeRateRepository->delete($id);

                session()->flash('success', trans('admin::app.settings.exchange_rates.delete-success'));

                Event::dispatch('core.exchange_rate.delete.after', $id);

                return response()->json(['message' => true], 200);
            } catch (\Exception $e) {
                report($e);

                session()->flash('error', trans('admin::app.response.delete-error', ['name' => 'Exchange rate']));
            }
        }

        return response()->json(['message' => false], 400);
    }
}

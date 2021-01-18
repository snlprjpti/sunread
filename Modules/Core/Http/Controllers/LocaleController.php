<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Core\Repositories\LocaleRepository;


class LocaleController extends BaseController
{

    /**
     * LocaleRepository object
     *
     * @var \Modules\Core\Repositories\LocaleRepository
     */
    protected $localeRepository;

    /**
     * Create a new controller instance.
     *
     * @param LocaleRepository $localeRepository
     */
    public function __construct(LocaleRepository $localeRepository)
    {
        $this->middleware('admin');
        $this->localeRepository = $localeRepository;
        parent::__construct();
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {

            // validating data.
            $this->validate($request, [
                'limit' => 'sometimes|numeric',
                'page' => 'sometimes|numeric',
                'sort_by' => 'sometimes',
                'sort_order' => 'sometimes|in:asc,desc',
                'q' => 'sometimes|string|min:1'
            ]);

            $sort_by = $request->get('sort_by') ? $request->get('sort_by') : 'id';
            $sort_order = $request->get('sort_order') ? $request->get('sort_order') : 'desc';
            $limit = $request->get('limit')? $request->get('limit'):$this->pagination_limit;


            $locale_model = $this->localeRepository->makeModel();
            $locales = $locale_model->query();

            if ($request->has('q')) {
                $locales->whereLike($locale_model->SEARCHABLE, $request->get('q'));
            }

            $locales->orderBy($sort_by, $sort_order);
            $locales = $locales->paginate($limit);

            return $this->successResponse($locales, trans('core::app.response.fetch-list-success', ['name' => 'Locale']));

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (QueryException $exception) {
            return $this->errorResponse($exception->getMessage(), 400);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     * @return JsonResponse
     */
    public function store()
    {
        try {
            Event::dispatch('core.locale.create.before');

            $this->validate(request(), [
                'code' => ['required', 'unique:locales,code'],
                'name' => 'required',
            ]);

            $locale = $this->localeRepository->create(request()->all());

            Event::dispatch('core.locale.create.after', $locale);

            return $this->successResponse($locale, trans('core::app.response.create-success', ['name' => 'Locale']), 201);

        } catch (ValidationException $exception) {
            return $this->errorResponse($exception->errors(), 422);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {

            $locale = $this->localeRepository->findOrFail($id);
            return $this->successResponse($locale, trans('core::app.response.fetch-success', ['name' => 'Locale']));

        }catch (ModelNotFoundException $exception){
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Locale']), 404);

        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function update($id)
    {
        try {
            $this->validate(request(), [
                'code' => ['required', 'unique:locales,code,' . $id],
                'name' => 'required',
                'direction' => 'in:ltr,rtl',
            ]);

            Event::dispatch('core.locale.update.before', $id);

            $this->localeRepository->update(request()->all(), $id);

            Event::dispatch('core.locale.update.after');

            return $this->successResponseWithMessage(trans('core::app.response.update-success', ['name' => 'Locale']));

        }catch (ModelNotFoundException $exception){
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Locale']), 404);

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
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {

        try {

            $locale = $this->localeRepository->findOrFail($id);

            if ($this->localeRepository->count() == 1) {
                return $this->errorResponse(trans('core::app.settings.locales.last-delete-error'), 400);
            }

            Event::dispatch('core.locale.delete.before', $id);

            $this->localeRepository->delete($id);

            Event::dispatch('core.locale.delete.after', $id);

            return $this->successResponseWithMessage(trans('core::app.response.deleted-success', ['name' => 'Locale']));

        }catch (ModelNotFoundException $exception){
            return $this->errorResponse(trans('core::app.response.not-found', ['name' => 'Locale']), 404);

        } catch (\Exception $e) {
            return $this->errorResponse(trans('core::app.response.delete-failed', ['name' => 'Locale']));
        }
    }


}

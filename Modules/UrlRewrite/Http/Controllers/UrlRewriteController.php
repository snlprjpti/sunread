<?php

namespace Modules\UrlRewrite\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Route;
use Modules\Core\Http\Controllers\BaseController;
use Modules\UrlRewrite\Entities\UrlRewrite;
use Modules\UrlRewrite\Repositories\UrlRewriteRepository;
use Modules\UrlRewrite\Transformers\UrlRewriteResource;

class UrlRewriteController extends BaseController
{
    protected $repository;

    public function __construct(UrlRewrite $urlRewrite, UrlRewriteRepository $urlRewriteRepository)
    {
        $this->model = $urlRewrite;
        $this->model_name = "UrlRewrite";
        $this->repository = $urlRewriteRepository;
        parent::__construct($this->model, $this->model_name);    
    }

    public function collection(object $data): ResourceCollection
    {
        return UrlRewriteResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new UrlRewriteResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        dd(Route::hasMacro("rewrites"));
        try
        {
            $this->validateListFiltering($request);
            $fetched = $this->getFilteredList($request);    
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }
        
        return $this->successResponse($this->collection($fetched), $this->lang("fetch-list-success"));
    }

    public function store(Request $request)
    {
        dd($request->all());
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('urlrewrite::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('urlrewrite::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}

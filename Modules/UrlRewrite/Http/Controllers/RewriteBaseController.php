<?php

namespace Modules\UrlRewrite\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Controller;
use Modules\UrlRewrite\Contracts\UrlRewriteInterface;
use Modules\UrlRewrite\Repositories\UrlRewriteRepository;

class RewriteBaseController
{
    /** @var UrlRewriteInterface */
    protected $repository;

    public function __construct(UrlRewriteInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke($url): object
    {
        if (! $urlRewrite = $this->repository->getByRequestPath($url)) abort(404);
        if ($urlRewrite->isForward()) return $this->forwardResponse($urlRewrite->target_path);
        return redirect($urlRewrite->target_path, $urlRewrite->getRedirectType());
    }

    protected function forwardResponse($url): Response
    {
        return Route::dispatch(Request::create('/'.ltrim($url, '/'), request()->getMethod()));
    } 
}

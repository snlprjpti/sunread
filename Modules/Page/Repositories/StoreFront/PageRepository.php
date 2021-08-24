<?php

namespace Modules\Page\Repositories\StoreFront;

use Exception;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;
use Modules\Page\Entities\Page;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Website;
use Modules\Page\Entities\PageScope;

class PageRepository extends BaseRepository
{
    public function __construct(Page $page)
    {
        $this->model = $page;
    }

    public function findPage(object $request, string $slug): object
    {
        try
        {      
            $website = Website::whereHostname($request->header("hc-host"))->firstOrFail();
            $store = Store::whereCode($request->header("hc-store"))->firstOrFail();

            $page = $this->model->with("page_attributes")->whereWebsiteId($website->id)->whereSlug($slug)->firstOrFail();
            $page_scope = $page->page_scopes()->whereScope("store");
            $all_scope = (clone $page_scope)->whereScopeId(0)->first();
            if(!$all_scope) $page_scope->whereScopeId($store->id)->firstOrFail();
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $page;
    }
}

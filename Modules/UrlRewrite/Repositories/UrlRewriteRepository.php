<?php

namespace Modules\UrlRewrite\Repositories;

use Modules\UrlRewrite\Entities\UrlRewrite;
use Modules\Core\Repositories\BaseRepository;

class UrlRewriteRepository extends BaseRepository
{
    public function __construct(UrlRewrite $urlRewrite)
    {
		$this->model = $urlRewrite;
		$this->model_key = "UrlRewrites";
		$this->rules = [
			"request_path" => "required",
			"entity_controller" => "required",
			"entity_method" => "required",
			"entity_id" => "required|exist:products,id"
		];
    }

}

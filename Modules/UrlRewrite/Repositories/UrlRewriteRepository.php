<?php

namespace Modules\UrlRewrite\Repositories;

use Modules\UrlRewrite\Entities\UrlRewrite;
use Modules\Core\Repositories\BaseRepository;
use Modules\UrlRewrite\Contracts\UrlRewriteInterface;

class UrlRewriteRepository implements UrlRewriteInterface
{

	public function getByRequestPath(string $url): ?object
    {
        return $this->model->where('request_path', $url)->first();
    }

	public function getModel(): object
    {
        return $this->model;
    }

    public function setModel(object $model): object
    {
        $this->model = $model;

        return $this;
    }

}

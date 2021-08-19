<?php

namespace Modules\Category\Repositories\StoreFront;

use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Traits\HasScope;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Repositories\ResolveRepository;

class CategoryRepository extends ResolveRepository
{
    use HasScope;

    protected $repository, $config_fields;
    protected bool $without_pagination = true;

    public function __construct(Category $category, CategoryValue $categoryValue)
    {
        $this->model = $category;
        $this->createModel();
    }

    public function getData($request)
    {
        $website = $this->resolveWebsiteUpdate($request);
        $categories = $this->model->whereWebsiteId($website->id)->whereHas("category_values"); 
    }
}


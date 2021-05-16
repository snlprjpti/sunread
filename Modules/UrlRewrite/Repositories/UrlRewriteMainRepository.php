<?php

namespace Modules\UrlRewrite\Repositories;

use Exception;
use Modules\Core\Repositories\BaseRepository;
use Modules\UrlRewrite\Rules\UrlRewriteRule;
use Modules\UrlRewrite\Entities\UrlRewrite;

class UrlRewriteMainRepository extends BaseRepository
{
    protected $repository;

    public function __construct(UrlRewrite $url_rewrite)
    {
        $this->model = $url_rewrite;
        $this->model_key = "url_rewrites";
        $this->rules = [
            "type" => "required",
            "type_attributes" =>"required|array",
            "request_path" => "required",
            "target_path" => "required",
            "redirect_type" => "sometimes",
        ];
    }

    public function geturlRewriteData(array $item, ?int $id = null): array
    {
        $urlRewrite = [];
        $model_path = config("url-rewrite.path.".$item['type']);
        $model = new $model_path();

        switch ($item['type']) {
            case "Product":
                $urlRewrite['type_attributes']["parameter"]["product"] = $item['parameter_id'];
                break;

            case "Category":
                $urlRewrite['type_attributes']["parameter"]["category"] = $item['parameter_id'];
                break;
        }

        $urlRewrite['type'] = $model->urlRewriteType;
        $urlRewrite['request_path'] = $item['request_path'];

        if($item['store_id']) $urlRewrite['type_attributes']["extra_fields"]["store_id"] = $item['store_id'];

        if($this->UrlRewriteExists($urlRewrite, $id)) throw new Exception("Already Exists");
        
        $urlRewrite['target_path'] = route($model->urlRewriteRoute,  $urlRewrite['type_attributes']["parameter"], false);

        return $urlRewrite;
    }

    public function ValidateUrlRewrite(object $request): array
    {
        $data = $request->validate([
            "type" => "required|in:Product,Category",
            "parameter_id" => [ "required", new UrlRewriteRule($request->type) ],
            "store_id" => "nullable|exists:stores,id",
            "request_path" => "required" 
        ]);
        return $data;
    }

    public function UrlRewriteExists(array $urlRewrite, int $id = null)
    {
        $exist_data_query = $this->model->getByTypeAndAttributes($urlRewrite['type'], $urlRewrite['type_attributes']);
        if(isset($id)) $exist_data_query = $exist_data_query->where('id', '!=', $id);
        return (boolean) $exist_data_query->first();
    }
}

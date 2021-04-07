<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\FileManager;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Traits\ApiResponseFormat;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BaseController extends Controller
{
    use ApiResponseFormat, ValidatesRequests, FileManager;

    protected $pagination_limit, $locale, $model, $model_name, $lang;

    public function __construct($model, $model_name)
    {
        $this->pagination_limit = 25;

        //TODO ::future handle this variable in static memory in core helper
        $this->locale = config("locales.lang", config("app.locale"));

        $this->model = $model;
        $this->model_name = $model_name;

        // Frequently Used Translations
        $name_array = ["name" => $this->model_name];
        $this->lang = [
            "fetch-list-success" => "response.fetch-list-success",
            "fetch-success" => "response.fetch-success",
            "create-success" => "response.create-success",
            "update-success" => "response.update-success",
            "delete-success" => "response.deleted-success",
            "delete-error" => "response.deleted-error",
            "last-delete-error" => "response.last-delete-error",
            "not-found" => "response.not-found",
            "login-error" => "users.users.login-error",
            "login-success" => "users.users.login-success",
            "logout-success" => "users.users.logout-success",
            "token-generation-problem" => "users.token.token-generation-problem",
        ];
    }

    /**
     * Validate list filtering query parameters
     * 
     * @param \Illuminate\Http\Request $request
     * @return Array
     */
    public function validateListFiltering($request)
    {
        $rules = [
            "limit" => "sometimes|numeric",
            "page" => "sometimes|numeric",
            "sort_by" => "sometimes",
            "sort_order" => "sometimes|in:asc,desc",
            "q" => "sometimes|string|min:1"
        ];

        $messages = [
            "limit.numeric" => "Limit must be a number.",
            "page.numeric" => "Page must be a number.",
            "sort_order.in" => "Order must be 'asc' or 'desc'.",
            "q.string" => "Search query must be string.",
            "q.min" => "Search query must be at least 1 character.",
        ];

        return $this->validate($request, $rules, $messages);
    }

    /**
     * Filter the requested list with parameters
     * 
     * @param \Illuminate\Http\Request $request
     * @param Array $with
     * @return Object
     */
    public function getFilteredList($request, $with = [])
    {
        $sort_by = $request->sort_by ?? "id";
        $sort_order = $request->sort_order ?? "desc";
        $limit = $request->limit ?? $this->pagination_limit;

        $rows = $this->model::query();
        // Load relationships
        if ($with !== []) $rows->with($with);
        if ($request->has("q")) $rows->whereLike($this->model::$SEARCHABLE, $request->q);

        return $rows->orderBy($sort_by, $sort_order)->paginate($limit);
    }

    /**
     * Store given image to storage
     * 
     * @param \Illuminate\Http\Request $request
     * @param String $file
     * @param String $folder
     * @return Mixed
     */
    public function storeImage($request, $file_name, $folder = null, $delete_url = null)
    {
        // Check if file is given
        if ( $request->file($file_name) === null ) return false;

        try
        {
            // Store File
            $file = $request->file($file_name);
            $key = \Str::random(6);
            $folder = $folder ?? "default";
            $file_path = $file->storeAs("images/{$folder}/{$key}", $file->getClientOriginalName(), ["disk" => "public"]);

            // Delete old file if requested
            if ( $delete_url !== null ) Storage::disk("public")->delete($delete_url);
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }

        return $file_path;
    }

    /**
     * Returns translation
     * 
     * @param String $key
     * @param array|null $parameters
     * @param string $module
     * @return String
     */
    public function lang($key, $parameters = null, $module = "core::app")
    {
        $parameters = $parameters ?? ["name" => $this->model_name];
        $translation_key = $this->lang[$key];
        
        return __("{$module}.{$translation_key}", $parameters);
    }
}

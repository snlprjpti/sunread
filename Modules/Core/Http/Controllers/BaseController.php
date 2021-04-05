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
        $this->locale = config('locales.lang', config('app.locale'));

        $this->model = $model;
        $this->model_name = $model_name;

        // Frequently Used Translations
        $name_array = ['name' => $this->model_name];
        $this->lang = [
            "fetch-list-success" => __('core::app.response.fetch-list-success', $name_array),
            "fetch-success" => __('core::app.response.fetch-success', $name_array),
            "create-success" => __('core::app.response.create-success', $name_array),
            "update-success" => __('core::app.response.update-success', $name_array),
            "delete-success" => __('core::app.response.deleted-success', $name_array),
            "delete-error" => __('core::app.response.deleted-error', $name_array),
            "last-delete-error" => __('core::app.response.last-delete-error', $name_array),
            "not-found" => __('core::app.response.not-found', $name_array),
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
            'limit' => 'sometimes|numeric',
            'page' => 'sometimes|numeric',
            'sort_by' => 'sometimes',
            'sort_order' => 'sometimes|in:asc,desc',
            'q' => 'sometimes|string|min:1'
        ];

        $messages = [
            'limit.numeric' => 'Limit must be a number.',
            'page.numeric' => 'Page must be a number.',
            'sort_order.in' => 'Order must be "asc" or "desc".',
            'q.string' => 'Search query must be string.',
            'q.min' => 'Search query must be at least 1 character.',
        ];

        return $this->validate($request, $rules, $messages);
    }

    /**
     * Filter the requested list with parameters
     * 
     * @param \Illuminate\Http\Request $request
     * @return Object
     */
    public function getFilteredList($request)
    {
        $sort_by = $request->get('sort_by') ?? 'id';
        $sort_order = $request->get('sort_order') ?? 'desc';
        $limit = $request->get('limit') ?? $this->pagination_limit;

        $rows = $this->model::query();
        if ($request->has('q')) $rows->whereLike($this->model::$SEARCHABLE, $request->get('q'));
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
    public function storeImage($request, $file_name, $folder=null, $delete_url=null)
    {
        // Check if file is given
        if ( $request->file($file_name) === null ) return false;

        try
        {
            // Store File
            $file = $request->file($file_name);
            $key = \Str::random(6);
            $folder = $folder ?? "default";
            $file_path = $file->storeAs("images/{$folder}/{$key}", $file->getClientOriginalName(), ['disk' => 'public']);
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }

        // Delete old file if requested
        if ( $delete_url !== null ) Storage::disk('public')->delete($delete_url);

        return $file_path;
    }

    /**
     * Returns translation
     * 
     * @param String $key
     * @return String
     */
    public function lang($key)
    {
        return $this->lang[$key] ?? null;
    }
}

<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\FileManager;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Traits\ApiResponseFormat;
use Illuminate\Validation\ValidationException;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BaseController extends Controller
{
    use ApiResponseFormat, ValidatesRequests, FileManager;

    protected $pagination_limit, $locale, $model, $model_name, $lang, $exception_statuses;

    public function __construct($model, string $model_name, array $exception_statuses = [])
    {
        $this->pagination_limit = 25;

        //TODO ::future handle this variable in static memory in core helper
        $this->locale = config("locales.lang", config("app.locale"));

        $this->model = $model;
        $this->model_name = $model_name;

        // Frequently Used Translations
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

        // Frequently thrown excpetions
        $this->exception_statuses = array_merge([
            ValidationException::class => 422,
            ModelNotFoundException::class => 404,
            QueryException::class => 400,
            SlugCouldNotBeGenerated::class => 500
        ], $exception_statuses);
    }

    public function validateListFiltering(object $request): array
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

    public function getFilteredList(object $request, array $with = []): object
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

    public function storeImage(object $request, string $file_name, ?string $folder = null, ?string $delete_url = null): string
    {
        // Check if file is given
        if ( $request->file($file_name) === null ) return null;

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

    public function lang(string $key, ?array $parameters = null, string $module = "core::app"): string
    {
        $parameters = $parameters ?? ["name" => $this->model_name];
        $translation_key = $this->lang[$key] ?? $key;
        
        return __("{$module}.{$translation_key}", $parameters);
    }

    public function getExceptionStatus(object $exception): int
    {
        return $this->exception_statuses[get_class($exception)] ?? 500;
    }

    public function getExceptionMessage(object $exception): array
    {
        switch(get_class($exception))
        {
            case ValidationException::class :
                return $exception->errors();
            break;

            case ModelNotFoundException::class :
                return ["errors" => [$this->lang('not-found')]];
            break;

            default :
                return ["errors" => [$exception->getMessage()]];
            break;
        }
    }

    public function handleException(object $exception): JsonResponse
    {
        return $this->errorResponse($this->getExceptionMessage($exception), $this->getExceptionStatus($exception));
    }
}

<?php

namespace Modules\EmailTemplate\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\BaseController;
use Modules\EmailTemplate\Entities\EmailTemplate;
use Modules\EmailTemplate\Repositories\EmailTemplateRepository;
use Modules\EmailTemplate\Transformers\EmailTemplateResource;
use Exception;

class EmailTemplateController extends BaseController
{
    private $repository;

    public function __construct(EmailTemplate $emailTemplate, EmailTemplateRepository $emailTemplateRepository)
    {
        $this->model = $emailTemplate;
        $this->model_name = "Email Template";
        $this->repository = $emailTemplateRepository;
        parent::__construct($this->model, $this->model_name);
    }

    public function collection(object $data): ResourceCollection
    {
        return EmailTemplateResource::collection($data);
    }

    public function resource(object $data): JsonResource
    {
        return new EmailTemplateResource($data);
    }

    public function index(Request $request): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetchAll($request);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->collection($fetched), $this->lang('fetch-list-success'));
    }

    public function store(Request $request): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);

            $data["template_content"] = $this->repository->validateTemplateContent($data["template_content"]);

            $created = $this->repository->create($data);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($created), $this->lang('create-success'), 201);
    }

    public function show(int $id): JsonResponse
    {
        try
        {
            $fetched = $this->repository->fetch($id);
            $data = (json_decode($fetched->template_content, true));

            if (json_last_error() == JSON_ERROR_NONE) {
                $fetched->template_content = $this->repository->getTemplate($data);
            }
        }
        catch( Exception $exception )
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($fetched), $this->lang('fetch-success'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try
        {
            $data = $this->repository->validateData($request);

            $updated = $this->repository->update($data, $id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponse($this->resource($updated), $this->lang('update-success'));
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $this->repository->delete($id);
        }
        catch (Exception $exception)
        {
            return $this->handleException($exception);
        }

        return $this->successResponseWithMessage($this->lang('delete-success'));
    }
}

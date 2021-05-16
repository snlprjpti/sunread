<?php

namespace Modules\UrlRewrite\Repositories;

use Modules\UrlRewrite\Entities\UrlRewrite;
use Modules\UrlRewrite\Contracts\UrlRewriteInterface;
use Modules\UrlRewrite\Exceptions\UrlRewriteException;

class UrlRewriteRepository implements UrlRewriteInterface
{
    public const allowedTypes = [0, 1, 2];

    protected $model;

    public function __construct(UrlRewrite $model)
    {
        $this->model = $model;
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

    public function find(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function checkIfIdExists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    public function checkIfRequestPathExists(string $url): bool
    {
        return $this->model->where('request_path', $url)->exists();
    }

    public function getByRequestPath(string $url): ?object
    {
        return $this->model->where('request_path', $url)->first();
    }

    public function getByTypeAndAttributes(string $type, array $attributes): ?object
    {
        return $this->model->getByTypeAndAttributes($type, $attributes)->first();
    }

    public function getByTargetPath($url): ?object
    {
        return $this->model->where('target_path', $url)->first();
    }

    public function all(): ?object
    {
        return $this->model->all();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function regenerateRoute(string $requestPath, object $urlRewrite, object $model = null): object
    {
        if (! \is_array($urlRewrite->type_attributes)) throw UrlRewriteException::columnNotSet($urlRewrite, 'type_attributes');

        return $this->update([ 
            "target_path" => $this->targetPathFromRoute($model->urlRewriteRoute, $urlRewrite->type_attributes),
            "request_path" => $requestPath 
        ], $urlRewrite->id );
    }

    public function create(string $requestPath, ?string $targetPath, ?string $type = null, ?array $typeAttributes = null, ?int $redirectType = 0, bool $unique = false, ?object $model = null): object 
    {
        [$requestPath, $targetPath] = $this->validateCreate($requestPath, $targetPath, $type, $typeAttributes, $redirectType, $unique, $model);
        return $this->model->create([
                'type' => $type,
                'type_attributes' => $typeAttributes,
                'request_path' => $requestPath,
                'target_path' => $targetPath,
                'redirect_type' => $redirectType
            ]);
    }

    public function update(array $data, int $id): object
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    protected function generateUnique(string $requestPath, int $id = 1): string
    {
        if ($this->checkIfRequestPathExists($requestPath.'-'.$id)) return $this->generateUnique($requestPath, $id + 1);
        return $requestPath.'-'.$id;
    }

    protected function targetPathFromRoute($route, $attributes): string
    {
        return route($route, $attributes['parameter'], false);
    }

    protected function validateCreate(string $requestPath, ?string $targetPath, ?string $type, ?array $typeAttributes, int $redirectType, ?bool $unique, object $model): array 
    {
        if (! in_array($redirectType, self::allowedTypes, true)) throw new UrlRewriteException('Redirect type must be 0, 1 or 2');

        if ($this->checkIfRequestPathExists($requestPath)) {
            if (! $unique) throw UrlRewriteException::requestPath($requestPath);
            $requestPath = $this->generateUnique($requestPath);
        }

        if ($targetPath === null && isset($model, $typeAttributes)) {
            $targetPath = $this->targetPathFromRoute($model->urlRewriteRoute, $typeAttributes);
        }

        return [$requestPath, $targetPath];
    }

    public function handleUrlRewrite(object $model, string $event): void
    {
        if( $event == "created" ) $this->create($model->urlRewriteRequestPath, null, get_class($model), $model->getUrlRewriteAttributesArray(), 0, true, $model);
        if ( $event == "updated" ) $this->regenerateRoute($model->urlRewriteRequestPath, $model->getUrlRewrite(), $model);
        if( $event == "deleted" ) $this->delete($model->getUrlRewrite()->id);
    }

}

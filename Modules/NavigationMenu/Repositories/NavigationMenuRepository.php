<?php

namespace Modules\NavigationMenu\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Core\Facades\CoreCache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Modules\NavigationMenu\Traits\HasScope;
use Modules\Core\Repositories\BaseRepository;
use Modules\NavigationMenu\Rules\SlugUniqueRule;
use Modules\NavigationMenu\Entities\NavigationMenu;
use Modules\NavigationMenu\Entities\NavigationMenuValue;
use Modules\NavigationMenu\Rules\NavigationMenuLocationRule;
use Modules\NavigationMenu\Exceptions\NavigationMenuNotFoundException;

class NavigationMenuRepository extends BaseRepository
{
    // Properties for NavigationMenuRepostiory
    protected $repository;
    protected bool $without_pagination = true;

    /**
     * NavigationMenuRepostiory Class Constructor
     */
    public function __construct(NavigationMenu $navigation_menu)
    {
        $this->model = $navigation_menu;
        $this->model_key = "navigationMenu";

        $this->rules = [
            // NavigationMenu validation
            "title" => "required|string|min:2|max:250",
            "website_id" => 'required|integer|exists:websites,id',
            "location" => ["string", new NavigationMenuLocationRule()],
            "status" => "in:0,1"
        ];
    }

    /**
     * Create Navigation Menu with Unique Location
     */
    public function createWithUniqueLocation(array $data): object
    {
        if(isset($data['location'])) {
            $navigation_menu = $this->model->where('location', $data['location']);
            $navigation_menu->each(function ($item){
                $this->update(['location' => null], $item->id);
            });
        }
        $created = $this->create($data);
        return $created;
    }

    /**
     * Update Navigation Menu with Unique Location
     */
    public function updateWithUniqueLocation(array $data, int $id): object
    {
        if(isset($data['location'])) {
            $navigation_menu = $this->model->where('location', $data['location']);
            $navigation_menu->each(function ($item) use($id) {
                if($item->id !== $id) $this->update(['location' => null], $item->id);
            });
        }
        $updated = $this->update($data, $id);
        return $updated;
    }

    /**
     * Check if Slug Exists. If not create it
     */
    public function examineSlug(array $data, ?object $navigation_menu = null): array
    {
        if(!isset($data["slug"])) {
            $slug = Str::slug($data["title"]);
            $original_slug = $slug;
            $count = 1;

            while ($this->checkSlug($slug)) {
                $slug = "{$original_slug}-{$count}";
                $count++;
            }

            $data["slug"] = $slug;
        }
        return $data;
    }

    /**
     * Check if Slug Exists Or Not
     */
    public function checkSlug(string $slug)
    {
        $navigation_menu = $this->model->where('slug', $slug)->first();
        return $navigation_menu;
    }


    /**
     * Store Redis Cache
     */
    public function storeCache(string $key, object $data)
    {
        Redis::SETNX($key, $data);
    }

    /**
     * Check if Redis Key Exists
     */
    public function checkIfRedisKeyExists(string $key)
    {
        return Redis::exists($key);
    }

    /**
     * Delete Redis Cache through key
     */
    public function deleteCache(string $key)
    {
        if(Redis::exists($key))
        {
            Redis::del(Redis::keys($key));
        }
    }

}


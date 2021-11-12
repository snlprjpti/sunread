<?php

namespace Modules\NavigationMenu\Repositories;

use Exception;
use Illuminate\Support\Str;
use Modules\Core\Facades\CoreCache;
use Modules\NavigationMenu\Traits\HasScope;
use Illuminate\Support\Facades\Storage;
use Modules\NavigationMenu\Entities\NavigationMenu;
use Modules\NavigationMenu\Rules\SlugUniqueRule;
use Modules\Core\Repositories\BaseRepository;
use Modules\NavigationMenu\Entities\NavigationMenuValue;
use Modules\NavigationMenu\Exceptions\NavigationMenuNotFoundException;
use Modules\NavigationMenu\Rules\NavigationMenuLocationRule;

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
            "slug" => "string|min:2|max:250|unique:navigation_menus,slug",
            "location" => ["string", new NavigationMenuLocationRule()],
        ];
    }

    /**
     * Check if Slug Exists. If not create it
     */
    public function examineSlug(array $data, ?object $club_house = null): array
    {
        if(!isset($data["slug"])) {
            $slug = Str::slug($data["title"]);
            $original_slug = $slug;
            $count = 1;

            while ($this->checkSlug($slug, $club_house)) {
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
    public function checkSlug(string $slug, ?object $club_house = null)
    {
        $navigation_menu = $this->model->where('slug', $slug)->first();
        return $navigation_menu;
    }


    /**
     * Creates a Unique Slug for ClubHouse
     */
    // public function createUniqueSlug(array $data, ?object $club_house = null)
    // {
    //     $slug = is_null($club_house) ? Str::slug($data["items"]["title"]["value"]) : (isset($data["items"]["title"]["value"]) ? Str::slug($data["items"]["title"]["value"]) : $club_house->value([ "scope" => $data["scope"], "scope_id" => $data["scope_id"] ], "slug"));
    //     $original_slug = $slug;

    //     $count = 1;

    //     while ($this->checkSlug($data, $slug, $club_house)) {
    //         $slug = "{$original_slug}-{$count}";
    //         $count++;
    //     }
    //     return $slug;
    // }

    // public function fetchWithSlug(string $club_house_slug, $scope): object
    // {
    //     try
    //     {
    //         $club_house_value = ClubHouseValue::whereAttribute("slug")->whereValue($club_house_slug)->firstOrFail();
    //         $club_house = $club_house_value->clubHouse;

    //         if(!$this->checkStatus($club_house, $scope)) throw new ClubHouseNotFoundException();
    //     }

    //     catch(Exception $exception)
    //     {
    //         throw $exception;
    //     }

    //     return $club_house;
    // }

}


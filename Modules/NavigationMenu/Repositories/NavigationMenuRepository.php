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
            "location" => "required|string",
        ];
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

    // public function checkStatus(object $club_house, $scope): bool
    // {
    //     try
    //     {
    //         $data = [
    //             "scope" => "website",
    //             "scope_id" => $scope->id,
    //         ];
    //         $status_value = $club_house->value($data, "status");
    //     }
    //     catch (Exception $exception)
    //     {
    //         throw $exception;
    //     }

    //     return $status_value === 1 ? true : false;
    // }

}


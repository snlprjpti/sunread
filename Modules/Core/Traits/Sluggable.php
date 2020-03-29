<?php

namespace Modules\Core\Traits;


use Illuminate\Support\Str;
use Modules\Core\Exceptions\SlugCouldNotBeGenerated;

//generates slugs for repositories
trait Sluggable
{

    /**
     * @param $title
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function createSlug($title, $id = 0)
    {

        // Normalize the title attribute
        $slug = Str::slug($title);

        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.

        $allSlugs = self::getRelatedSlugs($slug, $id);

        // If we haven't used it before then we are all good.
        if (!$allSlugs->contains('slug', $slug)) {
            return $slug;
        }

        //if used,then count them
        $count = $allSlugs->count();


        while (self::checkIfSlugExist($slug, $id) && $slug != "") {
            $slug = $slug . '-' . $count++;
        }

        if ($slug == "")
            throw new SlugCouldNotBeGenerated();

        return $slug;
    }

    private function getRelatedSlugs($slug, $id = 0)
    {
        return $this->model()->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")
            ->where('id', '<>', $id)
            ->get();
    }

    private  function checkIfSlugExist($slug, $id = 0)
    {
        return $this->model()->select('slug')->where('slug', $slug)
            ->where('id', '<>', $id)
            ->exists();

    }

}

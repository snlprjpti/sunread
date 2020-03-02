<?php

namespace Modules\Core\Traits;


use Illuminate\Support\Str;
use SlugCouldNotBeGenerated;

trait SlugAble
{

    /**
     * @param $title
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public static function createSlug($title, $id = 0)
    {

        // Normalize the title attribute
        $slug = Str::slug($title);


        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.

        $allSlugs = self::getRelatedSlugs( $slug, $id);

        // If we haven't used it before then we are all good.
        if (!$allSlugs->contains('slug', $slug)) {
            return $slug;
        }

        //if used,then count them
        $count  = $allSlugs->count();

        //then append a number at end
        $newSlug =  false;
        while (self::checkIfSlugExist($slug, $id) ){
            $newSlug = $slug . '-' . $count++;
        }

        if((!$newSlug) || $newSlug == "")
            throw new SlugCouldNotBeGenerated();

        return $newSlug;
    }

    protected static function getRelatedSlugs($slug, $id = 0)
    {
        return static()->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")
            ->where('id', '<>', $id)
            ->get();
    }

    protected static function checkIfSlugExist($slug, $id = 0)
    {
        return  static()->select('slug')->where('slug', $slug)
            ->where('id', '<>', $id)
            ->exists();
    }


}
<?php

namespace Modules\Category\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait,Translatable;

    protected $fillable = ['position', 'status', 'parent_id','image', 'slug'];
    protected $with =['translations'];
    public $translatedAttributes = ['name', 'description', 'meta_title', 'meta_description', 'meta_keywords'];

    public static function rules($id=0,$merge = [])
    {
        return  array_merge([
            'slug' => 'required |unique:categories,slug'.($id ? ",$id" : ''),
            'name' => 'required',
            'image' => 'base64image',
        ],$merge);

    }


    /**
     * Get image url for the category image.
     */
    public function image_url()
    {
        if (! $this->image)
            return;

        return Storage::url($this->image);
    }

    /**
     * Get image url for the category image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }


    /**
     * Getting the root category of a category
     *
     * @return Category
     */
    public function getRootCategories(): Collection
    {
        return Category::where('parent_id', '=', null)->get();
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class,'category_id');
    }

    public function createTranslation($request,$category)
    {
        //format data according to locales
        $locale_values = $request->get('locales');
        if (isset($locale_values) && is_array($locale_values)) {
            foreach ($locale_values as $key => $item) {
                $data = array_merge($item,
                    [
                        'locale' => $key,
                        'category_id' => $category->id,
                    ]);
            }

            if (is_array($data)) {
                $category_translation = CategoryTranslation::create($data);
                $category->translations()->save($category_translation);
            }
        }

    }



}

<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kalnoy\Nestedset\NodeTrait;
use Modules\Core\Traits\Sluggable;

class Category extends Model
{
    use NodeTrait, Sluggable;

    public static $SEARCHABLE = [ "translations.name", "slug" ];
    protected $fillable = [ "position", "status", "parent_id", "image", "slug" ];
    protected $with = [ "translations" ];

    public static function rules($id=0,$merge = [])
    {
        return  array_merge([
            'slug' => 'nullable |unique:categories,slug'.($id ? ",$id" : ''),
            'name' => 'required',
            'image' => 'sometimes|mimes:jpeg,jpg,bmp,png',
            'position'=>'sometimes|numeric',
            'status'=> 'sometimes|boolean',

        ],$merge);

    }

    /**
     * Get image url for the category image.
     * 
     * @return string
     */
    public function image_url()
    {
        if (! $this->image)
            return;

        return Storage::url($this->image);
    }

    /**
     * Get image url for the category image.
     * 
     * @return string
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

    /**
     * Getting the translations
     * 
     * @return CategoryTranslation
     */
    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    /**
     * Getting parent category
     * 
     * @return Category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Specify category tree
     *
     * @param int $id
     * @return mixed
     */
    public function getCategoryTree()
    {
        return $this->id
            ? $this::orderBy('position', 'ASC')->where('id', '!=', $this->id)->get()->toTree()
            : $this::orderBy('position', 'ASC')->get()->toTree();
    }
}

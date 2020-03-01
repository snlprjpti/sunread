<?php

namespace Modules\Category\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kalnoy\Nestedset\NodeTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Category extends Model
{
    use Translatable,NodeTrait;
    public $translatedAttributes = ['name', 'description', 'slug', 'url_path', 'meta_title', 'meta_description', 'meta_keywords'];

    protected $fillable = ['position', 'status', 'parent_id'];
    protected  $with = ['translations'];

    public static function rules($id=0,$merge = [])
    {
        return  array_merge([
            'slug' => 'required |unique:category_translations,slug'.($id ? ",$id" : ''),
            'name' => 'required',
            'image' => 'mimes:jpeg,jpg,bmp,png',
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

    /**
     * Returns all categories within the category's path
     *
     * @return Category[]
     */
    public function getPathCategories(): array
    {
        $category = $this->findInTree();

        $categories = [$category];

        while (isset($category->parent)) {
            $category = $category->parent;
            $categories[] = $category;
        }

        return array_reverse($categories);
    }

    /**
     * Finds and returns the category within a nested category tree
     * will search in root category by default
     * is used to minimize the numbers of sql queries for it only uses the already cached tree
     *
     * @param Category[] $categoryTree
     * @return Category
     */
    public function findInTree($categoryTree = null): Category
    {
//        if (! $categoryTree) {
//            $categoryTree = app(CategoryRepository::class)->getVisibleCategoryTree($this->getRootCategory()->id);
//        }

        $category = $categoryTree->first();

        if (! $category) {
            throw new NotFoundHttpException('category not found in tree');
        }

        if ($category->id === $this->id) {
            return $category;
        }
        return $this->findInTree($category->children);
    }
}

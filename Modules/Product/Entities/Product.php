<?php

namespace Modules\Product\Entities;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeFamily;
use Modules\Category\Entities\Category;

class Product extends Model
{

    protected $fillable = ['type', 'attribute_family_id', 'sku', 'parent_id', 'slug'];
    protected $typeInstance;

    /**
     * Retrieve product attributes
     *
     *
     * @param bool  $skipSuperAttribute
     * @return Collection
     */
    public function getEditableAttributes($group = null, $skipSuperAttribute = true)
    {
        return $this->getTypeInstance()->getEditableAttributes($group, $skipSuperAttribute);
    }



    /**
     * Get the product variants that owns the product.
     */
    public function variants()
    {
        return $this->hasMany(static::class, 'parent_id');
    }



    /**
     * Get the product that owns the product.
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * The categories that belong to the product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    /**
     * Get the product attribute values that owns the product.
     */
    public function attribute_values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     *
     * Retrieve type instance
     * @param $type
     * @return AbstractType
     */
    public function getTypeInstance($type = null)
    {
        if ($this->typeInstance)
            return $this->typeInstance;

        if($type){
            $this->type = $type;
        }
        $this->typeInstance = app(config('product_types.' . $this->type . '.class'));

        $this->typeInstance->setProduct($this);

        return $this->typeInstance;
    }


    /**
     * Get the product attribute family that owns the product.
     */
    public function attribute_family()
    {
        return $this->belongsTo(AttributeFamily::class);
    }


    public function associatedAttributes()
    {
        return $this->attribute_family->custom_attributes()->get();
    }



    private static function fetchValidation($attribute,$id)
    {
        $validations = [];

        array_push($validations, $attribute->is_required ? 'required' : 'nullable');

        if ($attribute->validation) {
            array_push($validations, $attribute->validation);
        }

        if ($attribute->type == 'price')
            array_push($validations, 'decimal');

        if ($attribute->is_unique) {
            array_push($validations,'unique:'.$attribute->slug.($id ? ",$id" : ''));
        }
        return $validations;

    }

    /**
     * The images that belong to the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    /**
     * The super attributes that belong to the product.
     */
    public function super_attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_super_attributes');
    }


}

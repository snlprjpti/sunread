<?php

namespace Modules\Product\Entities;


use Illuminate\Database\Eloquent\Model;
use Modules\Attribute\Entities\AttributeFamily;
use Modules\Category\Entities\Category;

class Product extends Model
{

    protected $fillable = ['type', 'attribute_family_id', 'sku', 'parent_id', 'slug'];


    /**
     * Retrieve product attributes
     *
     * @param Group $group
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
     * Retrieve type instance
     *
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

    /**
     * @param int $id
     * @return array
     */
    public static function rules($id = 0)
    {

        $product = Product::findOrFail($id);

        //static validation
        $rules = array_merge($product->getTypeInstance()->getTypeValidationRules(), [
            'sku' => ['required', 'unique:products,sku' . ($id ? ",$id" : '')],
            //'images.*' => 'mimes:jpeg,jpg,bmp,png',
            'price' => 'required',
            'special_price_from' => 'nullable|date',
            'special_price_to' => 'nullable|date|after_or_equal:special_price_from',
            'special_price' => ['nullable', 'decimal']
        ]);

        //Dynamic validation based on attribute
        $custom_attributes = $product->getEditableAttributes();
        foreach ($custom_attributes as $attribute) {
            if ($attribute->slug == 'sku' || $attribute->type == 'boolean')
                continue;
            $validations = self::fetchValidation($attribute,$id);
            $rules[$attribute->slug] = $validations;
        }

        return $rules;
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


}

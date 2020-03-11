<?php

namespace Modules\Product\Type;

use Illuminate\Database\QueryException;
use Modules\Core\Traits\FileManager;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttributeValue;


/**
 * Abstract class Type
 *
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft co
 */
abstract class AbstractType
{

    use FileManager;

    protected $product,$folder_path;
    private $folder = 'product';

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->folder_path =  storage_path('app/public/images/'). $this->folder.DIRECTORY_SEPARATOR;

    }

    /**
     * @param array $data
     * @return Product
     */
    public function create(array $data)
    {

        return $this->product->create($data);
    }



    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return Product
     */
    public function update(array $data, $id)
    {

        try{
        $product = $this->product->findOrFail($id);

        //updating product-table
        $product->update($data);

        //Get all product attributes
        $associated_attributes = $product->associatedAttributes();



        foreach ($associated_attributes as $attribute) {
            if (! isset($data[$attribute->slug])) {
                continue;
            }
            $attribute_value = $this->fetchAttributeValue($data,$attribute);
            $productAttribute = ProductAttributeValue::firstOrNew([
                'product_id' => $product->id,
                'attribute_id' => $attribute->id,

            ]);

            $productAttribute->fill(
                [
                    'product_id' => $product->id,
                    'attribute_id' => $attribute->id,
                    ProductAttributeValue::$attributeTypeFields[$attribute->type] => $attribute_value
                ]
            );
            $productAttribute->save();
        }

        //insert product-categories
        if  (isset($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

      //  $product->related_products()->sync($data['related_products'] ?? []);

        return $product;
        }catch (\Exception $exception){
            dd($exception);
        }


    }

    /**
     * Specify type instance product
     *
     * @param  Product $product
     * @return AbstractType
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    public function priceRuleCanBeApplied()
    {
        return true;
    }
//
    /**
     * Return true if this product type is saleable
     *
     * @return boolean
     */
    public function isSaleable()
    {
        if (! $this->product->status) {
            return false;
        }

        return true;
    }

        /**
     * Retrieve product attributes
     *
     * @return Collection
     */
    public function getEditableAttributes()
    {
        return $this->product->attribute_family->custom_attributes()->get();
    }

    /**
     * Returns validation rules
     *
     * @return array
     */
    public function getTypeValidationRules()
    {
        return [];
    }

    private function fetchAttributeValue(array $data, $attribute)
    {


        if ($attribute->type == 'boolean') {
            return $data[$attribute->slug] = isset($data[$attribute->slug]) && $data[$attribute->slug] ? 1 : 0;
        }


        if ($attribute->type == 'price' && isset($data[$attribute->slug]) && $data[$attribute->slug] == '') {
            return $data[$attribute->slug] = null;
        }

        if ($attribute->type == 'date' && $data[$attribute->slug] == '') {
            return $data[$attribute->slug] = null;
        }

        if ($attribute->type == 'multiselect' || $attribute->type == 'checkbox') {
            return $data[$attribute->slug] = implode(",", $data[$attribute->slug]);
        }


        if ($attribute->type == 'image' || $attribute->type == 'file') {
            return $data[$attribute->slug] = gettype($data[$attribute->slug]) == 'object'
                ? $this->uploadFile(request()->file($attribute->slug),$this->folder_path)
                : NULL;
        }
        return $data[$attribute->slug];

    }

}
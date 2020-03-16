<?php

namespace Modules\Product\Type;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\FileManager;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttributeValue;
use Modules\Product\Repositories\ProductAttributeValueRepository;
use Modules\Product\Services\ProductImageRepository;


/**
 * Abstract class Type
 *
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft co
 */
abstract class AbstractType
{
    use FileManager;

    /**
     * Has child products aka variants
     *
     * @var boolean
     */

    protected $hasVariants = false;


    protected $product, $folder_path,$productImage,$attributeValueRepository;
    private $folder = 'product';

    public function __construct(Product $product,ProductImageRepository $productImage,ProductAttributeValueRepository $attributeValueRepository)
    {
        $this->product = $product;
        $this->folder_path = storage_path('app/public/images/') . $this->folder . DIRECTORY_SEPARATOR;
        $this->productImage =  $productImage;
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * @param array $data
     * @return Product
     */
    public function create(array $data):Product
    {
        try{

            DB::beginTransaction();
            $product = $this->product->create($data);
            DB::commit();
            return  $product;
        }catch (QueryException $exception){
            DB::rollBack();
            throw $exception;
        }

    }


    /**
     * @param array $data
     * @param $id
     * @return Product
     * @throws \Exception
     */
    public function update(array $data, $id):Product
    {

        try {
            DB::beginTransaction();

            //Get the product
            $product = $this->product->findOrFail($id);

            //updating product-table
            $product->update($data);

            //Update Attributes


            //we fetch only the associated attributes of particular product type
            $associated_attributes = $product->associatedAttributes();

            $this->updateAttributes($associated_attributes ,$product,$data);

            //updating product-categories data
            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }


            //Upload ProductImage
            $this->productImage->uploadProductImages($data, $product);

            //TODO::future => Update cross-sell, up-sells, related_products, inventories

            DB::commit();
            return $product;

        } catch (QueryException $exception) {
            DB::rollBack();
            throw $exception;

        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

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
                ? $this->uploadFile(request()->file($attribute->slug), $this->folder_path)
                : NULL;
        }
        return $data[$attribute->slug];

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


    /**
     * Return true if this product type is saleable
     *
     * @return boolean
     */
    public function isSaleable()
    {
        if (!$this->product->status) {
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

    private function updateAttributes($associated_attributes,$product ,$data)
    {

        //Updating each product-attribute table
        foreach ($associated_attributes as $attribute) {
            if (!isset($data[$attribute->slug])) {
                continue;
            }
            $attribute_value = $this->fetchAttributeValue($data, $attribute);

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
    }


    /**
     * Return true if this product can have variants
     *
     * @return bool
     */
    public function hasVariants()
    {
        return $this->hasVariants;
    }


}
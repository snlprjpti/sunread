<?php

namespace Modules\Product\Type;

use Modules\Attribute\Entities\Attribute;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttributeValue;
use Illuminate\Support\Str;
use Modules\Product\Repositories\ProductAttributeValueRepository;

/**
 * Class Configurable.
 *
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft co
 */

class Configurable extends AbstractType
{
    protected $product,$attribute_model,$product_attribute_model, $attributeValueRepository;

    public function __construct(Product $product,Attribute $attribute_model,ProductAttributeValueRepository $attributeValueRepository)
    {
        $this->product = $product;
        $this->attribute_model = $attribute_model;
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * Skip attribute for downloadable product type
     *
     * @var array
     */
    protected $skipAttributes = ['price', 'cost', 'special_price', 'special_price_from', 'special_price_to', 'width', 'height', 'depth', 'weight'];

    protected $hasVariants = true;

    /**
     * @param array $data
     * @return Product
     */
    public function create(array $data)
    {
        //create parent configurable product item
        $product = $this->product->create($data);

        //create super-attributes

        if (isset($data['super_attributes'])) {
            $super_attributes = [];

            foreach ($data['super_attributes'] as $attributeCode => $attributeOptions) {
                $attribute = Attribute::where('slug', $attributeCode)->first();
                $super_attributes[$attribute->id] = $attributeOptions;
                $product->super_attributes()->attach($attribute->id);
            }

            foreach (array_permutation($super_attributes) as $permutation) {

                $variants = isset($data['variants'])? $data['variants']:[];

                $this->createVariant($product, $permutation,$variants);
            }
        }

        return $product;
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return Product
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $product = parent::update($data, $id, $attribute);

        if (request()->route()->getName() != 'admin.catalog.products.massupdate') {
            $previousVariantIds = $product->variants->pluck('id');

            if (isset($data['variants'])) {
                foreach ($data['variants'] as $variantId => $variantData) {
                    if (Str::contains($variantId, 'variant_')) {
                        $permutation = [];

                        foreach ($product->super_attributes as $superAttribute) {
                            $permutation[$superAttribute->id] = $variantData[$superAttribute->slug];
                        }
                        $this->createVariant($product, $permutation, $variantData);
                    } else {
                        if (is_numeric($index = $previousVariantIds->search($variantId)))
                            $previousVariantIds->forget($index);

                        $this->updateVariant($variantData, $variantId);
                    }
                }
            }

            foreach ($previousVariantIds as $variantId) {
                $this->product->delete($variantId);
            }
        }

        return $product;
    }

    /**
     * @param mixed $product
     * @param array $permutation
     * @param array $data
     * @return mixed
     */
    public function createVariant($product, $permutation, $data = [])
    {
        //default data
        if (!count($data)) {
            $data = [
                "sku" => $product->sku . '-variant-' . implode('-', $permutation),
                "slug" => $product->slug . '-variant-' . implode('-', $permutation),
                "name" => "",
                "inventories" => [],
                "price" => 0,
                "weight" => 0,
                "status" => 1
            ];
        }

        $typeOfVariants = 'simple';

        //store variant
        $variant = Product::create([
            'parent_id' => $product->id,
            'type' => $typeOfVariants,
            'attribute_family_id' => $product->attribute_family_id,
            'sku' => $data['sku'],
            'slug' => $data['slug'],
        ]);

        //store attributes
        foreach (['sku', 'name', 'price', 'weight', 'status'] as $attributeCode) {
            $attribute = Attribute::where('slug', $attributeCode)->first();
            $this->attributeValueRepository->createProductAttribute(
                [
                    'product_id' => $variant->id,
                    'attribute_id' => $attribute->id,
                    'value' => $data[$attributeCode],
                    'slug' => $attributeCode
                ]
            );

        }

        //store permuted attributes(usually color and size)
        foreach ($permutation as $attributeId => $optionId) {
            $this->attributeValueRepository->createProductAttribute(
                [
                    'product_id' => $variant->id,
                    'attribute_id' => $attributeId,
                    'value' => $optionId
                ]);
        }

        return $variant;
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function updateVariant(array $data, $id)
    {
        $variant = $this->product->find($id);

        $variant->update(['sku' => $data['sku']]);

        foreach (['sku', 'name', 'price', 'weight', 'status'] as $attributeCode) {
            $attribute = $this->attributeValueRepository->where('slug', $attributeCode);

            $attributeValue = $this->attributeValueRepository->where([
                'product_id' => $id,
                'attribute_id' => $attribute->id,
            ]);

            if (!$attributeValue) {
                $this->attributeValueRepository->create([
                    'product_id' => $id,
                    'attribute_id' => $attribute->id,
                    'value' => $data[$attribute->slug],
                ]);
            } else {
                $this->attributeValueRepository->update([
                    ProductAttributeValue::$attributeTypeFields[$attribute->type] => $data[$attribute->slug]
                ], $attributeValue->id);
            }
        }

        return $variant;
    }

    /**
     * Returns children ids
     *
     * @return array
     */
    public function getChildrenIds()
    {
        return $this->product->variants()->pluck('id')->toArray();
    }

    public function getTypeValidationRules()
    {
        return [
            'variants.*.name' => 'required',
            'variants.*.sku' => 'required',
            'variants.*.price' => 'required',
            'variants.*.weight' => 'required',
        ];
    }



}
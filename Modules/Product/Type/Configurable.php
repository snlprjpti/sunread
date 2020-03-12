<?php

namespace Modules\Product\Type;

use Modules\Attribute\Entities\Attribute;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttributeValue;
use Modules\Product\Entities\ProductFlat;
use Illuminate\Support\Str;

/**
 * Class Configurable.
 *
 * @author    Hemant Achhami
 * @copyright 2020 Hazesoft co
 */

class Configurable extends AbstractType
{
    protected $product,$attribute_model;

    public function __construct(Product $product,Attribute $attribute_model)
    {
        $this->product = $product;
        $this->attribute_model = $attribute_model;
    }

    /**
     * Skip attribute for downloadable product type
     *
     * @var array
     */
    protected $skipAttributes = ['price', 'cost', 'special_price', 'special_price_from', 'special_price_to', 'width', 'height', 'depth', 'weight'];

    /**
     * These blade files will be included in product edit page
     *
     * @var array
     */
    protected $additionalViews = [
        'admin::catalog.products.accordians.images',
        'admin::catalog.products.accordians.categories',
        'admin::catalog.products.accordians.variations',
        'admin::catalog.products.accordians.channels',
        'admin::catalog.products.accordians.product-links'
    ];

    /**
     * Is a composite product type
     *
     * @var boolean
     */
    protected $isComposite = true;

    /**
     * Show quantity box
     *
     * @var boolean
     */
    protected $showQuantityBox = true;

    /**
     * Has child products aka variants
     *
     * @var boolean
     */
    protected $hasVariants = true;

    /**
     * @param array $data
     * @return Product
     */
    public function create(array $data)
    {

        $product = $this->product->create($data);

        if (isset($data['super_attributes'])) {
            $super_attributes = [];

            foreach ($data['super_attributes'] as $attributeCode => $attributeOptions) {
                $attribute = Attribute::where('slug', $attributeCode)->first();
                $super_attributes[$attribute->id] = $attributeOptions;
                $product->super_attributes()->attach($attribute->id);
            }

            foreach (array_permutation($super_attributes) as $permutation) {
                $this->createVariant($product, $permutation);
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
                            $permutation[$superAttribute->id] = $variantData[$superAttribute->code];
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
            ProductAttributeValue::create([
                'product_id' => $variant->id,
                'attribute_id' => $attribute->id,
                'value' => $data[$attributeCode]
            ]);
        }

        //store permutted attributes(usually color and size)
        foreach ($permutation as $attributeId => $optionId) {
            Attribute::create([
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
        $variant = $this->productRepository->find($id);

        $variant->update(['sku' => $data['sku']]);

        foreach (['sku', 'name', 'price', 'weight', 'status'] as $attributeCode) {
            $attribute = $this->attributeRepository->findOneByField('code', $attributeCode);

            $attributeValue = $this->attributeValueRepository->findOneWhere([
                'product_id' => $id,
                'attribute_id' => $attribute->id,
                'channel' => $attribute->value_per_channel ? $data['channel'] : null,
                'locale' => $attribute->value_per_locale ? $data['locale'] : null
            ]);

            if (!$attributeValue) {
                $this->attributeValueRepository->create([
                    'product_id' => $id,
                    'attribute_id' => $attribute->id,
                    'value' => $data[$attribute->code],
                    'channel' => $attribute->value_per_channel ? $data['channel'] : null,
                    'locale' => $attribute->value_per_locale ? $data['locale'] : null
                ]);
            } else {
                $this->attributeValueRepository->update([
                    ProductAttributeValue::$attributeTypeFields[$attribute->type] => $data[$attribute->code]
                ], $attributeValue->id);
            }
        }

        $this->productInventoryRepository->saveInventories($data, $variant);

        return $variant;
    }

    /**
     * @param array $data
     * @param mixed $product
     * @return mixed
     */
    public function checkVariantOptionAvailabiliy($data, $product)
    {
        $superAttributeCodes = $product->parent->super_attributes->pluck('code');

        foreach ($product->parent->variants as $variant) {
            if ($variant->id == $product->id)
                continue;

            $matchCount = 0;

            foreach ($superAttributeCodes as $attributeCode) {
                if (!isset($data[$attributeCode]))
                    return false;

                if ($data[$attributeCode] == $variant->{$attributeCode})
                    $matchCount++;
            }

            if ($matchCount == $superAttributeCodes->count())
                return true;
        }

        return false;
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

    /**
     * @param CartItem $cartItem
     * @return bool
     */
    public function isItemHaveQuantity($cartItem)
    {
        return $cartItem->child->product->getTypeInstance()->haveSufficientQuantity($cartItem->quantity);
    }

    /**
     * Returns validation rules
     *
     * @return array
     */
    public function getTypeValidationRules()
    {
        return [
            'variants.*.name' => 'required',
            'variants.*.sku' => 'required',
            'variants.*.price' => 'required',
            'variants.*.weight' => 'required',
        ];
    }

    /**
     * Return true if item can be moved to cart from wishlist
     *
     * @param Wishlist $item
     * @return boolean
     */
    public function canBeMovedFromWishlistToCart($item)
    {
        if (isset($item->additional['selected_configurable_option']))
            return true;

        return false;
    }




}
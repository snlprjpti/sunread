<?php

namespace Modules\Product\Listeners;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Entities\Locale;
use Modules\Product\Entities\ProductAttributeValue;
use Modules\Product\Entities\ProductFlat as ProductFlatModel;
use Modules\Product\Helpers\ProductType;


/**
 * Product Flat Event handler
 *
 */
class ProductFlat
{
    /**
     * @var object
     */
    public $attributeTypeFields = [
        'text' => 'text',
        'textarea' => 'text',
        'price' => 'float',
        'boolean' => 'boolean',
        'select' => 'integer',
        'multiselect' => 'text',
        'datetime' => 'datetime',
        'date' => 'date',
        'file' => 'text',
        'image' => 'text',
        'checkbox' => 'text'
    ];
    /**
     * AttributeRepository Repository Object
     *
     * @var object
     */
    protected $attributeRepository;
    /**
     * AttributeOptionRepository Repository Object
     *
     * @var object
     */
    protected $attributeOptionRepository;
    /**
     * ProductFlatRepository Repository Object
     *
     * @var object
     */
    protected $productFlatRepository;
    /**
     * ProductAttributeValueRepository Repository Object
     *
     * @var object
     */
    protected $productAttributeValueRepository;
    /**
     * Attribute Object
     *
     * @var object
     */
    protected $attribute;

    /**
     * After the attribute is created
     *
     * @return void
     */
    public function afterAttributeCreatedUpdated($attribute)
    {
        if (!$attribute->is_user_defined) {
            return false;
        }

        if (!$attribute->use_in_flat) {
            $this->afterAttributeDeleted($attribute->id);
            return false;
        }

        if (!Schema::hasColumn('product_flat', $attribute->code)) {
            Schema::table('product_flat', function (Blueprint $table) use ($attribute) {
                $table->{$this->attributeTypeFields[$attribute->type]}($attribute->slug)->nullable();

                if ($attribute->type == 'select' || $attribute->type == 'multiselect') {
                    $table->string($attribute->slug . '_label')->nullable();
                }
            });
        }
    }

    public function afterAttributeDeleted($attributeId)
    {
        $attribute = $this->attributeRepository->find($attributeId);

        if (Schema::hasColumn('product_flat', strtolower($attribute->slug))) {
            Schema::table('product_flat', function (Blueprint $table) use ($attribute) {
                $table->dropColumn($attribute->slug);

                if ($attribute->type == 'select' || $attribute->type == 'multiselect') {
                    $table->dropColumn($attribute->slug . '_label');
                }
            });
        }
    }

    /**
     * Creates product flat
     *
     * @param Product $product
     * @return void
     */
    public function afterProductCreatedUpdated($product)
    {
        $this->createFlat($product);

        if (ProductType::hasVariants($product->type)) {
            foreach ($product->variants()->get() as $variant) {
                $this->createFlat($variant, $product);
            }
        }
    }

    /**
     * Creates product flat
     *
     * @param Product $product
     * @param Product $parentProduct
     * @return void
     */
    public function createFlat($product, $parentProduct = null)
    {

        static $familyAttributes = [];

        static $superAttributes = [];

        $locale = config('locales.lang')? config('locales.lang'):config('app.locale');
        $locale = Locale::where('code', $locale)->first();

        $productFlat = ProductFlatModel::firstOrNew([
            'product_id' => $product->id,
            'locale' => $locale->code
        ]);



        if (!array_key_exists($product->attribute_family->id, $familyAttributes))
            $familyAttributes[$product->attribute_family->id] = $product->associatedAttributes();


        if ($parentProduct && !array_key_exists($parentProduct->id, $superAttributes))
            $superAttributes[$parentProduct->id] = $parentProduct->super_attributes()->pluck('slug')->toArray();


        foreach ($familyAttributes[$product->attribute_family->id] as $attribute) {

            if ($parentProduct && !in_array($attribute->slug, array_merge($superAttributes[$parentProduct->id], ['sku', 'name', 'price', 'weight', 'status'])))
                continue;

            if (!Schema::hasColumn('product_flat', $attribute->slug))
                continue;


            $productAttributeValue = $product->attribute_values()->where('attribute_id', $attribute->id)->first();
      

            $productFlat->{$attribute->slug} = isset($productAttributeValue)?$productAttributeValue[ProductAttributeValue::$attributeTypeFields[$attribute->type]] ?? null:null;

            if ($attribute->type == 'select') {
                $attributeOption = AttributeOption::find($product->{$attribute->slug});

                if ($attributeOption) {
                    if ($attributeOptionTranslation = $attributeOption->translate($locale->code)) {
                        $productFlat->{$attribute->slug . '_label'} = $attributeOptionTranslation->label;
                    } else {
                        $productFlat->{$attribute->slug . '_label'} = $attributeOption->name;
                    }
                }
            } elseif ($attribute->type == 'multiselect') {
                $attributeOptionIds = explode(',', $product->{$attribute->slug});

                if (count($attributeOptionIds)) {
                    $attributeOptions = $this->attributeOptionRepository->findWhereIn('id', $attributeOptionIds);

                    $optionLabels = [];

                    foreach ($attributeOptions as $attributeOption) {
                        if ($attributeOptionTranslation = $attributeOption->translate($locale->code)) {
                            $optionLabels[] = $attributeOptionTranslation->label;
                        } else {
                            $optionLabels[] = $attributeOption->name;
                        }
                    }

                    $productFlat->{$attribute->code . '_label'} = implode(', ', $optionLabels);
                }
            }
        }

        $productFlat->created_at = $product->created_at;

        $productFlat->updated_at = $product->updated_at;

        $productFlat->min_price = $product->min_price;

        $productFlat->max_price = $product->max_price;


        //upload image
        $thumbnail = $product->images->where('type', 'thumbnail')->first();
        if($thumbnail){
            $productFlat->thumbnail = $thumbnail;
        }

        if ($parentProduct) {
            $parentProductFlat = $this->productFlatRepository->where([
                'product_id' => $parentProduct->id,
            ]);

            if ($parentProductFlat)
                $productFlat->parent_id = $parentProductFlat->id;
        }
        $productFlat->slug = $product->slug;

        $productFlat->save();
    }


}
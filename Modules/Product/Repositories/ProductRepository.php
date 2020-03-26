<?php


namespace Modules\Product\Repositories;


use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Core\Eloquent\Repository;
use Modules\Product\Entities\Product;
use Modules\Product\Services\ProductImageRepository;


class ProductRepository extends Repository
{
    /**
     * AttributeRepository object
     */
    protected $attributeRepository,$productImageRepository;

    /**
     * Create a new repository instance.
     * @param AttributeRepository $attributeRepository
     * @param ProductImageRepository $productImageRepository
     * @param App $app
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        ProductImageRepository $productImageRepository,
        App $app
    )
    {
        $this->productImageRepository = $productImageRepository;
        $this->attributeRepository = $attributeRepository;
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Product::class;
    }


    public function store(array $data)
    {
        Event::dispatch('catalog.product.create.before');

        try {
            DB::beginTransaction();
            $typeInstance = app(config('product_types.' . $data['type'] . '.class'));
            $product = $typeInstance->create($data);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw  $exception;
        }

        Event::dispatch('catalog.product.create.after', $product);
        return $product;
    }


    /**
     * @param array $data
     * @param int $id
     * @return Product
     * @throws \Exception
     */
    public function update(array $data, $id)
    {
        Event::dispatch('catalog.product.update.before', $id);
        try {

            DB::beginTransaction();
            $product = $this->find($id);
            $product = $product->getTypeInstance()->update($data, $id);
            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw  $exception;
        }

        Event::dispatch('catalog.product.update.after', $product);
        return $product;
    }

    public function delete($id)
    {
        try {
            $product = $this->findOrFail($id);
            //removing image only
            $this->productImageRepository->removeProductImages($product);
            $product->delete($id);

        }catch (\Exception $exception){
            throw $exception;
        }

    }

    public function rules($id = 0,$merge = [])
    {

        $product = $this->findOrFail($id);

        //static validation
        $rules = array_merge($product->getTypeInstance()->getTypeValidationRules(), [
            'sku' => ['required', 'unique:products,sku' . ($id ? ",$id" : '')],
            'price' => 'required',
            'special_price_from' => 'nullable|date',
            'special_price_to' => 'nullable|date|after_or_equal:special_price_from',
            'special_price' => ['nullable', 'decimal'],
            'old_price' => ['nullable', 'decimal']
        ],$merge);


        //Dynamic validation based on attribute
        $custom_attributes = $product->getTypeInstance()->getEditableAttributes();

        foreach ($custom_attributes as $attribute) {
            if ($attribute->slug == 'sku' || $attribute->type == 'boolean')
                continue;
            $validations = self::fetchValidation($attribute,$id);
            $rules[$attribute->slug] = $validations;
        }

        return $rules;
    }


}

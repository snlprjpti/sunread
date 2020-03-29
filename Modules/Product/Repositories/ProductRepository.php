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

        try {
            DB::beginTransaction();

            Event::dispatch('catalog.product.create.before');

            $typeInstance = app(config('product_types.' . $data['type'] . '.class'));
            $product = $typeInstance->create($data);

            Event::dispatch('catalog.product.create.after', $product);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw  $exception;
        }

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
        try {
            DB::beginTransaction();

            Event::dispatch('catalog.product.update.before', $id);

            $product = $this->find($id);
            $product = $product->getTypeInstance()->update($data, $id);

            Event::dispatch('catalog.product.update.after', $product);

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            throw  $exception;
        }

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
            'slug' => ['required','unique:products,slug'. ($id ? ",$id" : '')],
            'price' => 'required',
            'special_price_from' => 'nullable|date',
            'special_price_to' => 'nullable|date|after_or_equal:special_prices_from',
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

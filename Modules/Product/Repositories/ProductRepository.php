<?php


namespace Modules\Product\Repositories;


use Illuminate\Container\Container as App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Core\Eloquent\Repository;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductAttributeValue;
use Modules\Product\Services\ProductImageRepository;
//use Webkul\Product\Models\ProductAttributeValueProxy;
//use Webkul\Product\Repositories\ProductFlatRepository;


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

    public function getAll($categoryId = null)
    {
        $params = request()->input();
        $perPage = isset($params['limit']) && ! empty($params['limit']) ? $params['limit'] : 9;


        $page = Paginator::resolveCurrentPage('page');

        $repository = app(ProductFlatRepository::class)->scopeQuery(function ($query) use ($params, $categoryId) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            $qb = $query->distinct()
                ->select('product_flat.*')
                ->join('product_flat as variants', 'product_flat.id', '=', DB::raw('COALESCE(' . DB::getTablePrefix() . 'variants.parent_id, ' . DB::getTablePrefix() . 'variants.id)'))
                ->leftJoin('product_categories', 'product_categories.product_id', '=', 'product_flat.product_id')
                ->leftJoin('product_attribute_values', 'product_attribute_values.product_id', '=', 'variants.product_id')
                ->where('product_flat.channel', $channel)
                ->where('product_flat.locale', $locale)
                ->whereNotNull('product_flat.slug');


            if ($categoryId) {
                $qb->where('product_categories.category_id', $categoryId);
            }

            if (is_null(request()->input('status'))) {
                $qb->where('product_flat.status', 1);
            }

            if (is_null(request()->input('visible_individually'))) {
                $qb->where('product_flat.visible_individually', 1);
            }

            if (isset($params['q'])) {
                $qb->where('product_flat.name', 'like', '%' . urldecode($params['q']) . '%');
            }

            /* added for api as per the documentation */
            if (isset($params['name'])) {
                $qb->where('product_flat.name', 'like', '%' . urldecode($params['name']) . '%');
            }

            /* added for api as per the documentation */
            if (isset($params['slug'])) {
                $qb->where('product_flat.slug', 'like', '%' . urldecode($params['slug']) . '%');
            }

            # sort direction
            $orderDirection = 'asc';
            if (isset($params['order']) && in_array($params['order'], ['desc', 'asc'])) {
                $orderDirection = $params['order'];
            } else {
                $sortOption = $this->getDefaultSortByOption();
            }

            if (isset($params['sort'])) {
                $this->checkSortAttributeAndGenerateQuery($qb, $params['sort'], $orderDirection);
            } else {
                $sortOption = $this->getDefaultSortByOption();
                if (! empty($sortOptions)) {
                    $this->checkSortAttributeAndGenerateQuery($qb, $sortOption, $orderDirection);
                }
            }

            if ($priceFilter = request('price')) {
                $priceRange = explode(',', $priceFilter);
                if (count($priceRange) > 0) {
                    $qb->where('variants.min_price', '>=', core()->convertToBasePrice($priceRange[0]));
                    $qb->where('variants.min_price', '<=', core()->convertToBasePrice(end($priceRange)));
                }
            }

            $attributeFilters = $this->attributeRepository->getProductDefaultAttributes(array_keys(request()->except(['price'])));

            if (count($attributeFilters) > 0) {
                $qb->where(function ($filterQuery) use ($attributeFilters) {

                    foreach ($attributeFilters as $attribute) {
                        $filterQuery->orWhere(function ($attributeQuery) use ($attribute) {

                            $column = DB::getTablePrefix() . 'product_attribute_values.' . ProductAttributeValue::$attributeTypeFields[$attribute->type];

                            $filterInputValues = explode(',', request()->get($attribute->code));

                            # define the attribute we are filtering
                            $attributeQuery = $attributeQuery->where('product_attribute_values.attribute_id', $attribute->id);

                            # apply the filter values to the correct column for this type of attribute.
                            if ($attribute->type != 'price') {

                                $attributeQuery->where(function ($attributeValueQuery) use ($column, $filterInputValues) {
                                    foreach ($filterInputValues as $filterValue) {
                                        if (! is_numeric($filterValue)) {
                                            continue;
                                        }
                                        $attributeValueQuery->orWhereRaw("find_in_set(?, {$column})", [$filterValue]);
                                    }
                                });

                            } else {
                                $attributeQuery->where($column, '>=', core()->convertToBasePrice(current($filterInputValues)))
                                    ->where($column, '<=', core()->convertToBasePrice(end($filterInputValues)));
                            }
                        });
                    }

                });

                # this is key! if a product has been filtered down to the same number of attributes that we filtered on,
                # we know that it has matched all of the requested filters.
                $qb->groupBy('variants.id');
                $qb->havingRaw('COUNT(*) = ' . count($attributeFilters));
            }

            return $qb->groupBy('product_flat.id');

        });

        # apply scope query so we can fetch the raw sql and perform a count
        $repository->applyScope();
        $countQuery = "select count(*) as aggregate from ({$repository->model->toSql()}) c";
        $count = collect(DB::select($countQuery, $repository->model->getBindings()))->pluck('aggregate')->first();

        if ($count > 0) {
            # apply a new scope query to limit results to one page
            $repository->scopeQuery(function ($query) use ($page, $perPage) {
                return $query->forPage($page, $perPage);
            });

            # manually build the paginator
            $items = $repository->get();
        } else {
            $items = [];
        }

        $results = new LengthAwarePaginator($items, $count, $perPage, $page, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);

        return $results;
    }

    private function getDefaultSortByOption()
    {
        return 'asc';
    }

    /**
     * Check sort attribute and generate query
     *
     * @param object $query
     * @param string $sort
     * @param string $direction
     *
     * @return object
     */
    private function checkSortAttributeAndGenerateQuery($query, $sort, $direction)
    {
        $attribute = $this->attributeRepository->findOneByField('code', $sort);
        dd($attribute);
        if ($attribute) {
            if ($attribute->code === 'price') {
                $query->orderBy('min_price', $direction);
            } else {
                $query->orderBy($attribute->code, $direction);
            }
        } else {
            /* `created_at` is not an attribute so it will be in else case */
            $query->orderBy('product_flat.created_at', $direction);
        }

        return $query;
    }




}

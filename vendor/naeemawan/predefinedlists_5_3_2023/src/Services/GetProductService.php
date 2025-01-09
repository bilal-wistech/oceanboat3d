<?php

namespace NaeemAwan\PredefinedLists\Services;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;
use EcommerceHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetProductService
{
    protected PredefinedListInterface $productRepository;

    public function __construct(PredefinedListInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProduct(
        Request $request,
        $category = null,
        $brand = null,
        array $with = [],
        array $withCount = [],
        array $conditions = []
    ): Collection|LengthAwarePaginator {
        $num = (int)$request->input('num');
        $shows = EcommerceHelper::getShowParams();

        if (! array_key_exists($num, $shows)) {
            $num = (int)theme_option('number_of_products_per_page', 12);
        }

        $queryVar = [
            'keyword' => $request->input('q'),
            'categories' => (array)$request->input('categories', []),
            'max_price' => $request->input('max_price'),
            'min_price' => $request->input('min_price'),
            'num' => $num,
        ];

        if ($category) {
            $queryVar['categories'] = array_merge($queryVar['categories'], [$category]);
        }

        $orderBy = [
            'predefined_list.created_at' => 'DESC',
        ];

        $params = [
            'paginate' => [
                'per_page' => $queryVar['num'],
                'current_paged' => (int)$request->query('page', 1),
            ],
            'with' => $with,
            'withCount' => $withCount,
        ];


        $params['condition'] = array_merge([
            'predefined_list.status' => 1,
            'predefined_list.parent_id' => 0,
        ], $conditions);

        $params=array_merge([
            'keyword' => $queryVar['keyword'],
            'min_price' => $queryVar['min_price'],
            'max_price' => $queryVar['max_price'],
            'categories' => $queryVar['categories'],
            'order_by' => $orderBy,
        ], $params);

        $products = $this->productRepository->filterProducts($params);
        
        if ($queryVar['keyword'] && is_string($queryVar['keyword'])) {
            $products->setCollection(BaseHelper::sortSearchResults($products->getCollection(), $queryVar['keyword'], 'name'));
        }
        return $products;
    }
}

<?php

namespace NaeemAwan\PredefinedLists\Http\Controllers;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Botble\Ecommerce\Events\ProductViewed;
use Botble\Ecommerce\Http\Resources\ProductVariationResource;
use Botble\Ecommerce\Models\Brand;
use NaeemAwan\PredefinedLists\Models\PredefinedList;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedCategoryInterface;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\PredefinedListInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductTagInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use NaeemAwan\PredefinedLists\Services\GetProductService;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Carbon\Carbon;
use EcommerceHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use ProductCategoryHelper;
use RvMedia;
use SeoHelper;
use SlugHelper;
use Theme;

class PublicProductController
{
    protected PredefinedListInterface $productRepository;

    protected PredefinedCategoryInterface $productCategoryRepository;

    public function __construct(
        PredefinedListInterface $productRepository,
        PredefinedCategoryInterface $productCategoryRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
    }

    public function getProducts(Request $request, GetProductService $productService, BaseHttpResponse $response)
    {
        if (! EcommerceHelper::productFilterParamsValidated($request)) {
            return $response->setNextUrl(route('public.customize-boat'));
        }

        $query = BaseHelper::stringify($request->input('q'));

        $with = [];

        if ($query && ! $request->ajax()) {
            $products = $productService->getProduct($request, null, null, $with);

            SeoHelper::setTitle(__('Search result for ":query"', compact('query')));

            Theme::breadcrumb()
                ->add(__('Home'), route('public.index'))
                ->add(__('Search'), route('public.customize-boat'));

            SeoHelper::meta()->setUrl(route('public.customize-boat'));

            return Theme::scope(
                'ecommerce.search',
                compact('products', 'query'),
                'plugins/ecommerce::themes.search'
            )->render();
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Build a Boat'), route('public.customize-boat'));

        if(isset($request->cat_id)){
            $products = $productService->getProduct($request, $request->cat_id, null, $with);
        }else{
            $products = $productService->getProduct($request, null, null, $with);
        }

        if ($request->ajax()) {
            return $this->ajaxFilterProductsResponse($products, $request, $response);
        }

        SeoHelper::setTitle(__('Build a boat'))->setDescription(__('Build a boat'));

        do_action(PRODUCT_MODULE_SCREEN_NAME);

        return Theme::scope(
            'ecommerce.boats',
            compact('products'),
            'plugins/ecommerce::themes.boats'
        )->render();
    }

    public function getProduct($id,Request $request)
    {
        $product = $this->productRepository->findOrFail($id);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Build a Boat'), route('public.customize-boat'))
            ->add($product->ltitle, route('public.customize-boat.id',$product->id));
            
        return Theme::scope('ecommerce.boat',compact('product'),'plugins/ecommerce::themes.boat')->render();
    }

    public function getTypeContent(Request $request){
        $category=PredefinedList::where('id',$request->value)->first();
        return $category;
    }

    protected function ajaxFilterProductsResponse($products, Request $request, BaseHttpResponse $response, ?ProductCategory $category = null)
    {
        $total = $products->total();
        $message = $total > 1 ? __(':total Products found', compact('total')) : __(
            ':total Product found',
            compact('total')
        );

        $view = Theme::getThemeNamespace('views.ecommerce.includes.boat-items');

        if (! view()->exists($view)) {
            $view = 'plugins/ecommerce::themes.includes.boat-items';
        }


        return $response
            ->setData(view($view, compact('products'))->render());
    }


}

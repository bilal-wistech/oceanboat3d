<?php

namespace NaeemAwan\PredefinedLists\Http\Controllers;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Botble\Base\Events\AdminNotificationEvent;
use Botble\Base\Supports\AdminNotificationItem;
use Botble\Ecommerce\Events\ProductViewed;
use Botble\Ecommerce\Http\Resources\ProductVariationResource;
use Botble\Ecommerce\Models\Brand;
use NaeemAwan\PredefinedLists\Models\BoatDiscount;
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
use EmailHandler;
use SlugHelper;
use Theme;
use OrderHelper;
use NaeemAwan\PredefinedLists\Models\BoatEnquiry;
use NaeemAwan\PredefinedLists\Models\BoatEnquiryDetail;
use App\Models\BoatView;


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
        if (!EcommerceHelper::productFilterParamsValidated($request)) {
            return $response->setNextUrl(route('public.customize-boat'));
        }

        $query = BaseHelper::stringify($request->input('q'));

        $with = [];

        if ($query && !$request->ajax()) {
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

        if (isset($request->cat_id)) {
            $products = $productService->getProduct($request, $request->cat_id, null, $with);
        } else {
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

    public function getProduct($id, Request $request)
    {
        $product = $this->productRepository->getByWhereIn('type', [$id]);
        $product = $product[0];
        $product_cats = $product->childitems_display();
        $accessories = [];
        foreach ($product_cats as $key => $product_cat) {
            foreach ($product_cat->childitems() as $product_sub_cat) {
                foreach ($product_sub_cat->childitems() as $product_sub_sub_cat) {
                    $accessories[] = $product_sub_sub_cat;
                }
            }
        }
        if ($product->count() > 0) {
            $this->addViewCount($product->id, 'boat');
            Theme::breadcrumb()
                ->add(__('Home'), route('public.index'))
                ->add(__('Build a Boat'), route('public.customize-boat'))
                ->add($product->type, route('public.customize-boat.id', $product->id));
            return Theme::scope('ecommerce.boat', compact('product', 'accessories'), 'plugins/ecommerce::themes.boat')->render();
        }
    }
    public function addViewCount($id, $type)
    {
        if (empty($id))
            return response()->json(['message' => 'Please provide ID ', 'id' => ''], 400);
        ;
        $boatView = BoatView::firstOrNew(['entity_id' => $id, 'entity_type' => $type]);
        // Increment the total_count
        $boatView->total_count++;

        // Save the record
        $boatView->save();
        return response()->json(['message' => 'View added successfully', 'id' => $id], 200);
    }

    public function getTypeContent(Request $request)
    {
        $category = PredefinedList::where('id', $request->value)->first();
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

        if (!view()->exists($view)) {
            $view = 'plugins/ecommerce::themes.includes.boat-items';
        }


        return $response
            ->setData(view($view, compact('products'))->render());
    }


    public function SubmitBoat(Request $request)
    {
        //        dd($request);
        $request->validate([
            "boat_id" => "required",
            // "total_price"  => "gt:0",
            'option' => "required|array"
        ]);
        cache()->put('boat_data', $request->all(), 60 * 60);
        if (auth('customer')->check()) {
            return redirect()->route('customer.overview');
        } else {
            return redirect()->route('customer.register');
        }
    }
    public function applyDiscount(Request $request, BaseHttpResponse $response)
    {
        $code = $request->input('code');
        $accessoryId = $request->input('accessory_id');

        // Store the received values in the session
        $totalPrice = $request->input('total_price');
        session(['total_price' => $totalPrice]);

        // Check if the accessory is selected
        $selectedOptions = $request->input('selected_options', []);
        if (!in_array($accessoryId, $selectedOptions)) {
            return $response->setError()->setMessage('Accessory not selected.');
        }

        // Find the discount in the database
        $discount = BoatDiscount::where('code', $code)
            ->where('list_id', $accessoryId)
            ->orWhere('accessory_id',$accessoryId)
            ->where(function ($query) {
                $query->where('valid_to', '>=', now())
                    ->orWhere('never_expires', 1);
            })
            ->where('valid_from', '<=', now())
            ->first();

        if (!$discount) {
            return $response->setError()->setMessage('Invalid or expired discount code.');
        }

        // Get the product price from the discount object
        $pdl_list_price = $discount->list->price;

        // Calculate the discount amount based on product price
        $discountAmount = 0;
        if ($discount->discount_type === 'amount') {
            $discountAmount = $discount->discount;
        } elseif ($discount->discount_type === 'percentage') {
            $discountAmount = ($pdl_list_price * $discount->discount) / 100;
        }

        // Apply the discount to the total price
        $newTotalPrice = $totalPrice - $discountAmount;

        // Update the session with the new total price
        session(['total_price' => $newTotalPrice]);

        // Store or update the applied discount information in the session
        $appliedDiscounts = session('applied_discounts', []);
        $appliedDiscounts[$accessoryId] = [
            'code' => $discount->code,
            'amount' => $discountAmount,
        ];
        session(['applied_discounts' => $appliedDiscounts]);

        return $response->setMessage('Discount applied successfully.')
            ->setData([
                'new_total' => $newTotalPrice,
                'discount_amount' => $discountAmount,
            ]);
    }
}

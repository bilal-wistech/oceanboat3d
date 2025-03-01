@if ($product)
    <div class="product-cart-wrap mb-30">
        <div class="product-img-action-wrap">
            <div class="product-img product-img-zoom">
                <a href="{{ $product->url }}">
                    <img class="default-img" src="{{ RvMedia::getImageUrl($product->image, 'product-thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}">
                    <img class="hover-img" src="{{ RvMedia::getImageUrl($product->images[1] ?? $product->image, 'product-thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}">
                </a>
            </div>
            <!-- maryam -->
            <!-- <div class="product-action-1">
                <a aria-label="{{ __('Quick View') }}" href="#" class="action-btn hover-up js-quick-view-button" data-url="{{ route('public.ajax.quick-view', $product->id) }}"><i class="far fa-eye"></i></a>
                @if (EcommerceHelper::isWishlistEnabled())
                    <a aria-label="{{ __('Add To Wishlist') }}" href="#" class="action-btn hover-up js-add-to-wishlist-button" data-url="{{ route('public.wishlist.add', $product->id) }}"><i class="far fa-heart"></i></a>
                @endif
                @if (EcommerceHelper::isCompareEnabled())
                    <a aria-label="{{ __('Add To Compare') }}" href="#" class="action-btn hover-up js-add-to-compare-button" data-url="{{ route('public.compare.add', $product->id) }}"><i class="far fa-exchange-alt"></i></a>
                @endif
            </div> -->
            <div class="product-badges product-badges-position product-badges-mrg">
                @if ($product->isOutOfStock())
                    <span style="background-color: #000; font-size: 11px;">{{ __('Out Of Stock') }}</span>
                @else
                    @if ($product->productLabels->count())
                        @foreach ($product->productLabels as $label)
                            <span @if ($label->color) style="background-color: {{ $label->color }}" @endif>{{ $label->name }}</span>
                        @endforeach
                    @elseif ($product->front_sale_price !== $product->price && $percentSale = get_sale_percentage($product->price, $product->front_sale_price))
                        <span class="hot">{{ $percentSale }}</span>
                    @endif
                @endif
            </div>
        </div>
        <div class="product-content-wrap">
            @php $category = $product->categories->sortByDesc('id')->first(); @endphp

            <!-- maryam -->
            <!-- @if ($category)
                <div class="product-category">
                    <a href="{{ $category->url }}">{{ $category->name }}</a>
                </div>
            @endif -->
            <h2><a href="{{ $product->url }}">{{ strlen($product->name) > 20 ? substr($product->name, 0, 20) . '...' : $product->name }}</a></h2>


            <!-- @if (EcommerceHelper::isReviewEnabled())
                <div class="rating_wrap">
                    <div class="rating">
                        <div class="product_rate" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                    </div>
                    <span class="rating_num">({{ $product->reviews_count }})</span>
                </div>
            @endif -->

            {!! apply_filters('ecommerce_before_product_price_in_listing', null, $product) !!}

            <div class="product-price">
                <span>{{ format_price($product->front_sale_price_with_taxes) }}</span>
                @if ($product->front_sale_price !== $product->price)
                    <span class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                @endif
            </div>

            {!! apply_filters('ecommerce_after_product_price_in_listing', null, $product) !!}

            @if (EcommerceHelper::isCartEnabled())
                <div class="product-action-1 show" @if (!EcommerceHelper::isReviewEnabled()) style="bottom: 10px;" @endif>
                <a href="#" class="action-btn hover-up js-add-to-wishlist-button" data-url="{{ route('public.wishlist.add', $product->id) }}"><i class="far fa-heart"></i></a>
                <a class="action-btn hover-up add-to-cart-button" data-id="{{ $product->id }}" data-url="{{ route('public.cart.add-to-cart') }}" href="#"><i class="far fa-shopping-bag"></i></a>
                </div>
            @endif
        </div>
    </div>
@endif

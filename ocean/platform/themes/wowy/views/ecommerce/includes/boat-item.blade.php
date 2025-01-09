@if ($product)
    <div class="product-cart-wrap boat-custom mb-30">
        <div class="product-img-action-wrap">
            <div class="product-img product-img-zoom">
                <a href="{{url('/customize-boat/'.$product->id)}}">
                    <img class="default-img" src="{{ RvMedia::getImageUrl($product->main_image, '', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}">
                    <!-- <img class="hover-img" src="{{ RvMedia::getImageUrl($product->image[1] ?? $product->image, 'product-thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}"> -->
                </a>
            </div>
            <!-- <div class="product-action-1">
                <a aria-label="{{ __('Quick View') }}" href="#" class="action-btn hover-up js-quick-view-button" data-url="{{ route('public.ajax.quick-view', $product->id) }}"><i class="far fa-eye"></i></a>
                @if (EcommerceHelper::isWishlistEnabled())
                    <a aria-label="{{ __('Add To Wishlist') }}" href="#" class="action-btn hover-up js-add-to-wishlist-button" data-url="{{ route('public.wishlist.add', $product->id) }}"><i class="far fa-heart"></i></a>
                @endif
                @if (EcommerceHelper::isCompareEnabled())
                    <a aria-label="{{ __('Add To Compare') }}" href="#" class="action-btn hover-up js-add-to-compare-button" data-url="{{ route('public.compare.add', $product->id) }}"><i class="far fa-exchange-alt"></i></a>
                @endif
            </div> -->
            
        </div>
        <div class="product-content-wrap text-center">
            <h2>{{ $product->ltitle }}</h2>


            <div class="product-price">
                STARTING AT
                <span>&nbsp;{{ format_price($product->price) }}</span>
            </div>

            <div class="justify-content-center desc">
                <span>{{ $product->descp }}</span>
            </div>

            <div class="justify-content-center mt-20 mb-20">
                <a href="{{url('/customize-boat/'.$product->id)}}"><button type="button" class="btn boat">Build Your Boat</button></a>
            </div>

        </div>
    </div>
@endif

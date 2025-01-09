@php
    $shows = EcommerceHelper::getShowParams();
    $showing = (int)request()->input('num', (int)theme_option('number_of_products_per_page', 12));
@endphp


<div class="sort-by-cover mr-10 products_sortby">
        <div class="sort-by-product-wrap">
            <div class="sort-by">
                <span><i class="fa fa-th"></i>{{ __('Show:') }}</span>
            </div>
            <div class="sort-by-dropdown-wrap">
                <span> {!! Arr::get($shows, $showing, (int)theme_option('number_of_products_per_page', 12)) !!} <i class="far fa-angle-down"></i></span>
            </div>
        </div>
        <div class="sort-by-dropdown products_ajaxsortby" data-name="num">
            <ul>
                @foreach ($shows as $key => $label)
                    <li>
                        <a data-label="{{ $label }}"
                            class="@if ($showing == $key) active @endif"
                            href="{{ request()->fullUrlWithQuery(['num' => $key]) }}">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
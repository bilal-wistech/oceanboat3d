@php
    $sorts = EcommerceHelper::getSortParams();
    $sortBy = request()->input('sort-by', 'default_sorting');
@endphp

    <div class="sort-by-cover products_sortby">
        <div class="sort-by-product-wrap">
            <div class="sort-by">
                <span><i class="fa fa-sort-amount-down"></i>{{ __('Sort by:') }}</span>
            </div>
            <div class="sort-by-dropdown-wrap">
                <span><span>{!! Arr::get($sorts, $sortBy) !!}</span> <i class="far fa-angle-down"></i></span>
            </div>
        </div>
        <div class="sort-by-dropdown products_ajaxsortby" data-name="sort-by">
            <ul>
                @foreach ($sorts as $key => $label)
                    <li>
                        <a data-label="{{ $label }}"
                        class="@if ($sortBy == $key) active @endif"
                        href="{{ request()->fullUrlWithQuery(['sort-by' => $key]) }}">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>


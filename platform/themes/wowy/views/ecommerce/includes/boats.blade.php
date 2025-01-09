@php
    $layout = 'customize-boat';
@endphp

<div class="list-content-loading">
    <div class="half-circle-spinner">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
    </div>
</div>



<input type="hidden" name="page" data-value="{{ $products->currentPage() }}">
<input type="hidden" name="num" value="{{ request()->input('num') }}">
<input type="hidden" name="q" value="{{ BaseHelper::stringify(request()->query('q')) }}">

<div class="row">
    @forelse ($products as $product)
        <div class="col-lg-4 col-md-4">
            @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.boat-item', compact('product'))
        </div>
    @empty
        <div class="mt__60 mb__60 text-center">
            <p>{{ __('No products found!') }}</p>
        </div>
    @endforelse
</div>

@if ($products->total() > 0)
    <br>
    {!! $products->withQueryString()->onEachSide(1)->links(Theme::getThemeNamespace() . '::partials.custom-pagination') !!}
@endif

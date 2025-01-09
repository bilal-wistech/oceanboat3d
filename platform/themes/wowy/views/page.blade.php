@php
    $page->loadMissing('metadata');
    Theme::set('page', $page);
@endphp

@if ($page->template == 'default' && $page->slug == "about-us")
    <section class="mt-60 mb-60">
    	@include(Theme::getThemeNamespace() . '::views.ecommerce.includes.about-us', compact('page'))
    </section>
@elseif ($page->template == 'default')
	<section class="mt-60 mb-60">
        @if($page->slug != "contact")
    		<div class="container">
    	        <div class="row">
    	            <div class="col-12 text text-center">
    	                <h2 class="mb-40 fw-800">{!! $page->name !!}</h2>
    	                <p>{{ $page->description }}</p>
    	            </div>
    	        </div>
        	</div>
        @endif
    	{!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, BaseHelper::clean($page->content), $page) !!}
    </section>
@else
    {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, BaseHelper::clean($page->content), $page) !!}
@endif

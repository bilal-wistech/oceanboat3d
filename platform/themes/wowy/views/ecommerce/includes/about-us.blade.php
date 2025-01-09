@php
    $content_parts = explode("[", $page->content);
@endphp

<section class="mt-50 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text">
                <h3 class="mb-40 fw-800"><span class="billing">{{ $page->name}}</span></h3>
                {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, BaseHelper::clean($content_parts[0]), $page) !!}
            </div>
            <div class="col-md-6">
                <div class="image-stack">
                    <div class="image-stack__item image-stack__item--bottom">
                        <img src="{{ RvMedia::getImageUrl(isset($page->images[2]) ? $page->images[2] : '', '', false, RvMedia::getDefaultImage()) }}" alt="{{ $page->name }}">
                    </div>
                    <div class="image-stack__item image-stack__item--top">
                        <img src="{{ RvMedia::getImageUrl(isset($page->images[1]) ? $page->images[1] : '', '', false, RvMedia::getDefaultImage()) }}" alt="{{ $page->name }}" >
                    </div>
                </div>
            </div>
          
        </div>
    </div>
<div class="bottom-section mt-80">
    <img src="{{ RvMedia::getImageUrl(isset($page->images[3])? $page->images[3] : '', '', false, RvMedia::getDefaultImage()) }}" alt="{{ $page->name }}" class="bottom-img">
    <div class="card-container about">
    @foreach($content_parts as $key=>$content)
        @if($key>0)
            {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, BaseHelper::clean('['.$content), $page) !!}
        @endif
    @endforeach
  </div>
</div>
</section>

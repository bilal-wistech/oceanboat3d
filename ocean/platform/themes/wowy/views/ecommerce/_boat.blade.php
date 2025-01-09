@php
    $layout = 'customize-boat';
    Theme::layout($layout);

    Theme::asset()->usePath()->add('jquery-ui-css', 'css/plugins/jquery-ui.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-js', 'js/plugins/jquery-ui.js');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-touch-punch-js', 'js/plugins/jquery.ui.touch-punch.min.js');

    $categories=$product->frontcategories();
    $colors=childitems($product->frontcategories()[0]->id);
@endphp

<div class="row">
    <div class="col-8">
        <div class="row">
        <ul class="nav justify-content-center">
  @forelse($categories as $key=>$value)
  <li class="nav-item cat-item">
    <p class="nav-link" data-value="{{$value->type}}">{{$value->ltitle}}</p>
  </li>
  @empty
  @endforelse
  <li class="nav-item cat-item">
    <p class="nav-link" data-value="options" data-type="options">Options</p>
  </li>
  <li class="nav-item cat-item">
    <p class="nav-link" data-value="{{$product->id}}" data-type="summary">Summary</p>
  </li>
</ul>
</div>
<div class="row">
    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">

  @foreach($product->childitems() as $key=>$value)
  <div style="position:absolute;" class="cat-{{$value->id}}-img1"  >
    <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
  </div>
  @endforeach
  
  <div class="" style="z-index:0">
     <img src="{{ RvMedia::getImageUrl($product->image[1] ?? $product->image, '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
  </div>

    </div>
    <div class="carousel-item">

       @foreach($product->childitems() as $key=>$value)
  <div style="position:absolute;" class="cat-{{$value->id}}-img2"  >
    <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
  </div>
  @endforeach
  
  <div class="" style="z-index:0">
    <img src="{{ RvMedia::getImageUrl($product->image[2] ?? $product->image, '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
  </div>


    </div>
    <div class="carousel-item">

      @foreach($product->childitems() as $key=>$value)
  <div style="position:absolute;" class="cat-{{$value->id}}-img3"  >
    <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
  </div>
  @endforeach
  
  <div class="" style="z-index:0">
    <img src="{{ RvMedia::getImageUrl($product->image[3] ?? $product->image, '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
  </div>

      
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
</div>
    </div>
    <div class="col-4">
      @forelse($categories as $key=>$value)
        <div class="card card-category card-{{$value->type}}">
            <div class="card-header">
                <h4 class="category cat-title">{{$key+1}}. Choose your {{$value->type}}</h4>
            </div>
            <div class="card-body cat-body">
                @forelse($value->childitems() as $color)
                <div class="form-check">
                    <input class="form-check-input cat-item-check" name="{{$value->type}}" type="radio" value="{{$color->id}}" data-parent="{{$color->parent_id}}" data-waschecked="false">
                    <label class="form-check-label" for="{{$color->id}}">
                        {{$color->ltitle}}  ({{ format_price($color->price) }})
                    </label>
                </div>
                @empty
                @endforelse
            </div>
            <div class="card-footer">
              @if($key>0)
                <button class="btn card-btn" data-value="{{$categories[$key-1]->type}}" type="button">Previous Step</button>
              @endif
                <button class="btn card-btn" data-value="{{isset($categories[$key+1]) ? $categories[$key+1]->type : 'options'}}" type="button">Next Step</button>
            </div>
        </div>
        @empty
        @endforelse
        <div class="card card-category card-options">
            <div class="card-header">
                <h4 class="category cat-title">4. Choose your options</h4>
            </div>
            @php
            $cat_options=\NaeemAwan\PredefinedLists\Models\PredefinedList::where('parent_id',$product->id)->where('type','options')->get();
            @endphp
            <div class="card-body cat-body">
                @forelse($cat_options as $value)
                <div class="col">
                  {{$value->ltitle}}
                </div> 
                  @forelse($value->childitems() as $color)
                <div class="form-check">
                    <input class="form-check-input cat-item-check" type="radio" value="{{$color->id}}" name="options" data-parent="{{$color->parent_id}}" data-waschecked="false">
                    <label class="form-check-label" for="{{$color->id}}">
                        {{$color->ltitle}}  ({{ format_price($color->price) }})
                    </label>
                </div>
                  @empty
                  @endforelse
                @empty
                @endforelse
            </div>
            <div class="card-footer">
                <button class="btn card-btn" data-value="trailer" type="button">Previous Step</button>
                <button class="btn card-btn" data-value="summary" type="button">Next Step</button>
            </div>
        </div>
        <div class="card card-category card-summary">
          <div class="card-header">
              <h4 class="category cat-title">5. View your Summary</h4>
          </div>
          <div class="card-body">
              
          </div>
          <div class="card-footer">
              <button class="btn card-btn" data-value="options" type="button">Previous Step</button>
          </div>
        </div>
    </div>
</div>






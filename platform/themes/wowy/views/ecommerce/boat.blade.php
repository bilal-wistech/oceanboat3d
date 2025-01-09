@php
    $layout = 'customize-boat';
    Theme::layout($layout);

    Theme::asset()->usePath()->add('jquery-ui-css', 'css/plugins/jquery-ui.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-js', 'js/plugins/jquery-ui.js');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-touch-punch-js', 'js/plugins/jquery.ui.touch-punch.min.js');

    $categories=$product->childitems_display();
@endphp

<div class="row" id="custom-boat-container">
    <div class="col-lg-8 col-12">
        <ul class="row customboat-nav">
            @forelse($categories as $key=>$value)
                <li class="col cat-item cat-{{$value->type}} {{$key==0 ? 'selected' : ''}}">
                    <a class="customboat-nav-link " data-value="{{$value->type}}">{{$value->ltitle}}</a>
                </li>
            @empty
            @endforelse
            <li class="col cat-item cat-summary">
                <a class="customboat-nav-link" data-value="{{$product->id}}" data-type="summary">Summary</a>
            </li>
        </ul>
        @php
            $modelPath = $product->file;
        @endphp
        <div class="row">
            <div id="carouselExampleControls" class="custom-boat carousel slide">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        @foreach($product->childitems_sorted() as $category)
                            @foreach($category->childitems_sorted() as $key=>$value)
                                @if($category->multi_select == 1)
                                    @foreach($value->childitems_sorted() as $option)
                                        <div style="position:absolute;" class="cat-{{$option->id}}-img1">
                                            <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}"
                                                 class="d-block w-100" alt="...">
                                        </div>
                                    @endforeach
                                @else
                                    <div style="position:absolute;" class="cat-{{$value->id}}-img1">
                                        <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}"
                                             class="d-block w-100" alt="...">
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                        <div class="" style="z-index:0">
                            <img src="{{ RvMedia::getImageUrl($product->image[1] ?? $product->image, '', false, RvMedia::getDefaultImage()) }}"
                                 class="d-block w-100" alt="...">
                        </div>
                    </div>

                    <div class="carousel-item">
                        @foreach($product->childitems_sorted() as $category)
                            @foreach($category->childitems_sorted() as $key=>$value)
                                @if($category->multi_select == 1)
                                    @foreach($value->childitems_sorted() as $option)
                                        <div style="position:absolute;" class="cat-{{$option->id}}-img2">
                                            <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}"
                                                 class="d-block w-100" alt="...">
                                        </div>
                                    @endforeach
                                @else
                                    <div style="position:absolute;" class="cat-{{$value->id}}-img2">
                                        <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}"
                                             class="d-block w-100" alt="...">
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                        <div class="" style="z-index:0">
                            <img src="{{ RvMedia::getImageUrl($product->image[2] ?? $product->image, '', false, RvMedia::getDefaultImage()) }}"
                                 class="d-block w-100" alt="...">
                        </div>
                    </div>

                    <div class="carousel-item">
                        @foreach($product->childitems_sorted() as $category)
                            @foreach($category->childitems_sorted() as $key=>$value)
                                @if($category->multi_select == 1)
                                    @foreach($value->childitems_sorted() as $option)
                                        <div style="position:absolute;" class="cat-{{$option->id}}-img3">
                                            <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}"
                                                 class="d-block w-100" alt="...">
                                        </div>
                                    @endforeach
                                @else
                                    <div style="position:absolute;" class="cat-{{$value->id}}-img3">
                                        <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}"
                                             class="d-block w-100" alt="...">
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                        <div class="" style="z-index:0">
                            <img src="{{ RvMedia::getImageUrl($product->image[3] ?? $product->image, '', false, RvMedia::getDefaultImage()) }}"
                                 class="d-block w-100" alt="...">
                        </div>
                    </div>

                    @if(isset($product->image[4]))
                        <div class="carousel-item">
                            @foreach($product->childitems_sorted() as $category)
                                @foreach($category->childitems_sorted() as $key=>$value)
                                    @if($category->multi_select == 1)
                                        @foreach($value->childitems_sorted() as $option)
                                            <div style="position:absolute;" class="cat-{{$option->id}}-img4">
                                                <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}"
                                                     class="d-block w-100" alt="...">
                                            </div>
                                        @endforeach
                                    @else
                                        <div style="position:absolute;" class="cat-{{$value->id}}-img4">
                                            <img src="{{ RvMedia::getImageUrl('transparent-pic-150x150.png', '', false, RvMedia::getDefaultImage()) }}"
                                                 class="d-block w-100" alt="...">
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                            <div class="" style="z-index:0">
                                <img src="{{ RvMedia::getImageUrl($product->image[4] ?? $product->image, '', false, RvMedia::getDefaultImage()) }}"
                                     class="d-block w-100" alt="...">
                            </div>
                        </div>
                    @endif

                </div>
                <button style="left: 40% !important; top: 100% !important;" class="carousel-control-prev" type="button"
                        data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button style="left: 50% !important; top: 100% !important;" class="carousel-control-next" type="button"
                        data-bs-target="#carouselExampleControls" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <input type="hidden" name="boat_price" value="{{$product->price}}">

    </div>

    <div class="col-lg-4 col-12">
        <form id="submit-form" action="{{ route('public.customize-boat.submit') }}" method="post">
            @csrf
            <input type="hidden" name="boat_id" value="{{$product->id}}">
            <input type="hidden" name="total_price" value="0">
            @forelse($categories as $key=>$value)
                    <?php
                    $i = $key + 1;
                    ?>
                <div class="customboat-card card-category card-{{$value->type}} {{$key>0 ? 'd-none' : ''}}">
                    <div class="customboat-card-header">
                        <h4 class="category cat-title">{{$i}}. Choose your {{$value->ltitle}}</h4>
                    </div>
                    <div class="customboat-card-body cat-body">
                        @forelse($value->childitems() as $key1 => $value1)
                            <div class="col btn options-boat dropdown-toggle mt-5 mb-15" data-bs-toggle="collapse"
                                 href="#collapse{{$key1}}" aria-expanded="{{$key1==0 ? 'true': 'false' }}">
                                <div class="title">{{$value1->ltitle}}</div>
                            </div>
                            <div class="collapse {{$key1==0 ? 'show': '' }}" id="collapse{{$key1}}">
                                @forelse($value1->childitems() as $option)
                                    @if($option->side_layout=='radio')
                                        @if($value->multi_select!=3)
                                            <input class="form-check-input visually-hidden cat-item-check"
                                                   type="{{$value->multi_select == 1 ? 'checkbox' : 'radio' }}"
                                                   id="{{$option->id}}" value="{{$option->id}}"
                                                   data-typename="{{$value1->ltitle}}"
                                                   data-type="{{$value->multi_select == 2 ? $value->type : $value1->type }}"
                                                   name="{{$value->multi_select == 2 ? 'option['.$value->type.']' : 'option['.$value1->type.']' }}"
                                                   data-parent="{{$option->parent_id}}" data-waschecked="false">
                                        @endif
                                        <label class="form-check-label color-box" for="{{$option->id}}"
                                               style="background-image: url({{ RvMedia::getImageUrl($option->main_image) }});">
                                            <div class="tick-icon"><img src="{{ asset('/storage/check_circle.png') }}">
                                            </div>
                                            <div class="color-name">{{$option->ltitle}}</div>
                                        </label>

                                    @elseif($option->side_layout=='toggle')
                                        <div class="form-check">
                                            @if($value->multi_select!=3)
                                                <input class="form-check-input cat-item-check"
                                                       name="{{$value->multi_select == 2 ? 'option['.$value->type.']' : 'option['.$value1->type.']' }}"
                                                       type="{{$value->multi_select == 1 ? 'checkbox' : 'radio' }}"
                                                       value="{{$option->id}}" data-typename="{{$value1->ltitle}}"
                                                       data-type="{{$value->multi_select == 2 ? $value->type : $value1->type }}"
                                                       data-parent="{{$option->parent_id}}" data-waschecked="false"
                                                       id="collapse-{{$option->id}}">
                                            @endif
                                            <label class="form-check-label" for="collapse-{{$value->id}}">
                                                <div data-bs-toggle="{{$option->main_image? 'collapse' : ''}}"
                                                     data-bs-target="#color-details-{{$option->id}}"
                                                     aria-expanded="false" aria-controls="color-details-{{$option->id}}"
                                                     class="tog {{$option->main_image? 'dropdown-toggle' : ''}}">
                                                    {{$option->ltitle}} ({{ format_price($option->price) }})
                                                </div>
                                            </label>
                                            <div class="collapse" id="color-details-{{$option->id}}">
                                                <div class="content-boat">
                                                    <img class="img-fluid img-thumbnail landscape"
                                                         src="{{ RvMedia::getImageUrl($option->main_image) }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                @endforelse
                            </div>
                        @empty
                        @endforelse

                        <div class="row">
                            <div class="col-8">
                                <p class="text-center" style="font-size:16px"><b>Sub Total</b>: <span
                                            class="sub-total">{{ format_price($product->price) }}</span></p>
                                <p class="text-center" style="font-size:16px"><b>VAT 5%</b>: <span
                                            class="vat-price">{{ format_price(($product->price * 5)/100) }}</span></p>
                                <p class="text-center mb-10" style="font-size:16px"><b>Total</b>: <span
                                            class="vat-total">{{ format_price($product->price + (($product->price * 5)/100)) }}</span>
                                </p>
                            </div>
            
                        </div>

                    </div>
                    <div class="customboat-card-footer d-flex justify-content-between flex-row">
                        @if(isset($categories[$key+1]))
                            @if($key>0)
                                <button class="btn card-btn prv" data-curval="{{$categories[$key]->type}}"
                                        data-value="{{$categories[$key-1]->type}}" type="button">Back
                                </button>
                            @endif
                            <button class="btn card-btn" type="button" data-curval="{{$categories[$key]->type}}"
                                    data-value="{{isset($categories[$key+1]) ? $categories[$key+1]->type : ''}}">Next
                                Step
                            </button>
                        @else
                            <button class="btn card-btn prv" data-curval="{{$categories[$key]->type}}"
                                    data-value="{{isset($categories[$key-1]) ? $categories[$key-1]->type : ''}}"
                                    type="button">Back
                            </button>
                            <button class="btn card-btn" data-curval="{{$categories[$key]->type}}" data-value="summary"
                                    type="button">Next Step
                            </button>
                        @endif
                    </div>
                </div>
            @empty
            @endforelse
            <div class="customboat-card card-category card-summary mb-5 d-none">
                <div class="customboat-card-header">
                    <h4 class="category cat-title">{{$i+1}}. Final Step</h4>
                </div>
                <div class="customboat-card-body">
                    <div class="form-group">
                        <div class="textarea-style">
                            <textarea name="message" placeholder="{{ __('Message') }}"></textarea>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="redirect_url_pay" value="0">
                <div class="mt-10 customboat-card-footer d-flex flex-row flex-wrap">
                    <button type="button" class="btn card-btn prv" data-curval="summary"
                            data-value="{{lastitem($product->id)->type}}">Back
                    </button>
                    <button type="submit" class="btn card-btn" style="border-radius: unset;">Save & Exit</button>
                    <button type="button" class="btn view-summary">View Your Summary</button>
                    <button type="button" class="btn card-btn submit-btn" style="border-radius: unset;">Book Boat Now
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Booking Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>For booking confirmation of your Boat, you need to
                    pay {{ format_price(get_ecommerce_setting('down_payment'))}} as a downpayment. This will confirm
                    your booking and our team will get in touch with you further.</p>
                <p class="alert alert-success">Note: All Credit/Debit card and Apple Pay payments are accepted.</p>
            </div>
            <div class="modal-footer">
                <a id="submit-boot" class="btn btn-small d-block" href="#">Proceed to checkout</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-60" id="summary-end">
    <div class="col-md-8 col-12 m-auto">
        <div style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
            <div class="card card-custom">
                <div class="card-header text-center bg-brand">
                    <h4 class="text-white">Summary</h4>
                </div>
                <div class="card-body summary-card justify-content-center d-flex flex-row flex-wrap">
                </div>
                <div class="card-body list-style">
                    <h4>Included:</h4>
                    {!! $product->detail->standard_options !!}
                </div>
            </div>
            <div class="card-footer">
                <div class="row m-2">
                    <div class="col-9 text-end">
                        <p><b>Sub Total</b>: <span class="sub-total">{{ format_price($product->price) }}</span></p>
                        <p><b>VAT 5%</b>: <span class="vat-price">{{ format_price(($product->price * 5)/100) }}</span>
                        </p>
                        <p><b>Total</b>: <span
                                    class="vat-total">{{ format_price($product->price + (($product->price * 5)/100)) }}</span>
                        </p>
                    </div>
                 
                </div>

            </div>
        </div>
    </div>
</div>
</div>
<!-- scrolling -->
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    jQuery(function($) {
        var offset = $('#custom-boat-container').offset().top - 1;
        $('html, body').animate({
            scrollTop: offset
        }, 'slow');
    });
</script>

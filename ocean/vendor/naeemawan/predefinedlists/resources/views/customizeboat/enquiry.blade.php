@if ($boat_enquiry)
	<p>User Email: <i>{{ $boat_enquiry->customer->email }}</i></p>
    <p>User Name: <b><i>{{ $boat_enquiry->customer->name }}</i></b></p>
    <p>User Phone Number: <b><i>{{ $boat_enquiry->customer->phone }}</i></b></p>
    <p>Submitted At: <i>{{ $boat_enquiry->created_at }}</i></p>
    <p>Boat Name: <b><i>{{ $boat_enquiry->boat->ltitle }}</i></b></p>
    <p>Boat Customization Details: <b><i>{{ $boat_enquiry->message }}</i></b></p>
    <div class="row" id="summary-end">
	  <div class="col-12 m-auto">
	    <div class="card card-custom">
	      <div class="card-body summary-card justify-content-center d-flex flex-row flex-wrap">
	      	@if($boat_enquiry->details)
			@foreach($boat_enquiry->details as $key=>$value)
	      	<div class="card m-1">
	      		<div class="card-body text-center">
	      			<p><b>{{$value->slug->ltitle}}: </b>{{$value->slug->parent->ltitle}}</p>
	      			<p><b>Price</b> : {{ format_price($value->enquiry_option->price) }}</p>
	      		</div>
	      	</div>
			@endforeach
	      	@endif
	      </div>
	    </div>
		<!-- Final image -->
		<div class="row">
                        <div id="carouselExampleControls" class="custom-boat carousel slide">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
							@foreach($boat_enquiry->details as $key=>$value)
                            @if(isset($value->enquiry_option->image[1]))
                                <div style="position:absolute;"  >
                                    <img src="{{ RvMedia::getImageUrl($value->enquiry_option->image[1], '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
                                </div>
                            @endif
                            @endforeach
                            <div class="" style="z-index:0">
                                <img src="{{ RvMedia::getImageUrl($boat_enquiry->boat->image[1] ?? $boat_enquiry->image, '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
                            </div>
                            </div>

                            <div class="carousel-item">
                            @foreach($boat_enquiry->details as $key=>$value)
                            @if(isset($value->enquiry_option->image[2]))
                                <div style="position:absolute;"  >
                                    <img src="{{ RvMedia::getImageUrl($value->enquiry_option->image[2], '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
                                </div>
                            @endif
                            @endforeach
                            <div class="" style="z-index:0">
                                <img src="{{ RvMedia::getImageUrl($boat_enquiry->boat->image[2] ?? $boat_enquiry->image, '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
                            </div>
                            </div>

                            <div class="carousel-item">
							@foreach($boat_enquiry->details as $key=>$value)
                            @if(isset($value->enquiry_option->image[3]))
                                <div style="position:absolute;"  >
                                    <img src="{{ RvMedia::getImageUrl($value->enquiry_option->image[3], '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
                                </div>
                            @endif
                            @endforeach
                            <div class="" style="z-index:0">
                                <img src="{{ RvMedia::getImageUrl($boat_enquiry->boat->image[3] ?? $boat_enquiry->image, '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
                            </div>
                            </div>

                            @if(isset($boat_enquiry->boat->image[4]))
                            <div class="carousel-item">
							@foreach($boat_enquiry->details as $key=>$value)
                            @if(isset($value->enquiry_option->image[4]))
                                <div style="position:absolute;"  >
                                    <img src="{{ RvMedia::getImageUrl($value->enquiry_option->image[4], '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
                                </div>
                            @endif
                            @endforeach
                            <div class="" style="z-index:0">
                                <img src="{{ RvMedia::getImageUrl($boat_enquiry->boat->image[4] ?? $boat_enquiry->image, '', false, RvMedia::getDefaultImage()) }}" class="d-block w-100" alt="...">
                            </div>
                            </div>
                            @endif

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

        <div class="card-body">
        <h6>Included:</h6>
      {!! $boat_enquiry->boat->detail->standard_options !!}
      </div>
                    
                   
		<!-- end -->
	    <div class="card-footer">
	      <div class="row m-2">
	        <div class="col text-end">
	          <hr/>
	          <p><b>Boat Price</b>: <span class="sub-total">{{ format_price($boat_enquiry->boat->price) }}</span></p>
	          <p><b>Total Price</b>: <span class="sub-total">{{ format_price($boat_enquiry->total_price) }}</span></p>
	          <p><b>Total Price with 5% Vat</b>: <span class="sub-total">{{ format_price($boat_enquiry->vat_total) }}</span></p>
	          <p><b>Paid</b>: <span class="boat-price">{{ format_price($boat_enquiry->paid_amount) }}</span></p>
	          <p><b>Remaining Price</b>: <span class="boat-price">{{ format_price($boat_enquiry->vat_total - $boat_enquiry->paid_amount) }}</span></p>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
@endif
<style>
.carousel-control-prev {
    top: 80%;
    left: 40%;
}
.carousel-control-next {
    top: 80%;
    left: 50%;
}
.carousel-control-next-icon{
	background-image:none;
}
.carousel-control-prev-icon{
	background-image:none;
}
.custom-boat .carousel-control-next-icon {
    width: 20px;
    height: 20px;
    border: 4px solid #182955;
    border-left: 0;
    border-bottom: 0;
    transform: rotate(45deg);
}
.custom-boat .carousel-control-prev-icon {
    width: 20px;
    height: 20px;
    border: 4px solid #182955;
    border-right: 0;
    border-top: 0;
    transform: rotate(45deg);
}
</style>

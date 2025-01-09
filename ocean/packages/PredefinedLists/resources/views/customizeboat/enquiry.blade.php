@if ($boat_enquiry)
    <p>Submitted At: <i>{{ $boat_enquiry->created_at }}</i></p>
    <p>Boat Name: <b><i>{{ $boat_enquiry->boat->ltitle }}</i></b></p>
    <p>Message: <b><i>{{ $boat_enquiry->message }}</i></b></p>
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
	    <div class="card-footer">
	      <div class="row m-2">
	        <div class="col text-end">
	          <hr/>
	          <p><b>Boat Price</b>: <span class="boat-price">{{ format_price($boat_enquiry->boat->price) }}</span></p>
	          <p><b>Sub Total</b>: <span class="sub-total">{{ format_price($boat_enquiry->total_price) }}</span></p>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
@endif

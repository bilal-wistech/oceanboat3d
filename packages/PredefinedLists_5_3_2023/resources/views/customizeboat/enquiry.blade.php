@if ($boat_enquiry)
    <p>Submitted At: <i>{{ $boat_enquiry->created_at }}</i></p>
    <p>Boat Name: <b><i>{{ $boat_enquiry->boat->ltitle }}</i></b></p>
    <p>Message: <b><i>{{ $boat_enquiry->message }}</i></b></p>
    <div class="row" id="summary-end">
	  <div class="col-12 m-auto">
	    <div class="card card-custom">
	      <div class="card-body summary-card justify-content-center d-flex flex-row flex-wrap">
	      	@if($boat_enquiry->color!=null)
	      	<div class="card m-1">
	      		<div class="card-body text-center">
	      			<p><b>Color: </b>{{$boat_enquiry->color_option->ltitle}}</p>
	      			<p><b>Price</b> : $<span class="price">{{ format_price($boat_enquiry->color_option->price) }}</span></p>
	      		</div>
	      	</div>
	      	@endif
	      	@if($boat_enquiry->motor!=null)
	      	<div class="card m-1">
	      		<div class="card-body text-center">
	      			<p><b>Motor: </b>{{$boat_enquiry->motor_option->ltitle}}</p>
	      			<p><b>Price</b> : $<span class="price">{{ format_price($boat_enquiry->motor_option->price) }}</span></p>
	      		</div>
	      	</div>
	      	@endif
	      	@if($boat_enquiry->trailor!=null)
	      	<div class="card m-1">
	      		<div class="card-body text-center">
	      			<p><b>Trailor: </b>{{$boat_enquiry->trailor_option->ltitle}}</p>
	      			<p><b>Price</b> : $<span class="price">{{ format_price($boat_enquiry->trailor_option->price) }}</span></p>
	      		</div>
	      	</div>
	      	@endif
	      	@if($boat_enquiry->canvas_covers!=null)
	      	<div class="card m-1">
	      		<div class="card-body text-center">
	      			<p><b>Canvas & Covers: </b>{{$boat_enquiry->canvas_option->ltitle}}</p>
	      			<p><b>Price</b> : $<span class="price">{{ format_price($boat_enquiry->canvas_option->price) }}</span></p>
	      		</div>
	      	</div>
	      	@endif
	      	@if($boat_enquiry->fishing_locator!=null)
	      	<div class="card m-1">
	      		<div class="card-body text-center">
	      			<p><b>Fishing Locator: </b>{{$boat_enquiry->fishing_option->ltitle}}</p>
	      			<p><b>Price</b> : $<span class="price">{{ format_price($boat_enquiry->fishing_option->price)}}</span></p>
	      		</div>
	      	</div>
	      	@endif
	      	@if($boat_enquiry->general!=null)
	      	<div class="card m-1">
	      		<div class="card-body text-center">
	      			<p><b>General Options: </b>{{$boat_enquiry->general_option->ltitle}}</p>
	      			<p><b>Price</b> : $<span class="price">{{ format_price($boat_enquiry->general_option->price) }}</span></p>
	      		</div>
	      	</div>
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

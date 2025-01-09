<p>Boat Name : <i><b>{{ $order->boat->ltitle}} </b></i></p>
<p>Boat Price : <i><b>{{ format_price($order->boat->price) }} </b></i></p>
<div class="table">
    <table>

    	<tr>
            <td style="padding: 8px;border-bottom: 1px solid #ddd;"><b>Accessories/Add On</b></td>
            <td style="padding: 8px;border-bottom: 1px solid #ddd;"><b>Title</b></td>
            <td style="padding: 8px;border-bottom: 1px solid #ddd;"><b>Price</b></td>
        </tr>

        @if($order->details)
			@foreach($order->details as $key=>$value)
            <tr>
                <td style="padding: 8px;border-bottom: 1px solid #ddd;">{{$value->slug->ltitle}}</td>
                <td style="padding: 8px;border-bottom: 1px solid #ddd;">{{$value->slug->parent->ltitle}}</td>
                <td style="padding: 8px;border-bottom: 1px solid #ddd;">{{ format_price($value->enquiry_option->price) }}</td>
            </tr>
			@endforeach
	      	@endif

        <tr>
            <td style="padding: 8px;">&nbsp;</td>

            <td style="padding: 8px; text-align: left"></td>
            <td style="padding: 8px; text-align: left">
              <p><b>Boat Price</b>: <span class="sub-total">{{ format_price($order->boat->price) }}</span></p>
	          <p><b>Total Price</b>: <span class="sub-total">{{ format_price($order->total_price) }}</span></p>
	          <p><b>Total Price with 5% Vat</b>: <span class="sub-total">{{ format_price($order->vat_total) }}</span></p>
	          <p><b>Paid</b>: <span class="boat-price">{{ format_price($order->paid_amount) }}</span></p>
	          <p><b>Remaining Price</b>: <span class="boat-price">{{ format_price($order->vat_total - $order->paid_amount) }}</span></p>
            </td>
        </tr>
    </table>
</div>


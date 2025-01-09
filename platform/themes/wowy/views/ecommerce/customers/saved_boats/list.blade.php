@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Booking Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>For booking confirmation of your Boat, you need to pay {{ format_price(get_ecommerce_setting('down_payment'))}} as a downpayment. This will confirm your booking and our team will get in touch with you further.</p>
      </div>
      <div class="modal-footer">
         <a id="link" class="btn btn-small d-block" href="#">Proceed to checkout</a>
      </div>
    </div>
  </div>
</div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Your Saved Boats') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Enquiry ID') }}</th>
                            <th>{{ __('Boat Name') }}</th>
                            <th>{{ __('Submitted At') }}</th>
                            <th>{{ __('Total Price') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($boats as $boat)
                            <tr>
                                <td>OBCE-{{ $boat->id }}</td>
                                <td>{{ $boat->boat->ltitle }}</td>
                                <td>{{ $boat->created_at->format('d-m-Y h:m') }}</td>
                                <td>{{ format_price($boat->vat_total) }}</td>
                                <td>
                                    <a class="btn btn-small d-block mb-2" href="{{ route('customer.saved_boats.view', $boat->id) }}">{{ __('View Details') }}</a>
                                    @if($boat->is_finished)
                                    <a data-no-href class="btn btn-small d-block">Booked</a>
                                    @else
                                    <!-- <a class="btn btn-small d-block" href="{{ route('ngenius.transaction.id', $boat->id) }}">{{ __('Book Now') }}</a> -->
                                    <button type="button" class="btn btn-small d-block" id="book-now" style="width:100%;" data-bs-toggle="modal" data-value="{{ route('ngenius.transaction.id', $boat->id) }}" data-bs-target="#exampleModal">
                                        {{ __('Book Now') }}
                                    </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="5">{{ __('No orders found!') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {!! $boats->links(Theme::getThemeNamespace() . '::partials.custom-pagination') !!}
            </div>
        </div>
    </div>
@endsection

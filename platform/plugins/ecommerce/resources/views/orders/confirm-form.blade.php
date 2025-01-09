<div class="modal fade" id="confirm-order" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xs ">
  {!! Form::open(['route' => 'create.shipment']) !!}
    <div class="modal-content">
      <div class="modal-header bg-info">
      <h4 class="modal-title"><i class="til_img"></i><strong>Confirm and Ship order</strong></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body with-padding">
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <input class="form-check-input" name="pickup" type="checkbox" value="1">
                <label class="form-check-label">Pickup Order?</label>
            </div>
        </div>
        <br/>
        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                <label class="text-title-field">Time the location premises is available to dispatch order</label>
                <span>(Required if Pickup requested)</span>
                <input type="time" class="next-input" name="time" placeholder="18:00">
            </div>
        </div>
        <br/>
        <div class="next-form-grid">
            <div class="next-form-grid-cell">
                (5 working days (Mon-Sat) will be added on today's date for planned shipment and pickup date.)
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="float-start btn btn-warning" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="float-end btn btn-info">Confirm</button>
      </div>
    </div>
    {!! Form::close() !!}
  </div>
</div>
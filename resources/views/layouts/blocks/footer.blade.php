<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
  <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
    <div class="text-dark order-2 order-md-1">
      <span class="text-muted font-weight-bold mr-2">{{ date("Y") }}©</span>
      <a href="{{ url('/') }}" target="_blank" class="text-dark-75 text-hover-primary">{{ config('app.name') }}</a>
    </div>
  </div>
</div>
<div class="loader" style="display:none;">
  <center>
    <span class="svg-icon svg-icon-primary svg-icon-2x">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <rect x="0" y="0" width="24" height="24"/>
          <path d="M8,4 C8.55228475,4 9,4.44771525 9,5 L9,17 L18,17 C18.5522847,17 19,17.4477153 19,18 C19,18.5522847 18.5522847,19 18,19 L9,19 C8.44771525,19 8,18.5522847 8,18 C7.44771525,18 7,17.5522847 7,17 L7,6 L5,6 C4.44771525,6 4,5.55228475 4,5 C4,4.44771525 4.44771525,4 5,4 L8,4 Z" fill="#000000" opacity="0.3"/>
          <rect fill="#000000" opacity="0.3" x="11" y="7" width="8" height="8" rx="4"/>
          <circle fill="#000000" cx="8" cy="18" r="3"/>
        </g>
      </svg>
    </span>
  </center>
</div>

<div id="general-modal" class="mmcls fade modal" role="dialog" tabindex="-1" style="z-index:99999">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i aria-hidden="true" class="ki ki-close"></i></button>
      </div>
      <div class="modal-body">
        <div class='modalContent'></div>
      </div>
    </div>
  </div>
</div>
<div id="secondary-modal" class="mmcls fade modal" role="dialog" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i aria-hidden="true" class="ki ki-close"></i></button>
      </div>
      <div class="modal-body">
        <div class='modalContent'></div>
      </div>
    </div>
  </div>
</div>

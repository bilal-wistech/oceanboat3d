@push('toasterinlinejs')
@if (Session::has('success'))
toastr.success("{{ Session::get('success') }}", "{{__('common.success')}}", {timeOut: 5000});
@endif
@if (Session::has('error'))
toastr.error("{{ Session::get('error') }}", "{{__('common.error')}}", {timeOut: 5000});
@endif
@if (Session::has('warning'))
toastr.warning("{{ Session::get('warning') }}", "{{__('common.warning')}}", {timeOut: 5000});
@endif
@endpush
@if (Session::has('success') || Session::has('error') || Session::has('warning'))
@endif

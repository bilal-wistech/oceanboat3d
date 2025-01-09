<footer class="main">
    <section class="section-padding-60">
        <div class="container">
            <div class="row">
                {!! dynamic_sidebar('footer_sidebar') !!}
            </div>
        </div>
    </section>
    <div class="container pb-20 wow fadeIn animated">
        <div class="row">
            <div class="col-12 mb-20">
                <div class="footer-bottom"></div>
            </div>
            <div class="col-lg-6">
                <p class="float-md-left font-sm mb-0">{{ theme_option('copyright') }} {{ __('All rights reserved.') }}</p>
            </div>
            <div class="col-lg-6 d-none">
                <p class="text-lg-end text-start font-sm mb-0">
                    Made with <i class="fa fa-heart" style="color:red"></i> <a href="https://www.wistech.biz/" style="color:white"> Wisdom</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Quick view -->
<div class="modal fade custom-modal" id="quick-view-modal" tabindex="-1" aria-labelledby="quick-view-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="half-circle-spinner loading-spinner">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                </div>
                <div class="quick-view-content"></div>
            </div>
        </div>
    </div>
</div>

@if (is_plugin_active('ecommerce'))
    <script>
        window.currencies = {!! json_encode(get_currencies_json()) !!};
    </script>
@endif

{!! Theme::footer() !!}

<script>
    window.trans = {
        "Views": "{{ __('Views') }}",
        "Read more": "{{ __('Read more') }}",
        "days": "{{ __('days') }}",
        "hours": "{{ __('hours') }}",
        "mins": "{{ __('mins') }}",
        "sec": "{{ __('sec') }}",
        "No reviews!": "{{ __('No reviews!') }}"
    };
</script>

{!! Theme::place('footer') !!}
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/3.1.2/fullpage.min.js"></script>

@if (session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count() > 0) || isset($error_msg))
        <script type="text/javascript">
            window.onload = function () {
                @if (session()->has('success_msg'))
                window.showAlert('alert-success', '{{ session('success_msg') }}');
                @endif

                @if (session()->has('error_msg'))
                window.showAlert('alert-danger', '{{ session('error_msg') }}');
                @endif

                @if (isset($error_msg))
                window.showAlert('alert-danger', '{{ $error_msg }}');
                @endif

                @if (isset($errors))
                @foreach ($errors->all() as $error)
                window.showAlert('alert-danger', '{!! BaseHelper::clean($error) !!}');
                @endforeach
                @endif
            };
        </script>
    @endif
    <script>

$(document).ready(function() {
    $('.dropdown').hover(function() {
        $(this).find('.dropdown-menu').addClass('show');
        $(this).find('.dropdown-toggle').attr('aria-expanded', 'true');
    }, function() {
        $(this).find('.dropdown-menu').removeClass('show');
        $(this).find('.dropdown-toggle').attr('aria-expanded', 'false');
    });
    $('.dropdown-menu').mouseleave(function() {
        $(this).removeClass('show');
        $(this).prev('.dropdown-toggle').attr('aria-expanded', 'false');
    });

$('#fullpage').fullpage({
scrollingSpeed: 1000,
verticalCentered: false,
css3: true,
navigation: false,
navigationTooltips: ['Image 1', 'Image 2', 'Image 3'],
fitToSection: true,
afterRender: function(){
  $.fn.fullpage.moveTo(1);
}
});

$('.option').not(':first').hide();
  $('input[type="checkbox"]').click(function() {
  $('input[type="checkbox"]').not(this).prop('checked', false);
  $('.option').hide();
  if ($(this).prop('checked')) {
      $('#option' + $(this).val()).show();
    }
});

$('#book-now').on('click', function(event) {
var dataValue = $(this).data('value');
$('.modal-footer #link').attr('href',dataValue);
});


});


</script>

    <div id="scrollUp"><i class="fal fa-long-arrow-up"></i></div>
</body>
</html>

    {!! dynamic_sidebar('top_footer_sidebar') !!}
    <footer class="main">
        
        <section class="section-padding-60">
            <div class="container">
            <!-- maryam -->
            <div class="row">
                
                    <div class="col-lg-3 col-md-6">
    <div class="widget-about font-md mb-md-5 mb-lg-0">
        <div class="logo wow fadeIn  mb-10  animated" style="visibility: visible;">
            <a href="http://127.0.0.1:8000">
                <img src="storage/general/footer1.png" alt="footer">
            </a>
        </div>
        <p class="wow fadeIn  mt-20  animated" style="visibility: visible;">
            Germany - 785 15h Street, Office<br> 478 Berlin, De 81566
        </p>
        <p class="wow fadeIn  mt-20  animated" style="visibility: visible;">
            <i class="fa-envelope fas"></i>&nbsp; info@oceanboats.com
        </p>
        <p class="wow fadeIn  mt-20  animated" style="visibility: visible;">
            <i class="fa-phone fas"></i>&nbsp; +971 123 456 999
        </p>

        <div class="social-icons d-flex mt-30">
            <ul>
                <li><a href=""><i class="fab fa-linkedin"></i></a></li>
                <li><a href=""><i class="fab fa-facebook"></i></a></li>
                <li><a href=""><i class="fab fa-instagram"></i></a></li>
                <li><a href=""><i class="fab fa-twitter-square"></i></a></li>
            </ul>
        </div>

    </div>
</div>
<div class="col-lg-2 col-md-3">
    <h5 class="widget-title mb-30 wow fadeIn animated" style="visibility: visible;">MENU</h5>
    <ul class="footer-list wow fadeIn  mb-sm-5 mb-md-0  animated" style="visibility: visible;">
            <li>
            <a href="">
                 <span>Home</span>
            </a>
                    </li>
            <li>
            <a href="">
                 <span>About</span>
            </a>
                    </li>
            <li>
            <a href="">
                 <span>Built a Boat</span>
            </a>
                    </li>
            <li>
            <a href="">
                 <span>Accessories</span>
            </a>
                    </li>
            <li>
            <a href="">
                 <span>Reviews</span>
            </a>
                    </li>
    </ul>

</div>
<div class="col-lg-3 col-md-3">
    <h5 class="widget-title mb-30 wow fadeIn   animated" style="visibility: visible;">DISCOVER</h5>
    <ul class="footer-list wow fadeIn  mb-sm-5 mb-md-0  animated" style="visibility: visible;">
            <li>
            <a href="">
                 <span>FAQ's</span>
            </a>
                    </li>
            <li>
            <a href="">
                 <span>Contact Us</span>
            </a>
                    </li>
            <li>
            <a href="">
                 <span>Privacy Policy</span>
            </a>
                    </li>
            <li>
            <a href="">
                 <span>Refund and Return Policy</span>
            </a>
                    </li>
            
    </ul>

</div>
<div class="col-lg-4">
    <h5 class="widget-title mb-10 wow fadeIn   animated" style="visibility: visible;">NEWSLETTER</h5>
    <div class="row">
        <div class="col-md-4 col-lg-12">
            <p class="mb-30 wow fadeIn  mt-md-3  animated" style="visibility: visible;">Sign Up For Exclusive Offers From Us</p>
            <form class="newsletter-form" method="post" action="{{ route('public.newsletter.subscribe') }}">
                    @csrf
                    <div class="form-subcriber d-flex wow fadeIn animated ">
                        <input type="email" name="email" class="form-control bg-white font-small" placeholder="{{ __('Enter your email') }}">
                        <button class="btn bg-light subscribe" type="submit">{{ __('Subscribe') }}</button>
                    </div>
                    @if (setting('enable_captcha') && is_plugin_active('captcha'))
                        <div class="col-auto">
                            {!! Captcha::display() !!}
                        </div>
                    @endif
                </form>
                    </div>
    </div>
</div>

                </div>
            </div>
        </section>
        <div class="container pb-20 wow fadeIn animated">
            <div class="row">
                <div class="col-12 mb-20">
                    <div class="footer-bottom"></div>
                </div>
                <div class="col-lg-6">
                    <p class="float-md-left font-sm mb-0">2022 Ocean Boats. All rights reserved.</p>
                </div>
                <div class="col-lg-6">
                    <p class="text-lg-end text-start font-sm mb-0">
                        Made with <i class="fa fa-heart" style="color:red"></i> Wisdom
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
});

</script>
<div id="scrollUp"><i class="fal fa-long-arrow-up"></i></div>
</body>
</html>

@php
$title=BaseHelper::clean(__($config['name']));
$description=BaseHelper::clean(__($config['subtitle']));

@endphp
<div class="col-lg-4">
	<h5 class="widget-title wow fadeIn   animated" style="visibility: visible;">{!! BaseHelper::clean($title) !!}
	</h5>
	<div class="row">
        <div class="col-md-4 col-lg-12">
            <p class=" wow fadeIn  mt-md-3  animated" style="visibility: visible;">{!! BaseHelper::clean($description) !!}</p>
            <!-- Subscribe Form -->
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
                <!-- End Subscribe Form -->
        </div>
    </div>



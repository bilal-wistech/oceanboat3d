<section class="mt-50 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-12">
                <div class="contact-from-area  padding-20-row-col wow tmFadeInUp animated" id = "contact-form" style="visibility: visible;">
                    <h3 class="mb-50"><span class="billing">FEEL FREE </span>TO CONTACT US</h3>
                    <!--<div class="checkboxes mb-30">-->
                    <!--    <input type="checkbox" id="pleasure" name="option" value="1" checked>-->
                    <!--    <label for="pleasure">Pleasure</label>-->
                    <!--    <input type="checkbox" id="commercial" name="option" value="2">-->
                    <!--    <label for="commercial">Commercial</label>-->
                    <!--</div>-->
                 <div class="checkboxes mb-30">
                        <input type="checkbox" id="pleasure" name="option" value="1" checked class="d-none">
                        <label for="pleasure">Pleasure</label>
                        <input type="checkbox" id="commercial" name="option" value="2" class="d-none">
                        <label for="commercial">Commercial</label>
                    </div>
                    {!! Form::open(['route' => 'public.send.contact', 'class' => 'contact-form-style text-center contact-form', 'method' => 'POST']) !!}
                        {!! apply_filters('pre_contact_form', null) !!}
                        <div id="option1" class="option">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-style mb-20">
                                    <input name="name" value="{{ old('name') }}" placeholder="{{ __('Name') }}" type="text">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="input-style mb-20">
                                    <input name="phone" value="{{ old('phone') }}" placeholder="{{ __('Phone') }}" type="tel">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="input-style mb-20">
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}">
                                </div>
                            </div>
                            <input type="hidden" name="type" value="pleasure">
                            <!-- <div class="col-lg-12 col-md-12">
                                <div class="input-style mb-20">
                                    <input name="address" value="{{ old('address') }}" placeholder="{{ __('Address') }}" type="text">
                                </div>
                            </div> -->
            
                            <!-- <div class="col-lg-12 col-md-12">
                                <div class="input-style mb-20">
                                    <input name="subject" value="{{ old('subject') }}" placeholder="{{ __('Subject') }}" type="text">
                                </div>
                            </div> -->
                            <div class="col-lg-12 col-md-12">
                                <div class="textarea-style">
                                    <textarea name="content" placeholder="{{ __('Message') }}">{{ old('content') }}</textarea>
                                </div>

                                @if (is_plugin_active('captcha'))
                                    @if (setting('enable_captcha'))
                                        <div class="col-md-12">
                                            {!! Captcha::display() !!}
                                        </div>
                                    @endif

                                    @if (setting('enable_math_captcha_for_contact_form', 0))
                                        <div class="col-md-12 text-left">
                                            <label for="math-group">{{ app('math-captcha')->label() }}</label>
                                            {!! app('math-captcha')->input(['class' => 'form-control', 'id' => 'math-group']) !!}
                                        </div>
                                    @endif
                                @endif

                                {!! apply_filters('after_contact_form', null) !!}
                                <button class="submit submit-auto-width mt-30" type="submit">{{ __('Send message') }}</button>
                            </div>
                             <div class="form-group">
                            <div class="contact-message contact-success-message mt-30 alert alert-success" role="alert" style="display: none"></div>
                            <div class="contact-message contact-error-message mt-30" style="display: none"></div>
                        </div>
                        </div>
                        </div>
                        {!! Form::close() !!}
                        {!! Form::open(['route' => 'public.send.contact', 'class' => 'contact-form-style text-center contact-form', 'method' => 'POST']) !!}
                        <div id="option2" class="option">
                        <div class="row">
                        <div class="col-12">
                                <div class="input-style mb-20">
                                    <input name="name" value="{{ old('name') }}" placeholder="{{ __('Company Name') }}" type="text">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="input-style mb-20">
                                    <input name="phone" value="{{ old('phone') }}" placeholder="{{ __('Phone') }}" type="tel">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="input-style mb-20">
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}">
                                </div>
                            </div>
                            <input type="hidden" name="type" value="commercial">
                            <div class="col-lg-12 col-md-12">
                                <div class="textarea-style">
                                    <textarea name="content" placeholder="{{ __('Boats of interest') }}">{{ old('content') }}</textarea>
                                </div>

                                @if (is_plugin_active('captcha'))
                                    @if (setting('enable_captcha'))
                                        <div class="col-md-12">
                                            {!! Captcha::display() !!}
                                        </div>
                                    @endif

                                    @if (setting('enable_math_captcha_for_contact_form', 0))
                                        <div class="col-md-12 text-left">
                                            <label for="math-group">{{ app('math-captcha')->label() }}</label>
                                            {!! app('math-captcha')->input(['class' => 'form-control', 'id' => 'math-group']) !!}
                                        </div>
                                    @endif
                                @endif

                                {!! apply_filters('after_contact_form', null) !!}
                                <button class="submit submit-auto-width mt-30" type="submit">{{ __('Send message') }}</button>
                            </div>
                        </div>
                        </div>
                        {!! Form::close() !!}
                      
                    <!-- {!! Form::close() !!} -->
                </div>
            </div>

            <div class="col-lg-5 mt-50 bg-grey">
            <div class="Address-form"> 
            @if (theme_option('address') || theme_option('phone') || theme_option('working_hours'))
            <h4 class="mt-20 mb-10 fw-600 wow fadeIn animated">{{ __('Address') }}</h4>
            @if (theme_option('address'))
                <p class="wow fadeIn mb-40 animated text-black">
                {{ theme_option('address') }}
                </p>
            @endif
            <h4 class="mt-20 mb-10 fw-600 wow fadeIn animated">{{ __('Information') }}</h4>
            @if (theme_option('phone'))
                <p class="wow fadeIn animated d-inline-block text-black">
                  {{ theme_option('phone') }}
                </p>
            @endif
                <!--    @if (theme_option('contact_email'))-->
                <!--    <p class="wow fadeIn animated text-black mb-40">-->
                <!--        {{ theme_option('contact_email') }}-->
                <!--    </p>-->
                <!--@endif-->
                <h4 class="mt-20 mb-10 fw-600 wow fadeIn animated">{{ __('Follow us') }}</h4>
                <div class="social-icons d-flex mb-40">
                    <ul>
                        @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                            @if (count($socialLink) == 4)
                                <li><a href="{{ $socialLink[2]['value'] }}"><i class="{{ $socialLink[1]['value'] }}"></i></a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
           <h4 class="mt-20 mb-10 fw-600 wow fadeIn animated">{{ __('Working Hours') }}</h4>
            @if (theme_option('working_hours'))
                <p class="wow fadeIn animated text-black">
                   {{ theme_option('working_hours') }}
                </p>
            @endif
            
        @endif  
</div>
</div>  
     <!-- @if (theme_option('contact_info_boxes'))
   
        <div class="col-lg-4">
            @foreach(json_decode(theme_option('contact_info_boxes'), true) as $item)
                @if (count($item) == 4)
                   
                        <h4 class="mb-15 text-muted">{!! BaseHelper::clean($item[0]['value']) !!}</h4>
                        {!! BaseHelper::clean($item[1]['value']) !!}<br>
                        <abbr title="{{ __('Phone') }}">{{ __('Phone') }}:</abbr> {!! BaseHelper::clean($item[2]['value']) !!}<br>
                        <abbr title="{{ __('Email') }}">{{ __('Email') }}: </abbr>{!! BaseHelper::clean($item[3]['value']) !!}<br>
                        <a class="btn btn-outline btn-sm btn-brand-outline font-weight-bold text-brand bg-white text-hover-white mt-20 border-radius-5 btn-shadow-brand hover-up" href="https://maps.google.com/?q={{ urlencode(clean($item[1]['value'])) }}"><i class="fa fa-map text-muted mr-15"></i>{{ __('View map') }}</a>
                   
                @endif
            @endforeach
        </div>
   
    <hr>
@endif -->

        </div>
    </div>
</section>
<style>
.option {
  display: none;
}
#pleasure {
  display: block;
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    jQuery(function ($) {
        var offset = $('#contact-form').offset().top - 100;
        $('html, body').animate({
            scrollTop: offset
        }, 'slow');
    });
</script>
<div class="col-lg-4 col-md-6">
    <div class="widget-about font-md mb-md-5 mb-lg-0">
        @if (theme_option('logo'))
            <div class="logo logo-width-1 wow fadeIn  mb-10   animated">
                <a href="{{ route('public.index') }}">
                    <img src="{{ RvMedia::getImageUrl(theme_option('logo_light')) }}" alt="{{ theme_option('site_title') }}">
                </a>
            </div>
        @endif
        @if (theme_option('address') || theme_option('phone'))
            @if (theme_option('address'))
                <p class="wow fadeIn animated">
                    {{ theme_option('address') }}
                </p>
            @endif
            @if (theme_option('phone'))
                <p class="wow fadeIn animated">
                    <i class="fa-phone fas"></i>&nbsp; {{ theme_option('phone') }}
                </p>
            @endif
                @if (theme_option('contact_email'))
                    <p class="wow fadeIn animated">
                        <i class="fa-envelope fas"></i>&nbsp; {{ theme_option('contact_email') }}
                    </p>
                @endif
        @endif
        @if (theme_option('social_links'))
        <div class="social-icons d-flex mt-30">
            <ul>
                @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                    @if (count($socialLink) == 4 && $socialLink[1]['value'] && $socialLink[2]['value'])
                        <li><a href="{{ $socialLink[2]['value'] }}" target="_blank"><i class="{{ $socialLink[1]['value'] }}"></i></a></li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

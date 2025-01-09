<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- maryam -->
        <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/3.1.2/fullpage.min.css">
        <!-- end -->
        <link rel="preconnect" href="{{ $fontURL = config('core.base.general.google_fonts_url', 'https://fonts.bunny.net') }}">
        <link href="{{ $fontURL }}/css2?family={{ urlencode(theme_option('font_text', 'Poppins')) }}:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
        
        <style>
            :root {
                --font-text: {{ theme_option('font_text', 'Poppins') }}, sans-serif;
                --color-brand: {{ theme_option('color_brand', '#5897fb') }};
                --color-brand-2: {{ theme_option('color_brand_2', '#3256e0') }};
                --color-primary: {{ theme_option('color_primary', '#3f81eb') }};
                --color-secondary: {{ theme_option('color_secondary', '#41506b') }};
                --color-warning: {{ theme_option('color_warning', '#ffb300') }};
                --color-danger: {{ theme_option('color_danger', '#ff3551') }};
                --color-success: {{ theme_option('color_success', '#3ed092') }};
                --color-info: {{ theme_option('color_info', '#18a1b7') }};
                --color-text: {{ theme_option('color_text', '#4f5d77') }};
                --color-heading: {{ theme_option('color_heading', '#222222') }};
                --color-grey-1: {{ theme_option('color_grey_1', '#111111') }};
                --color-grey-2: {{ theme_option('color_grey_2', '#242424') }};
                --color-grey-4: {{ theme_option('color_grey_4', '#90908e') }};
                --color-grey-9: {{ theme_option('color_grey_9', '#f4f5f9') }};
                --color-muted: {{ theme_option('color_muted', '#8e8e90') }};
                --color-body: {{ theme_option('color_body', '#4f5d77') }};
            }
        </style>

        {!! Theme::header() !!}

        @php
            $headerStyle = theme_option('header_style') ?: '';
            $page = Theme::get('page');
            if ($page) {
                $headerStyle = $page->getMetaData('header_style', true) ?: $headerStyle;
            }
            $headerStyle = ($headerStyle && in_array($headerStyle, array_keys(get_layout_header_styles()))) ? $headerStyle : '';
        @endphp
    </head>
    <body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif class="@if (BaseHelper::siteLanguageDirection() == 'rtl') rtl @endif header_full_true wowy-template css_scrollbar lazy_icons btnt4_style_2 zoom_tp_2 css_scrollbar template-index wowy_toolbar_true hover_img2 swatch_style_rounded swatch_list_size_small label_style_rounded wrapper_full_width header_full_true header_sticky_true hide_scrolld_true des_header_3 h_banner_true top_bar_true prs_bordered_grid_1 search_pos_canvas lazyload @if (Theme::get('bodyClass')) {{ Theme::get('bodyClass') }} @endif">
        {!! apply_filters(THEME_FRONT_BODY, null) !!}
        <div id="alert-container"></div>

        {!! Theme::partial('preloader') !!}

 <!-- maryam -->

    @php
    $crumbs=Theme::breadcrumb()->getCrumbs();
    $lastIndex = count($crumbs) - 1;
    @endphp
    <div class="section hero mob">
      <img class="w-100 h-100" src="{{ RvMedia::getImageUrl(isset($page) && isset($page->image) ? $page->image : 'bannerhome.jpg','400 x 400')}}" alt="Ocean Boats Banner Image">
      <div class="banner-text mob">
        <!-- <h1 class="heading1">{{(isset($page) && isset($page->name) ? $page->name : 'Accessories')}}</h1> -->
        <h1 class="heading1">{!! $crumbs[$lastIndex]['label'] !!}</h1>
        {!! Theme::breadcrumb()->render() !!}
      </div>
    </div>

       
 <header class="header-areac {{ $headerStyle }}">
        <div class="header-laptop">
        <div id="header-top1">
            <div class="header-bar-container">
                <div class="logo1">
                    <a href="{{ route('public.index') }}"><img
                            src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                            alt="{{ theme_option('site_title') }}"></a>
                </div>
                <div class="header-thin">
                    <ul class="header-navbar-nav">
                        <li><a href="{{url('contact')}}">Have any Questions?</a></li>
                        @if (theme_option('phone'))
                            <li><a href="">{{ theme_option('phone') }}</a></li>
                        @endif
                        <!--@if (theme_option('contact_email'))-->
                        <!--    <li><a href="">{{ theme_option('contact_email') }}</a></li>-->
                        <!--@endif-->
                        <li><form id="standard-3" action="{{ route('public.products') }}" id="form2">
                        <input type="text" class="search-txt-input" name="q" maxlength="100" placeholder="Search...">
                        <button type="submit" form="form2"  class="search-button">
                       <i class="fa fa-search"></i></button>
                       </form></li>
                    </ul>
                    <ul class="header-navbar-nav-right header-navbar-nav">
                        @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                            @if (count($socialLink) == 4)
                                <li><a href="{{ $socialLink[2]['value'] }}" target="_blank"><i class="{{ $socialLink[1]['value'] }}"></i></a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div class="line-break"></div>  
            </div>
        </div> 
        <div id="header-bottom1">
                <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block main-menu-light-white hover-boder hover-boder-white">
                            <nav>
                                {!!
                                    Menu::renderMenuLocation('main-menu', [
                                        'view' => 'main-menu',
                                    ])
                                !!}
                            </nav>
                        </div>
               
                <ul class="header-navbar-bottom-right header-navbar-bottom">
                <div class="header-action-2">
                    <div class="header-action-icon-2">
                        <a href="{{ route('public.wishlist') }}" class="wishlist-count">
                            <img class="svgInject" alt="{{ __('Wishlist') }}" src="{{ Theme::asset()->url('images/icons/icon-heart-white.svg') }}">
                                <span class="pro-count blue">@if (auth('customer')->check())<span>{{ auth('customer')->user()->wishlist()->count() }}</span> @else <span>{{ Cart::instance('wishlist')->count() }}</span>@endif</span>
                        </a>
                    </div>
                    <div class="header-action-icon-2">
                        <a class="mini-cart-icon" href="{{ route('public.cart') }}">
                            <img alt="{{ __('Cart') }}" src="{{ Theme::asset()->url('images/icons/icon-cart-white.svg') }}">
                                <span class="pro-count blue">{{ Cart::instance('cart')->count() }}</span>
                        </a>
                    </div>
                                    
                </div>
                  @if (auth('customer')->check())
                    <li><a href="{{ route('customer.overview') }}"><img alt="{{ __('Sign In') }}" src="{{ Theme::asset()->url('images/icons/icon-user-white.svg') }}" style="width: 18%;position: absolute;float: right;"></a></li>
                    @else
                    <li><a href="{{ route('customer.login') }}"><button type="button" class="btn login">Log In</button></li>
                    @endif
                
                </ul>
        </div>
        </div>
        <div class="header-mobile">
        <div id="header-top1">
            <div class="thin-header">
                    <ul class="header-navbar-nav">
                        <li><a href="">Have any Questions?</a></li>
                        @if (theme_option('phone'))
                            <li><a href="">{{ theme_option('phone') }}</a></li>
                        @endif
                        <!--@if (theme_option('contact_email'))-->
                        <!--    <li><a href="">{{ theme_option('contact_email') }}</a></li>-->
                        <!--@endif-->
                    </ul>
                    <ul class="header-navbar-nav-right header-navbar-nav">
                        @if (theme_option('social_links'))
                        @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                        <li><a href="{{ $socialLink[2]['value'] }}"><i class="{{ $socialLink[1]['value'] }}"></i></a></li>
                        @endforeach
                        @endif
                    </ul>
                
            </div>
        </div> 
        <div class="logo1">
                    <a href="{{ route('public.index') }}"><img
                            src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                            alt="{{ theme_option('site_title') }}"></a>
                </div>
        <div id="header-bottom1">
               <ul class="header-navbar-bottom-right header-navbar-bottom">
                <div class="header-action-2">
                    <div class="header-action-icon-2">
                        <a href="{{ route('public.wishlist') }}" class="wishlist-count">
                            <img class="svgInject" alt="{{ __('Wishlist') }}" src="{{ Theme::asset()->url('images/icons/icon-heart-white.svg') }}">
                                <span class="pro-count blue">@if (auth('customer')->check())<span>{{ auth('customer')->user()->wishlist()->count() }}</span> @else <span>{{ Cart::instance('wishlist')->count() }}</span>@endif</span>
                        </a>
                    </div>
                    <div class="header-action-icon-2">
                        <a class="mini-cart-icon" href="{{ route('public.cart') }}">
                            <img alt="{{ __('Cart') }}" src="{{ Theme::asset()->url('images/icons/icon-cart-white.svg') }}">
                                <span class="pro-count blue">{{ Cart::instance('cart')->count() }}</span>
                        </a>
                    </div>
                    <div class="header-action-icon-2">
                        <a href="{{ route('customer.login') }}">
                            <img alt="{{ __('Sign In') }}" src="{{ Theme::asset()->url('images/icons/icon-user-white.svg') }}">
                        </a>
                    </div>
                    <div class="header-action-icon-2">
                    <div class="burger-icon burger-icon-white">
                        <span class="burger-icon-top"></span>
                        <span class="burger-icon-mid"></span>
                        <span class="burger-icon-bottom"></span>
                    </div>
                </div>                 
                </div>
               
                </ul>
        </div>
        </div>
        
</div>

   <!-- end -->
            <div class="header-top header-top-ptb-1 d-none d-lg-block">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-xl-3 col-lg-4">
                            <div class="header-info">
                                <ul>
                                    @if (theme_option('hotline'))
                                        <li><i class="fa fa-phone-alt mr-5"></i><a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a></li>
                                    @endif

                                    @if (is_plugin_active('ecommerce') && EcommerceHelper::isOrderTrackingEnabled())
                                        <li><i class="far fa-anchor mr-5"></i><a href="{{ route('public.orders.tracking') }}">{{ __('Track Your Order') }}</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="col-xl-5 col-lg-4">
                            <div class="text-center">
                                @if (theme_option('header_messages'))
                                    <div id="news-flash" class="d-inline-block">
                                        <ul>
                                            @foreach(json_decode(theme_option('header_messages'), true) as $headerMessage)
                                                @if (count($headerMessage) == 4)
                                                    <li>
                                                        @if ($headerMessage[0]['value'])
                                                            <i class="{{ $headerMessage[0]['value'] }} d-inline-block mr-5"></i>
                                                        @endif

                                                        @if ($headerMessage[1]['value'])
                                                            <span class="d-inline-block">
                                                                {!! BaseHelper::clean($headerMessage[1]['value']) !!}
                                                            </span>
                                                        @endif
                                                        @if ($headerMessage[2]['value'] && $headerMessage[3]['value'])
                                                            <a class="active d-inline-block" href="{{ url($headerMessage[2]['value']) }}">{!! BaseHelper::clean($headerMessage[3]['value']) !!}</a>
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php $currencies = is_plugin_active('ecommerce') ? get_all_currencies() : []; @endphp

                        @if (is_plugin_active('ecommerce') || is_plugin_active('language'))
                            <div class="col-xl-4 col-lg-4">
                                <div class="header-info header-info-right">
                                        <ul>
                                            @if (is_plugin_active('language'))
                                                {!! Theme::partial('language-switcher') !!}
                                            @endif

                                            @if (is_plugin_active('ecommerce'))
                                                @if (count($currencies) > 1)
                                                    <li>
                                                        <a class="language-dropdown-active" href="#"> <i class="fa fa-coins"></i> {{ get_application_currency()->title }} <i class="fa fa-chevron-down"></i></a>
                                                        <ul class="language-dropdown">
                                                            @foreach ($currencies as $currency)
                                                                @if ($currency->id !== get_application_currency_id())
                                                                    <li><a href="{{ route('public.change-currency', $currency->title) }}">{{ $currency->title }}</a></li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endif
                                                @if (auth('customer')->check())
                                                    <li><a href="{{ route('customer.overview') }}">{{ auth('customer')->user()->name }}</a></li>
                                                @else
                                                    <li><a href="{{ route('customer.login') }}">{{ __('Log In / Sign Up') }}</a></li>
                                                @endif
                                            @endif
                                        </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
                <div class="container">
                    <div class="header-wrap header-space-between">
                        @if (theme_option('logo'))
                            <div class="logo logo-width-1">
                                <a href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}"></a>
                            </div>
                        @endif
                        @if (is_plugin_active('ecommerce'))
                            <div class="search-style-2">
                                <form action="{{ route('public.products') }}" method="get">
                                    <div class="form-group--icon">
                                        <div class="product-cat-label">{{ __('All Categories') }}</div>
                                        <select class="product-category-select" name="categories[]">
                                            <option value="">{{ __('All Categories') }}</option>
                                            {!! Theme::partial('product-categories-select', ['categories' => $categories, 'indent' => null]) !!}
                                        </select>
                                    </div>
                                    <input type="text" name="q" placeholder="{{ __('Search for items…') }}" autocomplete="off">
                                    <button type="submit"> <i class="far fa-search"></i> </button>
                                </form>
                            </div>
                            <div class="header-action-right">
                                <div class="header-action-2">
                                    @if (EcommerceHelper::isCompareEnabled())
                                        <div class="header-action-icon-2">
                                            <a href="{{ route('public.compare') }}" class="compare-count">
                                                <img class="svgInject" alt="{{ __('Compare') }}" src="{{ Theme::asset()->url('images/icons/icon-compare.svg') }}">
                                                <span class="pro-count blue"><span>{{ Cart::instance('compare')->count() }}</span></span>
                                            </a>
                                        </div>
                                    @endif
                                    @if (EcommerceHelper::isWishlistEnabled())
                                        <div class="header-action-icon-2">
                                            <a href="{{ route('public.wishlist') }}" class="wishlist-count">
                                                <img class="svgInject" alt="{{ __('Wishlist') }}" src="{{ Theme::asset()->url('images/icons/icon-heart.svg') }}">
                                                <span class="pro-count blue">@if (auth('customer')->check())<span>{{ auth('customer')->user()->wishlist()->count() }}</span> @else <span>{{ Cart::instance('wishlist')->count() }}</span>@endif</span>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="header-action-icon-2">
                                        <a class="mini-cart-icon" href="{{ route('public.cart') }}">
                                            <img alt="{{ __('Cart') }}" src="{{ Theme::asset()->url('images/icons/icon-cart.svg') }}">
                                            <span class="pro-count blue">{{ Cart::instance('cart')->count() }}</span>
                                        </a>
                                        <div class="cart-dropdown-wrap cart-dropdown-hm2">
                                            {!! Theme::partial('cart-panel') !!}
                                        </div>
                                    </div>
                                    <div class="header-action-icon-2">
                                        <a href="{{ route('customer.login') }}">
                                            <img alt="{{ __('Sign In') }}" src="{{ Theme::asset()->url('images/icons/icon-user.svg') }}">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </header>
    
        </div>
        <div class="mobile-header-active mobile-header-wrapper-style">
            <div class="mobile-header-wrapper-inner">
                <div class="mobile-header-top">
                    @if (theme_option('logo'))
                        <div class="mobile-header-logo">
                            <a href="{{ route('public.index') }}"><img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}"></a>
                        </div>
                    @endif
                    <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                        <button class="close-style search-close">
                            <i class="icon-top"></i>
                            <i class="icon-bottom"></i>
                        </button>
                    </div>
                </div>
                @if (is_plugin_active('ecommerce'))
                    <div class="mobile-header-content-area">
                    <div class="mobile-search search-style-3 mobile-header-border">
                        <form action="{{ route('public.products') }}">
                            <input type="text" name="q" placeholder="{{ __('Search...') }}">
                            <button type="submit"> <i class="far fa-search"></i> </button>
                        </form>
                    </div>
                    <!-- maryam -->
                    <div class="mobile-menu-wrap mobile-header-border">
                    <!-- mobile menu start -->
                    <!-- mobile menu start -->
                    <nav>
                            {!!
                                Menu::renderMenuLocation('main-menu', [
                                    'options' => ['class' => 'mobile-menu'],
                                    'view'    => 'mobile-menu',
                                ])
                            !!}
                        </nav>
                        <!-- mobile menu end -->
                        <!-- mobile menu end -->
                    </div>
                    

                    @if (theme_option('social_links'))
                        <div class="mobile-social-icon">
                    @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                    @if (count($socialLink) == 4 && $socialLink[1]['value'] && $socialLink[2]['value'])
                        <li><a href="{{ $socialLink[2]['value'] }}" target="_blank"><i class="{{ $socialLink[1]['value'] }}"></i></a></li>
                    @endif
                @endforeach
            </ul>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>


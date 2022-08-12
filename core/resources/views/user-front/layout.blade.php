<!DOCTYPE html>
<html lang="en" @if ($userCurrentLang->rtl == 1) dir="rtl" @endif>

<head>
    <!--====== Required meta tags ======-->
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="@yield('meta-description')">
    <meta name="keywords" content="@yield('meta-keywords')">

    @yield('og-meta')
    <!--====== Title ======-->
    <title> {{ convertUtf8($user->username) }} - @yield('tab-title') </title>
    @includeIf('user-front.partials.styles')
    @if ($userBs->whatsapp_status == 1)
        <style>
            .back-to-top {
                left: 10px;
            }

        </style>
    @endif
</head>

<body class="@if ($userBs->theme == 'home_five') dark-version @endif ">
    <!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade
    your browser</a> to improve your experience and security.</p>
<![endif]-->

    <!--====== Preloader ======-->
    @if (!empty($userBs->preloader))
        <div id="preloader">
            <div class="loader-cubes">
                <img src="{{ asset('assets/front/img/user/' . $userBs->preloader) }}" alt="">
            </div>
        </div>
    @endif

    @if ($userBs->theme === 'home_two')
        @includeIf('user-front.partials.header_two')
    @elseif($userBs->theme === 'home_three')
        @includeIf('user-front.partials.header_three')
    @elseif($userBs->theme === 'home_four')
        @includeIf('user-front.partials.header_four')
    @elseif($userBs->theme === 'home_five')
        @includeIf('user-front.partials.header_five')
    @elseif($userBs->theme === 'home_six')
        @includeIf('user-front.partials.header_six')
    @elseif($userBs->theme === 'home_seven')
        @includeIf('user-front.partials.header_seven')
    @else
        @includeIf('user-front.partials.header')
    @endif

    @if (!request()->routeIs('front.user.detail.view'))
        @php
            $brBg = $userBs->breadcrumb ?? 'breadcrumb.jpg';
        @endphp
        <!--====== Breadcrumb part Start ======-->
        <section class="breadcrumb-section bg-img-c lazy
    @if ($userBs->theme === 'home_three') breadcrumb-3 @endif"
            data-bg="{{ asset('assets/front/img/user/' . $brBg) }}">
            <div class="container">
                <div class="breadcrumb-text">
                    <h1 class="page-title">@yield('page-name')</h1>
                    <ul>
                        <li>
                            <a
                                href="{{ route('front.user.detail.view', getParam()) }}">{{ $keywords['Home'] ?? 'Home' }}</a>
                        </li>
                        <li>@yield('br-name')</li>
                    </ul>
                </div>
            </div>
            <div class="breadcrumb-shapes">
                <div class="one"></div>
                <div class="two"></div>
            </div>
        </section>
        <!--====== Breadcrumb part End ======-->
    @endif

    @yield('content')

    @if ($userBs->theme == 'home_two')
        @includeIf('user-front.partials.footer_two')
    @elseif($userBs->theme == 'home_three')
        @includeIf('user-front.partials.footer_three')
    @elseif($userBs->theme == 'home_four')
        @includeIf('user-front.partials.footer_four')
    @elseif($userBs->theme == 'home_five')
        @includeIf('user-front.partials.footer_five')
    @elseif($userBs->theme == 'home_six')
        @includeIf('user-front.partials.footer_six')
    @elseif($userBs->theme == 'home_seven')
        @includeIf('user-front.partials.footer_seven')
    @else
        @includeIf('user-front.partials.footer')
    @endif

    @if ($userBs->whatsapp_status == 1)
        {{-- WhatsApp Chat Button --}}
        <div id="WAButton"></div>
    @endif

    @includeIf('user-front.partials.scripts')


</body>

</html>

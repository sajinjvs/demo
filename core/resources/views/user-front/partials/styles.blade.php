<!--====== Favicon Icon ======-->
<link rel="shortcut icon"
    href="{{ !empty($userBs->favicon) ? asset('assets/front/img/user/' . $userBs->favicon) : '' }}" type="img/png" />
<!--====== Animate Css ======-->
<link rel="stylesheet" href="{{ asset('assets/front/user/css/animate.min.css') }}">
<!--====== Bootstrap css ======-->
<link rel="stylesheet" href="{{ asset('assets/front/user/css/bootstrap.min.css') }}" />
<!--====== Fontawesome css ======-->
<link rel="stylesheet" href="{{ asset('assets/front/user/css/font-awesome.min.css') }}" />
<!--====== Flaticon css ======-->
<link rel="stylesheet" href="{{ asset('assets/front/user/css/flaticon.css') }}" />
<!--====== Magnific Popup css ======-->
<link rel="stylesheet" href="{{ asset('assets/front/user/css/magnific-popup.css') }}" />
<!--====== Slick  css ======-->
<link rel="stylesheet" href="{{ asset('assets/front/user/css/slick.css') }}" />
<!--====== Toastr CSS ======-->
<link rel="stylesheet" href="{{ asset('assets/front/css/toastr.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/front/user/css/owl.carousel.min.css') }}" />
<!--====== Whatsapp  css ======-->
<link rel="stylesheet" href="{{ asset('assets/front/user/css/whatsapp.min.css') }}" />
<!--====== Jquery ui ======-->
<link rel="stylesheet" href="{{ asset('assets/front/user/css/jquery-ui.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/admin/css/jquery.timepicker.min.css') }}">
{{-- version 4,5,6 --}}

@if ($userBs->theme === 'home_five' || $userBs->theme === 'home_four')
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/common.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme45.css') }}" />
    @if ($userCurrentLang->rtl == 1)
        <!--====== RTL-Commonn Css ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/common-rtl.css') }}" />
        <!--====== RTL-Main Css ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme4-5/rtl-style.css') }}" />
        <!--====== RTL-Responsive CSS ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme4-5/rtl-responsive.css') }}" />
    @endif
@elseif($userBs->theme === 'home_six')
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/common.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme6.css') }}" />
    @if ($userCurrentLang->rtl == 1)
        <!--====== RTL-Commonn Css ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/common-rtl.css') }}" />
        <!--====== RTL-Main Css ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme6/rtl-style.css') }}" />
        <!--====== RTL-Responsive CSS ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme6/rtl-responsive.css') }}" />
    @endif
@elseif($userBs->theme === 'home_seven')
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/common.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme7.css') }}" />
    @if ($userCurrentLang->rtl == 1)
        <!--====== RTL-Commonn Css ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/common-rtl.css') }}" />
        <!--====== RTL-Main Css ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme7/rtl-style.css') }}" />
        <!--====== RTL-Responsive CSS ======-->
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/theme7/rtl-responsive.css') }}" />
    @endif
@else
    <link rel="stylesheet" href="{{ asset('assets/front/user/css/style.css') }}" />
    @if ($userCurrentLang->rtl == 1)
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/rtl-style.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/front/user/css/rtl-responsive.css') }}" />
    @endif
@endif
<!--====== Style css ======-->



<!--====== Base color ======-->
@php
if (!empty($userBs->base_color)) {
    $baseColor = $userBs->base_color;
} else {
    $baseColor = '';
}

@endphp

<link rel="stylesheet" href="{{ asset('assets/front/user/css/base-color.php?color=' . $baseColor) }}">


@if ($userBs->tawkto_status == 1)
    <style>
        div#WAButton {
            bottom: 130px;
        }

    </style>
@endif

@yield('styles')

<style>
    {!! $userBs->custom_css !!}

</style>

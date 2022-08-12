<!--====== Header part start ======-->
<header class="sticky-header">
    <!-- Header Menu  -->
    <div class="header-nav">
        <div class="container-fluid container-1600">
            <div class="nav-container">
                @if (isset($userBs->logo))
                    <!-- Site Logo -->
                    <div class="site-logo">
                        <a href="{{ route('front.user.detail.view', getParam()) }}">
                            <img class="lazy"
                                data-src="{{ asset('assets/front/img/user/' . $userBs->logo) }}" alt="Logo">
                        </a>
                    </div>
                @endif

                <!-- Main Menu -->
                <div class="nav-menu d-lg-flex align-items-center">

                    <!-- Navbar Close Icon -->
                    <div class="navbar-close">
                        <div class="cross-wrap"><span></span><span></span></div>
                    </div>

                    <!-- Mneu Items -->
                    <div class="menu-items">
                        <ul>
                            @php
                                $links = json_decode($userMenus, true);
                            @endphp
                            @foreach ($links as $link)
                                @php
                                    $href = getUserHref($link);
                                @endphp
                                @if (!array_key_exists('children', $link))
                                    <li><a href="{{ $href }}"
                                            target="{{ $link['target'] }}">{{ $link['text'] }}</a></li>
                                @else
                                    <li class="has-submemu">
                                        <a href="{{ $href }}"
                                            target="{{ $link['target'] }}">{{ $link['text'] }}</a>
                                        <ul class="submenu">
                                            @foreach ($link['children'] as $level2)
                                                @php
                                                    $l2Href = getUserHref($level2);
                                                @endphp
                                                <li><a href="{{ $l2Href }}"
                                                        target="{{ $level2['target'] }}">{{ $level2['text'] }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <!-- Pushed Item -->
                    <div class="nav-pushed-item"></div>
                </div>

                <!-- Navbar Extra  -->
                <div class="navbar-extra d-flex align-items-center">
                    <!-- language selection -->
                    <form action="{{ route('changeUserLanguage', getParam()) }}" id="userLangForms">
                        @csrf
                        <input type="hidden" name="username" value="{{ $user->username }}">
                        <input type="hidden" name="code" id="lang-code" value="">
                        <div class="language-selection language-selection-two">
                            @if ($userCurrentLang->id)
                                <a class="language-btn" href="javascript:void(0)">
                                    {{ convertUtf8($userCurrentLang->name) }}
                                    <i class="far fa-angle-down"></i>
                                </a>
                            @endif
                            <ul class="language-list" id="language-list">
                                @foreach ($userLangs as $userLang)
                                    <li><a href="javascript:void(0)"
                                            data-value="{{ $userLang->code }}">{{ convertUtf8($userLang->name) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </form>
                    <!-- Navbtn -->
                    @if (in_array('Request a Quote', $packagePermissions))
                        @if ($userBs->is_quote)
                            <div class="navbar-btn nav-push-item">
                                <a class="main-btn main-btn-3"
                                    href="{{ route('front.user.quote', getParam()) }}">{{ $keywords['Request_A_Quote'] ?? 'Request A Quote' }}</a>
                            </div>
                        @endif
                    @endif
                    <!-- Navbar Toggler -->
                    <div class="navbar-toggler">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!--====== Header part end ======-->

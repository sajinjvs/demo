   <!--====== Start Header ======-->
   <header class="template-header absolute-header sticky-header">
       <div class="container-fluid container-1550">
           <div class="header-inner">
               <div class="header-left">
                    @if (isset($userBs->logo))
                        <div class="site-logo">
                            <a href="{{ route('front.user.detail.view', getParam()) }}">
                                <img class="lazy" data-src="{{ asset('assets/front/img/user/' . $userBs->logo) }}"
                                    alt="logo">
                            </a>
                        </div>
                    @endif
               </div>
               <div class="header-center">
                   <nav class="nav-menu d-none d-xl-block">
                       <ul class="primary-menu">
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
                                   <li>
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
                   </nav>
               </div>
               <div class="header-right">
                   <ul class="header-extra">
                       @if (in_array('Request a Quote', $packagePermissions))
                           @if ($userBs->is_quote)
                               <li class="header-btns d-none d-md-block">
                                   <a href="{{ route('front.user.quote', getParam()) }}" class="template-btn">
                                       {{ $keywords['Request_A_Quote'] ?? 'Request A Quote' }}
                                       <i class="far fa-long-arrow-right"></i>
                                   </a>
                               </li>
                           @endif
                       @endif
                       <li class=" d-xl-block">
                           <form action="{{ route('changeUserLanguage', getParam()) }}" id="userLangForms">
                               @csrf
                               <input type="hidden" name="username" value="{{ $user->username }}">
                               <select onchange="submit()" name="code" id="lang-code" class="form-control form-control-sm">
                                   @foreach ($userLangs as $userLang)
                                       <option {{ $userCurrentLang->id == $userLang->id ? 'selected' : '' }}
                                           value="{{ $userLang->code }}">{{ convertUtf8($userLang->name) }}
                                       </option>
                                   @endforeach
                               </select>
                           </form>
                       </li>
                       <li class="d-xl-none">
                           <div class="navbar-toggler">
                               <span></span>
                               <span></span>
                               <span></span>
                           </div>
                       </li>
                   </ul>
               </div>
           </div>
       </div>
       <!-- Mobile Menu -->
       <div class="slide-panel mobile-slide-panel">
           <div class="panel-overlay"></div>
           <div class="panel-inner">

               <nav class="mobile-menu">
                   <ul class="primary-menu">
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
                               <li>
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
                       @if (in_array('Request a Quote', $packagePermissions))
                            @if ($userBs->is_quote)
                                <li class=" d-block d-md-none"><a href="{{ route('front.user.quote', getParam()) }}">{{ $keywords['Request_A_Quote'] ?? 'Request A Quote' }}</a></li>
                            @endif
                        @endif
                   </ul>
               </nav>
               <a href="#" class="panel-close">
                   <i class="fal fa-times"></i>
               </a>
           </div>
       </div>
   </header>
   <!--====== End Header ======-->

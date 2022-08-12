<?php

namespace App\Providers;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\FooterQuickLink;
use App\Models\User\FooterText;
use App\Models\User\UserContact;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Social;
use App\Models\Language;
use App\Models\User\Language as UserLanguage;
use App\Models\Menu;
use App\Models\User;
use App\Models\User\BasicSetting;
use App\Models\User\Menu as UserMenu;
use App\Models\User\SEO;
use App\Models\User\UserPermission;
use App\Models\User\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        if (!app()->runningInConsole()) {
            $socials = Social::orderBy('serial_number', 'ASC')->get();
            $langs = Language::all();
    
            View::composer('*', function ($view)
            {
                if (session()->has('lang')) {
                    $currentLang = Language::where('code', session()->get('lang'))->first();
                } else {
                    $currentLang = Language::where('is_default', 1)->first();
                }
    
                $bs = $currentLang->basic_setting;
                $be = $currentLang->basic_extended;
    
                if (Menu::where('language_id', $currentLang->id)->count() > 0) {
                    $menus = Menu::where('language_id', $currentLang->id)->first()->menus;
                } else {
                    $menus = json_encode([]);
                }
    
                if ($currentLang->rtl == 1) {
                    $rtl = 1;
                } else {
                    $rtl = 0;
                }
    
                $view->with('bs', $bs );
                $view->with('be', $be );
                $view->with('currentLang', $currentLang );
                $view->with('menus', $menus );
                $view->with('rtl', $rtl );
            });
    
            View::composer(['user.*'], function ($view)
            {
                if (Auth::check()) {
                    $userBs = DB::table('user_basic_settings')->where('user_id', Auth::user()->id)->first();
                    $view->with('userBs', $userBs );
                }
            });
    
            View::composer(['user-front.*'], function ($view)
            {
                $user = getUser();
    
                if (session()->has('user_lang')) {
                    $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
                    if (empty($userCurrentLang)) {
                        $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                        session()->put('user_lang', $userCurrentLang->code);
                    }
                } else {
                    $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                }
    
                $keywords = json_decode($userCurrentLang->keywords, true);
    
                if (UserMenu::where('language_id', $userCurrentLang->id)->where('user_id', $user->id)->count() > 0) {
                    $userMenus = UserMenu::where('language_id', $userCurrentLang->id)->where('user_id', $user->id)->first()->menus;
                } else {
                    $userMenus = json_encode([]);
                }
    
    
                $userBs = BasicSetting::where('user_id', $user->id)->first();
                $social_medias = $user->social_media()->get() ?? collect([]);
                $userSeo = SEO::where('language_id', $userCurrentLang->id)->where('user_id', $user->id)->first();
                $userLangs = UserLanguage::where('user_id', $user->id)->get();
    
    
                $packagePermissions = UserPermissionHelper::packagePermission($user->id);
                $packagePermissions = json_decode($packagePermissions, true);
    
                $footerData = FooterText::where('language_id', $userCurrentLang->id)
                                        ->where('user_id',$user->id)
                                        ->first();

                if ($userBs->theme == 'home_seven') {
                    $fservices = UserService::where('lang_id', $userCurrentLang->id)
                        ->where('user_id',$user->id)
                        ->get();
                }

                $footerQuickLinks = FooterQuickLink::where('language_id', $userCurrentLang->id)
                    ->where('user_id',$user->id)
                    ->orderBy('serial_number', 'asc')
                    ->get();
    
                $footerRecentBlogs = User\Blog::query()
                    ->where('user_id', $user->id)
                    ->where('language_id', $userCurrentLang->id)
                    ->orderBy('id', 'DESC')
                    ->limit(3)
                    ->get();
                $userContact = UserContact::where([
                    ['user_id', $user->id],
                    ['language_id', $userCurrentLang->id]
                ])->first();
    
                $home_text = User\HomePageText::query()
                    ->where([
                        ['user_id', $user->id],
                        ['language_id', $userCurrentLang->id]
                    ])->first();
                $home_sections = User\HomeSection::where('user_id', $user->id)->first();
    
                $view->with('user', $user );
                $view->with('home_text', $home_text);
                $view->with('home_sections', $home_sections);
                $view->with('userSeo', $userSeo );
                $view->with('userBs', $userBs );
                $view->with('userMenus', $userMenus);
                $view->with('userFooterQuickLinks', $footerQuickLinks);
                $view->with('userFooterData', $footerData);
                $view->with('userFooterRecentBlogs', $footerRecentBlogs);
                $view->with('userContact', $userContact);
                $view->with('social_medias', $social_medias );
                $view->with('userCurrentLang', $userCurrentLang );
                $view->with('userLangs', $userLangs );
                $view->with('keywords', $keywords );
                $view->with('packagePermissions', $packagePermissions );
                if ($userBs->theme == 'home_seven') {
                    $view->with('fservices', $fservices );
                }
            });
    
            View::share('langs', $langs);
            View::share('socials', $socials);
        }
    }
}

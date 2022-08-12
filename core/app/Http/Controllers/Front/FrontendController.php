<?php

namespace App\Http\Controllers\Front;

require_once 'core/vendor/Transliterator/Transliterator.php';
require_once 'core/vendor/vcard/VCard.php';

use Config;
use Validator;
use Carbon\Carbon;
use App\Models\Faq;
use App\Models\Seo;
use App\Models\Blog;
use App\Models\Page;
use App\Models\User;
use App\Models\Feature;
use App\Models\Package;
use App\Models\Partner;
use App\Models\Process;
use App\Models\Language;
use App\Models\Bcategory;
use App\Models\Subscriber;
use App\Models\User\Quote;
use App\Models\Testimonial;
use App\Models\BasicSetting;
use Illuminate\Http\Request;
use App\Models\BasicExtended;
use App\Models\OfflineGateway;
use App\Models\PaymentGateway;
use App\Models\User\UserVcard;
use App\Models\User\HeroSlider;
use App\Models\User\QuoteInput;
use App\Http\Helpers\MegaMailer;
use App\Models\User\UserContact;
use App\Models\User\HomePageText;
use JeroenDesloovere\VCard\VCard;
use App\Models\BasicSetting as BS;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use App\Models\BasicExtended as BE;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User\UserCustomDomain;
use App\Models\User\PortfolioCategory;
use Illuminate\Support\Facades\Session;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\Language as UserLanguage;

class FrontendController extends Controller
{
    public function __construct()
    {
        $bs = BS::first();
        $be = BE::first();

        Config::set('captcha.sitekey', $bs->google_recaptcha_site_key);
        Config::set('captcha.secret', $bs->google_recaptcha_secret_key);
        Config::set('mail.host', $be->smtp_host);
        Config::set('mail.port', $be->smtp_port);
        Config::set('mail.username', $be->smtp_username);
        Config::set('mail.password', $be->smtp_password);
        Config::set('mail.encryption', $be->encryption);
    }
    public function index()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $lang_id = $currentLang->id;
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;

        $data['processes'] = Process::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
        $data['features'] = Feature::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
        $data['featured_users'] = User::where([
            ['featured', 1],
            ['status', 1]
        ])
            ->whereHas('memberships', function ($q) {
                $q->where('status', '=', 1)
                    ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'));
            })->get();

        $data['templates'] = User::where([
            ['preview_template', 1],
            ['status', 1],
            ['online_status', 1]
        ])
            ->whereHas('memberships', function ($q) {
                $q->where('status', '=', 1)
                    ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'));
            })->orderBy('template_serial_number', 'ASC')->get();

        $data['testimonials'] = Testimonial::where('language_id', $lang_id)
            ->orderBy('serial_number', 'ASC')
            ->get();
        $data['blogs'] = Blog::where('language_id', $lang_id)->orderBy('id', 'DESC')->take(2)->get();

        $data['packages'] = Package::query()->where('status', '1')->where('featured', '1')->get();
        $data['partners'] = Partner::where('language_id', $lang_id)
            ->orderBy('serial_number', 'ASC')
            ->get();

        $data['seo'] = Seo::where('language_id', $lang_id)->first();

        $terms = [];
        if (Package::query()->where('status', '1')->where('featured', '1')->where('term', 'monthly')->count() > 0) {
            $terms[] = 'Monthly';
        }
        if (Package::query()->where('status', '1')->where('featured', '1')->where('term', 'yearly')->count() > 0) {
            $terms[] = 'Yearly';
        }
        if (Package::query()->where('status', '1')->where('featured', '1')->where('term', 'lifetime')->count() > 0) {
            $terms[] = 'Lifetime';
        }
        $data['terms'] = $terms;

        $be = BasicExtended::select('package_features')->firstOrFail();
        $allPfeatures = $be->package_features ? $be->package_features : "[]";
        $data['allPfeatures'] = json_decode($allPfeatures, true);


        return view('front.index', $data);
    }

    public function subscribe(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:subscribers'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $subsc = new Subscriber;
        $subsc->email = $request->email;
        $subsc->save();

        return "success";
    }

    public function loginView()
    {

        return view('front.login');
    }

    public function checkUsername($username)
    {
        $count = User::where('username', $username)->count();
        $status = $count > 0 ? true : false;
        return response()->json($status);
    }

    public function step1($status, $id)
    {
        Session::forget('coupon');
        Session::forget('coupon_amount');

        if (Auth::check()) {
            return redirect()->route('user.plan.extend.index');
        }
        $data['status'] = $status;
        $data['id'] = $id;
        $package = Package::findOrFail($id);
        $data['package'] = $package;

        $hasSubdomain = false;
        $features = [];
        if (!empty($package->features)) {
            $features = json_decode($package->features, true);
        }
        if (is_array($features) && in_array('Subdomain', $features)) {
            $hasSubdomain = true;
        }

        $data['hasSubdomain'] = $hasSubdomain;

        return view('front.step', $data);
    }

    public function step2(Request $request)
    {
        $data = $request->session()->get('data');
        if (session()->has('coupon_amount')) {
            $data['cAmount'] = session()->get('coupon_amount');
        } else {
            $data['cAmount'] = 0;
        }
        return view('front.checkout', $data);
    }

    public function checkout(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $seo = Seo::where('language_id', $currentLang->id)->first();
        $be = $currentLang->basic_extended;
        $data['bex'] = $be;
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['password'] = $request->password;
        $data['status'] = $request->status;
        $data['id'] = $request->id;
        $online = PaymentGateway::query()->where('status', 1)->get();
        $offline = OfflineGateway::where('status', 1)->get();
        $data['offline'] = $offline;
        $data['payment_methods'] = $online->merge($offline);
        $data['package'] = Package::query()->findOrFail($request->id);
        $data['seo'] = $seo;
        $request->session()->put('data', $data);
        return redirect()->route('front.registration.step2');
    }


    // packages start
    public function pricing(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();

        $data['bex'] = BE::first();
        $data['abs'] = BS::first();

        $terms = [];
        if (Package::query()->where('status', '1')->where('term', 'monthly')->count() > 0) {
            $terms[] = 'Monthly';
        }
        if (Package::query()->where('status', '1')->where('term', 'yearly')->count() > 0) {
            $terms[] = 'Yearly';
        }
        if (Package::query()->where('status', '1')->where('term', 'lifetime')->count() > 0) {
            $terms[] = 'Lifetime';
        }
        $data['terms'] = $terms;

        $be = BasicExtended::select('package_features')->firstOrFail();
        $allPfeatures = $be->package_features ? $be->package_features : "[]";
        $data['allPfeatures'] = json_decode($allPfeatures, true);

        return view('front.pricing', $data);
    }

    // blog section start
    public function blogs(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();

        $data['currentLang'] = $currentLang;

        $lang_id = $currentLang->id;

        $category = $request->category;
        if (!empty($category)) {
            $data['category'] = Bcategory::findOrFail($category);
        }
        $term = $request->term;

        $data['bcats'] = Bcategory::where('language_id', $lang_id)->where('status', 1)->orderBy('serial_number', 'ASC')->get();


        $data['blogs'] = Blog::when($category, function ($query, $category) {
            return $query->where('bcategory_id', $category);
        })
            ->when($term, function ($query, $term) {
                return $query->where('title', 'like', '%' . $term . '%');
            })
            ->when($currentLang, function ($query, $currentLang) {
                return $query->where('language_id', $currentLang->id);
            })->orderBy('serial_number', 'ASC')->paginate(3);
        return view('front.blogs', $data);
    }

    public function blogdetails($slug, $id)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $lang_id = $currentLang->id;


        $data['blog'] = Blog::findOrFail($id);
        $data['bcats'] = Bcategory::where('status', 1)->where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
        return view('front.blog-details', $data);
    }

    public function contactView()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();

        return view('front.contact', $data);
    }

    public function faqs()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();

        $lang_id = $currentLang->id;
        $data['faqs'] = Faq::where('language_id', $lang_id)
            ->orderBy('serial_number', 'DESC')
            ->get();
        return view('front.faq', $data);
    }

    public function dynamicPage($slug)
    {
        $data['page'] = Page::where('slug', $slug)->firstOrFail();

        return view('front.dynamic', $data);
    }

    public function users(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();

        $homeTexts = [];
        if (!empty($request->designation)) {
            $homeTexts = HomePageText::when($request->designation, function ($q) use ($request) {
                return $q->where('designation', 'like', '%' . $request->designation . '%');
            })->select('user_id')->get();
        }

        $userIds = [];

        foreach ($homeTexts as $key => $homeText) {
            if (!in_array($homeText->user_id, $userIds)) {
                $userIds[] = $homeText->user_id;
            }
        }
        $data['users'] = null;
        $users = User::where('online_status', 1)
            ->whereHas('memberships', function ($q) {
                $q->where('status', '=', 1)
                    ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'));
            })
            ->when($request->company, function ($q) use ($request) {
                return $q->where('company_name', 'like', '%' . $request->company . '%');
            })
            ->when($request->location, function ($q) use ($request) {
                return $q->where(function ($query) use ($request) {
                    $query->where('city', 'like', '%' . $request->location . '%')
                        ->orWhere('state', 'like', '%' . $request->location . '%')
                        ->orWhere('address', 'like', '%' . $request->location . '%')
                        ->orWhere('country', 'like', '%' . $request->location . '%');
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(9);

        $data['users'] = $users;
        return view('front.users', $data);
    }

    public function userDetailView($domain)
    {
        $user = getUser();
        $data['user'] = $user;

        if (Auth::check() && Auth::user()->id != $user->id && $user->online_status != 1) {
            return redirect()->route('front.index');
        } elseif (!Auth::check() && $user->online_status != 1) {
            return redirect()->route('front.index');
        }

        $package = UserPermissionHelper::userPackage($user->id);
        if (is_null($package)) {
            Session::flash('warning', 'User membership is expired');
            if (Auth::check()) {
                return redirect()->route('user-dashboard')->with('error', 'User membership is expired');
            } else {
                return redirect()->route('front.user.view');
            }
        }

        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();

            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }
        $userBs = \App\Models\User\BasicSetting::where('user_id', $user->id)->first();

        $data['home_sections'] = User\HomeSection::where('user_id', $user->id)->first();

        $data['home_text'] = User\HomePageText::query()
            ->where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->first();
        $data['portfolios'] = $user->portfolios()
            ->where('language_id', $userCurrentLang->id)
            ->where('featured', 1)
            ->orderBy('serial_number', 'ASC')
            ->get() ?? collect([]);
        $data['portfolio_categories'] = $user->portfolioCategories()
            ->whereHas('portfolios', function ($q) {
                $q->where('featured', 1);
            })
            ->where('language_id', $userCurrentLang->id)
            ->where('status', 1)
            ->orderBy('serial_number', 'ASC')
            ->get() ?? collect([]);
        $data['skills'] = $user->skills()
            ->where('language_id', $userCurrentLang->id)
            ->orderBy('serial_number', 'ASC')
            ->get() ?? collect([]);
        $data['counterInformations'] = $user->counterInformations()
            ->where('language_id', $userCurrentLang->id)
            ->orderBy('serial_number', 'ASC')
            ->get() ?? collect([]);
        $data['services'] = $user->services()->where([
            ['lang_id', $userCurrentLang->id],
            ['featured', 1]
        ])
            ->orderBy('serial_number', 'ASC')
            ->get() ?? collect([]);
        $data['testimonials'] = $user->testimonials()
            ->where('lang_id', $userCurrentLang->id)
            ->orderBy('serial_number', 'ASC')
            ->get() ?? collect([]);

        $blogLimits = 3;
        if ($userBs->theme === 'home_one') {
            $blogLimits = 3;
        } elseif ($userBs->theme === 'home_four' || $userBs->theme === 'home_five' || $userBs->theme === 'home_seven') {
            $blogLimits = 3;
        } elseif ($userBs->theme === 'home_six') {
            $blogLimits = 4;
        } else {
            $blogLimits = 6;
        }
        $data['blogs'] = $user->blogs()
            ->where('language_id', $userCurrentLang->id)
            ->orderBy('id', 'DESC')
            ->take($blogLimits)
            ->get() ?? collect([]);

        $data['teams'] = $user->teams()
            ->where('language_id', $userCurrentLang->id)
            ->where('featured', 1)
            ->get() ?? collect([]);
        $data['brands'] = $user->brands()
            ->get() ?? collect([]);

        $data['sliders'] = HeroSlider::where('language_id', $userCurrentLang->id)
            ->where('user_id', $user->id)
            ->orderBy('serial_number', 'asc')
            ->get();
        if ($userBs->theme === 'home_two') {
            $data['work_processes'] = User\WorkProcess::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->orderBy('serial_number', 'ASC')->get();
            return view('user-front.home-page.home-two', $data);
        } elseif ($userBs->theme === 'home_three') {
            $data['work_processes'] = User\WorkProcess::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->orderBy('serial_number', 'ASC')->get();
            $data['contact'] = UserContact::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->first();
            $data['faqs'] = User\FAQ::query()
                ->where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->where('featured', 1)
                ->get();
            $data['static'] = User\HeroStatic::where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->first();
            return view('user-front.home-page.home-three', $data);
        } elseif ($userBs->theme === 'home_four') {
            $data['work_processes'] = User\WorkProcess::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->orderBy('serial_number', 'ASC')->get();
            $data['contact'] = UserContact::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->first();
            $data['faqs'] = User\FAQ::query()
                ->where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->where('featured', 1)
                ->get();
            $data['static'] = User\HeroStatic::where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->first();

            $data['portfolioCategories'] = PortfolioCategory::where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)->where('is_featured', 1)->get();
            return view('user-front.home-page.home-four', $data);
        } elseif ($userBs->theme === 'home_five') {
            $data['work_processes'] = User\WorkProcess::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->orderBy('serial_number', 'ASC')->get();
            $data['contact'] = UserContact::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->first();
            $data['faqs'] = User\FAQ::query()
                ->where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->where('featured', 1)
                ->get();
            $data['static'] = User\HeroStatic::where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->first();
            $data['portfolioCategories'] = PortfolioCategory::where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)->where('is_featured', 1)->get();
            return view('user-front.home-page.home-five', $data);
        } elseif ($userBs->theme === 'home_six') {
            $data['work_processes'] = User\WorkProcess::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->orderBy('serial_number', 'ASC')->get();
            $data['contact'] = UserContact::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->first();
            $data['faqs'] = User\FAQ::query()
                ->where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->where('featured', 1)
                ->get();
            $data['static'] = User\HeroStatic::where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->first();
            return view('user-front.home-page.home-six', $data);
        } elseif ($userBs->theme === 'home_seven') {
            $data['contact'] = UserContact::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->first();
            $data['work_processes'] = User\WorkProcess::where([
                ['user_id', $user->id],
                ['language_id', $userCurrentLang->id]
            ])->orderBy('serial_number', 'ASC')->get();
            $data['faqs'] = User\FAQ::query()
                ->where('user_id', $user->id)
                ->where('language_id', $userCurrentLang->id)
                ->where('featured', 1)
                ->get();
            return view('user-front.home-page.home-seven', $data);
        } else {
            return view('user-front.home-page.home-one', $data);
        }
    }

    public function paymentInstruction(Request $request)
    {
        $offline = OfflineGateway::where('name', $request->name)
            ->select('short_description', 'instructions', 'is_receipt')
            ->first();

        return response()->json([
            'description' => $offline->short_description,
            'instructions' => $offline->instructions, 'is_receipt' => $offline->is_receipt
        ]);
    }

    public function contactMessage($domain, Request $request)
    {
        $rules = [
            'fullname' => 'required',
            'email' => 'required|email:rfc,dns',
            'subject' => 'required',
            'message' => 'required'
        ];


        $request->validate($rules);


        if (!empty($request->type) && $request->type == 'vcard') {
            $data['toMail'] = $request->to_mail;
            $data['toName'] = $request->to_name;
        } else {
            $toUser = User::query()->findOrFail($request->id);
            $data['toMail'] = $toUser->email;
            $data['toName'] = $toUser->username;
        }
        $data['subject'] = $request->subject;
        $data['fullname'] = $request->fullname;
        $data['email'] = $request->email;
        $data['body'] = "<div>$request->message</div><br>
                         <strong>For further contact with the enquirer please use the below information:</strong><br>
                         <strong>Enquirer Name:</strong> $request->fullname <br>
                         <strong>Enquirer Mail:</strong> $request->email <br>
                         ";
        $mailer = new MegaMailer();
        $mailer->mailContactMessage($data);
        Session::flash('success', 'Mail sent successfully');
        return back();
    }

    public function adminContactMessage(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email:rfc,dns',
            'subject' => 'required',
            'message' => 'required'
        ];

        $bs = BS::select('is_recaptcha')->first();

        if ($bs->is_recaptcha == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }
        $messages = [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ];

        $request->validate($rules, $messages);

        $data['fromMail'] = $request->email;
        $data['fromName'] = $request->name;
        $data['subject'] = $request->subject;
        $data['body'] = $request->message;
        $mailer = new MegaMailer();
        $mailer->mailToAdmin($data);
        Session::flash('success', 'Message sent successfully');
        return back();
    }

    public function userServices($domain)
    {
        $user = getUser();
        $id = $user->id;

        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['home_text'] = User\HomePageText::query()
            ->where([
                ['user_id', $id],
                ['language_id', $userCurrentLang->id]
            ])->first();

        $data['services'] = User\UserService::query()
            ->where('user_id', $id)
            ->where('lang_id', $userCurrentLang->id)
            ->orderBy('serial_number', 'ASC')
            ->get();
        return view('user-front.service.index', $data);
    }

    public function userServiceDetail($domain, $slug, $id)
    {
        $data['service'] = User\UserService::query()->findOrFail($id);
        return view('user-front.service.show', $data);
    }

    public function userBlogs(Request $request, $domain)
    {
        $user = getUser();
        $id = $user->id;
        $data['user'] = $user;
        $catid = $request->category;
        $term = $request->term;

        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['home_text'] = User\HomePageText::query()
            ->where([
                ['user_id', $id],
                ['language_id', $userCurrentLang->id]
            ])->first();


        $data['blogs'] = User\Blog::query()
            ->when($catid, function ($query, $catid) {
                return $query->where('category_id', $catid);
            })
            ->when($term, function ($query, $term) {
                return $query->where('title', 'LIKE', '%' . $term . '%');
            })
            ->where('user_id', $id)
            ->where('language_id', $userCurrentLang->id)
            ->orderBy('serial_number', 'ASC')
            ->paginate(3);

        $data['blog_categories'] = User\BlogCategory::query()
            ->where('status', 1)
            ->orderBy('serial_number', 'ASC')
            ->where('language_id', $userCurrentLang->id)
            ->where('user_id', $id)
            ->get();

        $data['allCount'] = User\Blog::query()
            ->where('user_id', $id)
            ->where('language_id', $userCurrentLang->id)
            ->count();
        return view('user-front.blog.index', $data);
    }

    public function userBlogDetail($domain, $slug, $id)
    {
        $user = getUser();
        $userId = $user->id;
        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['blog'] = User\Blog::query()->findOrFail($id);

        $data['latestBlogs'] = User\Blog::query()
            ->where('user_id', $userId)
            ->where('language_id', $userCurrentLang->id)
            ->orderBy('id', 'DESC')
            ->limit(3)->get();

        $data['blog_categories'] = User\BlogCategory::query()
            ->where('status', 1)
            ->orderBy('serial_number', 'ASC')
            ->where('language_id', $userCurrentLang->id)
            ->where('user_id', $userId)
            ->get();

        $data['allCount'] = User\Blog::query()
            ->where('user_id', $userId)
            ->where('language_id', $userCurrentLang->id)
            ->count();
        return view('user-front.blog.show', $data);
    }

    public function userPortfolios(Request $request, $domain)
    {
        $user = getUser();
        $id = $user->id;
        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['home_text'] = User\HomePageText::query()
            ->where([
                ['user_id', $id],
                ['language_id', $userCurrentLang->id]
            ])->first();
        $data['portfolio_categories'] = User\PortfolioCategory::query()
            ->where('status', 1)
            ->orderBy('serial_number', 'ASC')
            ->where('language_id', $userCurrentLang->id)
            ->where('user_id', $id)
            ->get();

        $data['catId'] = $request->category;

        $data['portfolios'] = User\Portfolio::query()
            ->where('user_id', $id)
            ->latest()
            ->orderBy('serial_number', 'ASC')
            ->where('language_id', $userCurrentLang->id)
            ->get();
        return view('user-front.portfolio.index', $data);
    }

    public function userPortfolioDetail($domain, $slug, $id)
    {
        $user = getUser();
        $userId = $user->id;
        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $portfolio = User\Portfolio::query()->findOrFail($id);
        $catId = $portfolio->category_id;
        $data['relatedPortfolios'] = User\Portfolio::where('category_id', $catId)->where('id', '<>', $portfolio->id)->where('user_id', $userId)->orderBy('id', 'DESC')->limit(5);
        $data['portfolio'] = $portfolio;
        $data['portfolio_categories'] = User\PortfolioCategory::query()
            ->where('status', 1)
            ->where('language_id', $userCurrentLang->id)
            ->where('user_id', $userId)
            ->orderBy('serial_number', 'ASC')
            ->get();
        $data['allCount'] = User\Portfolio::where('language_id', $userCurrentLang->id)->where('user_id', $userId)->count();
        return view('user-front.portfolio.show', $data);
    }

    public function userJobs(Request $request, $domain)
    {
        $user = getUser();
        $id = $user->id;
        $data['user'] = $user;
        $cat_id = $request->category;
        $term = $request->term;

        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['home_text'] = User\HomePageText::query()
            ->where([
                ['user_id', $id],
                ['language_id', $userCurrentLang->id]
            ])->first();

        $data['jobs'] = User\Job::query()
            ->when($cat_id, function ($query, $cat_id) {
                return $query->where('jcategory_id', $cat_id);
            })
            ->when($term, function ($query, $term) {
                return $query->where('title', 'LIKE', '%' . $term . '%');
            })
            ->where('user_id', $id)
            ->where('language_id', $userCurrentLang->id)
            ->orderBy('serial_number', 'ASC')
            ->paginate(4);

        $data['job_categories'] = User\Jcategory::query()
            ->where('status', 1)
            ->orderBy('serial_number', 'ASC')
            ->where('language_id', $userCurrentLang->id)
            ->where('user_id', $id)
            ->get();

        $data['allCount'] = User\Job::query()
            ->where('user_id', $id)
            ->where('language_id', $userCurrentLang->id)
            ->count();
        return view('user-front.job.index', $data);
    }

    public function userJobDetail($domain, $slug, $id)
    {
        $user = getUser();
        $userId = $user->id;
        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['job'] = User\Job::query()->findOrFail($id);

        $data['latestJobs'] = User\Job::query()
            ->where('user_id', $userId)
            ->where('language_id', $userCurrentLang->id)
            ->orderBy('id', 'DESC')
            ->limit(3)->get();

        $data['job_categories'] = User\Jcategory::query()
            ->where('status', 1)
            ->orderBy('serial_number', 'ASC')
            ->where('language_id', $userCurrentLang->id)
            ->where('user_id', $userId)
            ->get();

        $data['allCount'] = User\Job::query()
            ->where('user_id', $userId)
            ->where('language_id', $userCurrentLang->id)
            ->count();
        return view('user-front.job.show', $data);
    }

    public function contact(Request $request, $domain)
    {
        $user = getUser();
        $data['user'] = $user;
        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['contact'] = UserContact::where([
            ['user_id', $data['user']->id],
            ['language_id', $userCurrentLang->id]
        ])->first();
        $data['home_text'] = User\HomePageText::query()
            ->where([
                ['user_id', $data['user']->id],
                ['language_id', $userCurrentLang->id]
            ])->first();
        return view('user-front.contact', $data);
    }

    public function userTeam($domain)
    {
        $user = getUser();
        $id = $user->id;

        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['home_text'] = User\HomePageText::query()
            ->where([
                ['user_id', $id],
                ['language_id', $userCurrentLang->id]
            ])->first();

        $data['members'] = User\Member::query()
            ->where('user_id', $id)
            ->where('language_id', $userCurrentLang->id)
            ->get();
        return view('user-front.team', $data);
    }

    public function userFaqs($domain)
    {
        $user = getUser();
        $id = $user->id;

        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['home_text'] = User\HomePageText::query()
            ->where([
                ['user_id', $id],
                ['language_id', $userCurrentLang->id]
            ])->first();

        $data['faqs'] = User\FAQ::query()
            ->where('user_id', $id)
            ->where('language_id', $userCurrentLang->id)
            ->get();
        return view('user-front.faqs', $data);
    }

    public function quote($domain)
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

        $bs = User\BasicSetting::where('user_id', $user->id)->first();

        if ($bs->is_quote == 0) {
            return view('errors.404');
        }

        $data['inputs'] = QuoteInput::where([
            ['language_id', $userCurrentLang->id],
            ['user_id', $user->id]
        ])->orderBy('order_number', 'ASC')->get();
        return view('user-front.quote', $data);
    }


    public function sendquote(Request $request, $domain)
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
        app()->setLocale($userCurrentLang->code);

        $quote_inputs = QuoteInput::where([
            ['language_id', $userCurrentLang->id],
            ['user_id', $user->id]
        ])->get();

        $rules = [
            'name' => 'required',
            'email' => 'required|email'
        ];


        $allowedExts = array('zip');
        foreach ($quote_inputs as $input) {
            if ($input->required == 1) {
                $rules["$input->name"][] = 'required';
            }
            // check if input type is 5, then check for zip extension
            if ($input->type == 5) {
                $rules["$input->name"][] = function ($attribute, $value, $fail) use ($request, $input, $allowedExts) {
                    if ($request->hasFile("$input->name")) {
                        $ext = $request->file("$input->name")->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            $fail("Only zip file is allowed");
                        }
                    }
                };
            }
        }
        $request->validate($rules);

        $fields = [];
        foreach ($quote_inputs as $key => $input) {
            $in_name = $input->name;
            // if the input is file, then move it to 'files' folder
            if ($input->type == 5) {
                if ($request->hasFile("$in_name")) {
                    $fileName = uniqid() . '.' . $request->file("$in_name")->getClientOriginalExtension();
                    $directory = 'assets/front/files/';
                    @mkdir($directory, 0775, true);
                    $request->file("$in_name")->move($directory, $fileName);

                    $fields["$in_name"]['value'] = $fileName;
                    $fields["$in_name"]['type'] = $input->type;
                }
            } else {
                if ($request["$in_name"]) {
                    $fields["$in_name"]['value'] = $request["$in_name"];
                    $fields["$in_name"]['type'] = $input->type;
                }
            }
        }
        $jsonfields = json_encode($fields);
        $jsonfields = str_replace("\/", "/", $jsonfields);


        $quote = new Quote;
        $quote->name = $request->name;
        $quote->email = $request->email;
        $quote->user_id = $user->id;
        $quote->fields = $jsonfields;

        $quote->save();

        $subject = "Quote Request Received";

        $be = BE::first();

        $mail = new PHPMailer(true);

        if ($be->is_smtp == 1) {
            try {
                //Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host = $be->smtp_host;                    // Set the SMTP server to send through
                $mail->SMTPAuth = true;                                   // Enable SMTP authentication
                $mail->Username = $be->smtp_username;                     // SMTP username
                $mail->Password = $be->smtp_password;                               // SMTP password
                $mail->SMTPSecure = $be->encryption;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port = $be->smtp_port;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                $mail->CharSet = 'UTF-8';
                $mail->addReplyTo($request->email);
                //Recipients
                $mail->setFrom($be->from_mail, $request->name);
                $mail->addAddress($user->email);     // Add a recipient

            } catch (Exception $e) {
                Session::flash('error', $e->getMessage());
                return back();
            }
        } else {
            try {
                //Recipients
                $mail->setFrom($be->from_mail, $request->name);
                $mail->addReplyTo($request->email);
                $mail->addAddress($user->email);     // Add a recipient
            } catch (Exception $e) {
                Session::flash('error', $e->getMessage());
                return back();
            }
        }

        $message = '<div dir="ltr">You have received a new quote request.<br/><strong dir="ltr">Client Name - </strong><span dir="ltr">' . $request->name . '</span><br/><strong dir="ltr">Client Mail - </strong><span dir="ltr">' . $request->email . "</span><br>";

        if (!empty($fields) && is_array($fields)) {
            foreach ($fields as $key => $field) {
                if ($field['type'] != 5) {
                    $message .= "<div><strong dir='ltr'>" . ucwords(str_replace("_", " ", $key)) . " - </strong>";
                    if (!is_array($field['value'])) {
                        $message .= "<span dir='ltr'>" . $field['value'] . "</span>";
                    } else {
                        $values = $field['value'];
                        $i = 0;
                        foreach ($values as $key => $value) {
                            $i++;
                            $message .= "<span dir='ltr'>" . $value . "</span>";
                            if (count($values) > $i) {
                                $message .= ", ";
                            }
                        }
                    }
                    $message .= "</div>";
                }
            }
        }
        $message .= "</div>";

        // Content
        $mail->isHTML(true);   // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();

        Session::flash('success', 'Quote request sent successfully');
        return back();
    }

    public function vcard($domain, $id)
    {
        $vcard = UserVcard::findOrFail($id);

        $count = $vcard->user->memberships()->where('status', '=', 1)
            ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'))
            ->count();

        // check if the vcard owner does not have membership
        if ($count == 0) {
            return view('errors.404');
        }

        $cFeatures = UserPermissionHelper::packagePermission($vcard->user_id);
        $cFeatures = json_decode($cFeatures, true);
        if (empty($cFeatures) || !is_array($cFeatures) || !in_array('vCard', $cFeatures)) {
            return view('errors.404');
        }

        $parsedUrl = parse_url(url()->current());
        $host = $parsedUrl['host'];
        // if the current host contains the website domain
        if (strpos($host, env('WEBSITE_HOST')) !== false) {
            $host = str_replace("www.", "", $host);
            // if the current URL is subdomain
            if ($host != env('WEBSITE_HOST')) {
                $hostArr = explode('.', $host);
                $username = $hostArr[0];
                if (strtolower($vcard->user->username) != strtolower($username) || !cPackageHasSubdomain($vcard->user)) {
                    return view('errors.404');
                }
            } else {
                $path = explode('/', $parsedUrl['path']);
                $username = $path[1];
                if (strtolower($vcard->user->username) != strtolower($username)) {
                    return view('errors.404');
                }
            }
        }
        // if the current host doesn't contain the website domain (meaning, custom domain)
        else {
            // Always include 'www.' at the begining of host
            if (substr($host, 0, 4) == 'www.') {
                $host = $host;
            } else {
                $host = 'www.' . $host;
            }
            // if the current package doesn't have 'custom domain' feature || the custom domain is not connected
            $cdomain = UserCustomDomain::where('requested_domain', '=', $host)->orWhere('requested_domain', '=', str_replace("www.", "", $host))->where('status', 1)->firstOrFail();
            $username = $cdomain->user->username;
            if (!cPackageHasCdomain($vcard->user) || ($username != $vcard->user->username)) {
                return view('errors.404');
            }
        }

        $infos = json_decode($vcard->information, true);

        $prefs = [];
        if (!empty($vcard->preferences)) {
            $prefs = json_decode($vcard->preferences, true);
        }

        $keywords = json_decode($vcard->keywords, true);

        $data['vcard'] = $vcard;
        $data['infos'] = $infos;
        $data['prefs'] = $prefs;
        $data['keywords'] = $keywords;
        if ($vcard->template == 1) {
            return view('vcard.index1', $data);
        } elseif ($vcard->template == 2) {
            return view('vcard.index2', $data);
        } elseif ($vcard->template == 3) {
            return view('vcard.index3', $data);
        } elseif ($vcard->template == 4) {
            return view('vcard.index4', $data);
        } elseif ($vcard->template == 5) {
            return view('vcard.index5', $data);
        } elseif ($vcard->template == 6) {
            return view('vcard.index6', $data);
        } elseif ($vcard->template == 7) {
            return view('vcard.index7', $data);
        } elseif ($vcard->template == 8) {
            return view('vcard.index8', $data);
        } elseif ($vcard->template == 9) {
            return view('vcard.index9', $data);
        } elseif ($vcard->template == 10) {
            return view('vcard.index10', $data);
        }
    }

    public function vcardImport($domain, $id)
    {
        $vcard = UserVcard::findOrFail($id);

        // define vcard
        $vcardObj = new VCard();

        // add personal data
        if (!empty($vcard->name)) {
            $vcardObj->addName($vcard->name);
        }
        if (!empty($vcard->company)) {
            $vcardObj->addCompany($vcard->company);
        }
        if (!empty($vcard->occupation)) {
            $vcardObj->addJobtitle($vcard->occupation);
        }
        if (!empty($vcard->email)) {
            $vcardObj->addEmail($vcard->email);
        }
        if (!empty($vcard->phone)) {
            $vcardObj->addPhoneNumber($vcard->phone, 'WORK');
        }
        if (!empty($vcard->address)) {
            $vcardObj->addAddress($vcard->address);
            $vcardObj->addLabel($vcard->address);
        }
        if (!empty($vcard->website_url)) {
            $vcardObj->addURL($vcard->website_url);
        }

        $vcardObj->addPhoto('assets/front/img/user/vcard/' . $vcard->profile_image);

        return \Response::make(
            $vcardObj->getOutput(),
            200,
            $vcardObj->getHeaders(true)
        );
    }

    public function changeLanguage($lang): \Illuminate\Http\RedirectResponse
    {
        session()->put('lang', $lang);
        app()->setLocale($lang);
        return redirect()->route('front.index');
    }

    public function changeUserLanguage(Request $request, $domain): \Illuminate\Http\RedirectResponse
    {
        session()->put('user_lang', $request->code);
        return redirect()->route('front.user.detail.view', $domain);
    }

    public function userCPage($param, $slug)
    {
        $user = getUser();
        $userId = $user->id;
        if (session()->has('user_lang')) {
            $userCurrentLang = UserLanguage::where('code', session()->get('user_lang'))->where('user_id', $user->id)->first();
            if (empty($userCurrentLang)) {
                $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
                session()->put('user_lang', $userCurrentLang->code);
            }
        } else {
            $userCurrentLang = UserLanguage::where('is_default', 1)->where('user_id', $user->id)->first();
        }

        $data['page'] = User\Page::query()->where('user_id', $userId)->where('language_id', $userCurrentLang->id)->where('slug', $slug)->firstOrFail();

        return view('user-front.custom-page', $data);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\MegaMailer;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\BasicExtended;
use App\Models\BasicSetting;
use App\Models\Membership;
use App\Models\OfflineGateway;
use App\Models\Package;
use App\Models\PaymentGateway;
use App\Models\User;
use App\Models\User\BasicSetting as UserBasicSetting;
use App\Models\User\HomePageText;
use App\Models\User\HomeSection;
use App\Models\User\Language;
use App\Models\User\Menu;
use App\Models\User\UserPermission;
use Carbon\Carbon;
use Hash;
use Session;
use Validator;

class RegisterUserController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->term;

        $users = User::when($term, function($query, $term) {
            $query->where('username', 'like', '%' . $term . '%')->orWhere('email', 'like', '%' . $term . '%');
        })->orderBy('id', 'DESC')->paginate(10);

        $online = PaymentGateway::query()->where('status', 1)->get();
        $offline = OfflineGateway::where('status', 1)->get();
        $gateways = $online->merge($offline);
        $packages = Package::query()->where('status', '1')->get();

        return view('admin.register_user.index',compact('users', 'gateways', 'packages'));
    }

    public function view($id)
    {
        $user = User::findOrFail($id);
        $packages = Package::query()->where('status', '1')->get();

        $online = PaymentGateway::query()->where('status', 1)->get();
        $offline = OfflineGateway::where('status', 1)->get();
        $gateways = $online->merge($offline);

        return view('admin.register_user.details',compact('user', 'packages', 'gateways'));

    }

    public function store(Request $request) {

        $rules = [
            'username' => 'required|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'package_id' => 'required',
            'payment_gateway' => 'required'
        ];

        $messages = [
            'package_id.required' => 'The package field is required',
            'online_status.required' => 'The publicly hidden field is required'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $user = User::where('username', $request['username']);
        if ($user->count() == 0) {
            $user = User::create([
                'email' => $request['email'],
                'username' => $request['username'],
                'password' => bcrypt($request['password']),
                'status' => 1,
                'email_verified' => 1,
            ]);

            UserBasicSetting::create([
                'user_id' => $user->id,
            ]);

            $homeSection = new HomeSection();
            $homeSection->user_id = $user->id;
            $homeSection->save();
        }
        
        if ($user) {
            $deLang = Language::firstOrFail();
            $langCount = Language::where('user_id', $user->id)->where('is_default', 1)->count();
            if ($langCount == 0) {
                $lang = Language::create([
                    'name' => 'English',
                    'code' => 'en',
                    'is_default' => 1,
                    'rtl' => 0,
                    'user_id' => $user->id,
                    'keywords' => $deLang->keywords
                ]);

                $htext = new HomePageText();
                $htext->language_id = $lang->id;
                $htext->user_id = $user->id;
                $htext->save();

                $umenu = new Menu();
                $umenu->language_id = $lang->id;
                $umenu->user_id = $user->id;
                $umenu->menus = '[{"text":"Home","href":"","icon":"empty","target":"_self","title":"","type":"home"},{"type":"custom","text":"About","href":"","target":"_self","children":[{"text":"Team","href":"","icon":"empty","target":"_self","title":"","type":"team"},{"text":"Career","href":"","icon":"empty","target":"_self","title":"","type":"career"},{"text":"FAQ","href":"","icon":"empty","target":"_self","title":"","type":"faq"}]},{"text":"Services","href":"","icon":"empty","target":"_self","title":"","type":"services"},{"text":"Blog","href":"","icon":"empty","target":"_self","title":"","type":"blog"},{"text":"Portfolios","href":"","icon":"empty","target":"_self","title":"","type":"portfolios"},{"text":"Contact","href":"","icon":"empty","target":"_self","title":"","type":"contact"}]';
                $umenu->save();
            }

            $package = Package::find($request['package_id']);
            $be = BasicExtended::first();
            $bs = BasicSetting::select('website_title')->first();
            $transaction_id = UserPermissionHelper::uniqidReal(8);

            $startDate = Carbon::today()->format('Y-m-d');
            if ($package->term === "monthly") {
                $endDate = Carbon::today()->addMonth()->format('Y-m-d');
            } elseif ($package->term === "yearly") {
                $endDate = Carbon::today()->addYear()->format('Y-m-d');
            } elseif ($package->term === "lifetime") {
                $endDate = Carbon::maxValue()->format('d-m-Y');
            }

            $memb = Membership::create([
                'price' => $package->price,
                'currency' => $be->base_currency_text ? $be->base_currency_text : "USD",
                'currency_symbol' => $be->base_currency_symbol ? $be->base_currency_symbol : $be->base_currency_text,
                'payment_method' => $request["payment_gateway"],
                'transaction_id' => $transaction_id ? $transaction_id : 0,
                'status' => 1,
                'is_trial' => 0,
                'trial_days' => 0,
                'receipt' => $request["receipt_name"] ? $request["receipt_name"] : null,
                'transaction_details' => null,
                'settings' => json_encode($be),
                'package_id' => $request['package_id'],
                'user_id' => $user->id,
                'start_date' => Carbon::parse($startDate),
                'expire_date' => Carbon::parse($endDate),
            ]);
            $package = Package::findOrFail($request['package_id']);
            $features = json_decode($package->features, true);
            $features[] = "Contact";
            UserPermission::create([
                'package_id' => $request['package_id'],
                'user_id' => $user->id,
                'permissions' => json_encode($features)
            ]);


            $requestData = [
                'start_date' => $startDate,
                'expire_date' => $endDate,
                'payment_method' => $request['payment_gateway']
            ];
            $file_name = $this->makeInvoice($requestData,"membership",$user,null,$package->price,$request['payment_gateway'],null,$be->base_currency_symbol_position,$be->base_currency_symbol,$be->base_currency_text,$transaction_id,$package->title,$memb);

            $mailer = new MegaMailer();
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
            $data = [
                'toMail' => $user->email,
                'toName' => $user->fname,
                'username' => $user->username,
                'package_title' => $package->title,
                'package_price' => ($be->base_currency_text_position == 'left' ? $be->base_currency_text . ' ' : '') . $package->price . ($be->base_currency_text_position == 'right' ? ' ' . $be->base_currency_text : ''),
                'activation_date' => $startDate->toFormattedDateString(),
                'expire_date' => $endDate->toFormattedDateString(),
                'membership_invoice' => $file_name,
                'website_title' => $bs->website_title,
                'templateType' => 'registration_with_premium_package',
                'type' => 'registrationWithPremiumPackage'
            ];
            $mailer->mailFromAdmin($data);
        }

        Session::flash('success', 'User added successfully!');
        return "success";
    }


    public function userban(Request $request)
    {
        $user = User::where('id',$request->user_id)->first();
        $user->update([
            'status' => $request->status,
        ]);
        Session::flash('success', 'Status update successfully!');
        return back();

    }


    public function emailStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'email_verified' => $request->email_verified,
        ]);

        Session::flash('success', 'Email status updated for ' . $user->username);
        return back();
    }

    public function userFeatured(Request $request)
    {
        $user = User::where('id',$request->user_id)->first();
        $user->featured = $request->featured;
        $user->save();
        Session::flash('success', 'User featured update successfully!');
        return back();
    }


    public function changePass($id) {
        $data['user'] = User::findOrFail($id);
        return view('admin.register_user.password', $data);
    }


    public function updatePassword(Request $request)
    {

        $messages = [
            'npass.required' => 'New password is required',
            'cfpass.required' => 'Confirm password is required',
        ];

        $request->validate([
            'npass' => 'required',
            'cfpass' => 'required',
        ], $messages);


        $user = User::findOrFail($request->user_id);
        if ($request->npass == $request->cfpass) {
            $input['password'] = Hash::make($request->npass);
        } else {
            return back()->with('warning', __('Confirm password does not match.'));
        }

        $user->update($input);

        Session::flash('success', 'Password update for ' . $user->username);
        return back();
    }

    public function delete(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if ($user->testimonials()->count() > 0) {
            $testimonials = $user->testimonials()->get();
            foreach ($testimonials as $key => $tstm) {
                @unlink('assets/front/img/user/testimonials/' . $tstm->image);
                $tstm->delete();
            }
        }

        if ($user->social_media()->count() > 0) {
            $user->social_media()->delete();
        }

        if ($user->skills()->count() > 0) {
            $user->skills()->delete();
        }

        if ($user->services()->count() > 0) {
            $services = $user->services()->get();
            foreach ($services as $key => $service) {
                @unlink('assets/front/img/user/services/' . $service->image);
                $service->delete();
            }
        }

        if ($user->subscribers()->count() > 0) {
            $user->subscribers()->delete();
        }

        if ($user->seos()->count() > 0) {
            $user->seos()->delete();
        }

        if ($user->portfolios()->count() > 0) {
            $portfolios = $user->portfolios()->get();
            foreach ($portfolios as $key => $portfolio) {
                @unlink('assets/front/img/user/portfolios/' . $portfolio->image);
                if ($portfolio->portfolio_images()->count() > 0) {
                    foreach ($portfolio->portfolio_images as $key => $pi) {
                        @unlink('assets/front/img/user/portfolios/' . $pi->image);
                        $pi->delete();
                    }
                }
                $portfolio->delete();
            }
        }

        if ($user->portfolioCategories()->count() > 0) {
            $user->portfolioCategories()->delete();
        }

        if ($user->permission()->count() > 0) {
            $user->permission()->delete();
        }

        if ($user->languages()->count() > 0) {
            $user->languages()->delete();
        }

        if ($user->home_page_texts()->count() > 0) {
            $homeTexts = $user->home_page_texts()->get();
            foreach ($homeTexts as $key => $homeText) {
                @unlink('assets/front/img/user/home_settings/' . $homeText->about_image);
                @unlink('assets/front/img/user/home_settings/' . $homeText->about_video_image);
                @unlink('assets/front/img/user/home_settings/' . $homeText->testimonial_image);
                @unlink('assets/front/img/user/home_settings/'. $homeText->video_section_image);
                @unlink('assets/front/img/user/home_settings/'. $homeText->why_choose_us_section_image);
                @unlink('assets/front/img/user/home_settings/'. $homeText->why_choose_us_section_video_image);
                @unlink('assets/front/img/work_process/'.$homeText->work_process_section_img);
                @unlink('assets/front/img/work_process/'.$homeText->work_process_section_video_img);
                @unlink('assets/front/img/user/home_settings/'. $homeText->quote_section_image);

                $homeText->delete();
            }
        }

        if ($user->home_section()->count() > 0) {
            $user->home_section()->delete();
        }

        if ($user->jobs()->count() > 0) {
            $user->jobs()->delete();
        }

        if ($user->jcategories()->count() > 0) {
            $user->jcategories()->delete();
        }

        if ($user->teams()->count() > 0) {
            $teams = $user->teams()->get();
            foreach ($teams as $key => $team) {
                @unlink('/assets/front/img/user/team/'.$team->image);
                $team->delete();
            }
        }

        if ($user->permissions()->count() > 0) {
            $user->permissions()->delete();
        }

        if ($user->qr_codes()->count() > 0) {
            $qr_codes = $user->qr_codes()->get();
            foreach ($qr_codes as $key => $qr) {
                @unlink('assets/front/img/user/qr/' . $qr->image);
                $qr->delete();
            }
        }

        if ($user->quotes()->count() > 0) {
            $user->quotes()->delete();
        }

        if ($user->quote_inputs()->count() > 0) {
            $quote_inputs = $user->quote_inputs()->get();
            foreach ($quote_inputs as $key => $input) {
                if ($input->quote_input_options()->count() > 0) {
                    $input->quote_input_options()->delete();
                }
                $input->delete();
            }
        }

        if ($user->services()->count() > 0) {
            $services = $user->services()->get();
            foreach ($services as $key => $service) {
                @unlink('assets/front/img/user/service/' . $service->image);
                $service->delete();
            }
        }

        if ($user->vcards()->count() > 0) {
            $vcards = $user->vcards()->get();
            foreach ($vcards as $key => $vcard) {
                @unlink('assets/front/img/user/vcard/' . $vcard->profile_image);
                @unlink('assets/front/img/user/vcard/' . $vcard->cover_image);
                if ($vcard->user_vcard_projects()->count() > 0) {
                    foreach ($vcard->user_vcard_projects as $key => $project) {
                        @unlink('assets/front/img/user/projects/' . $project->image);
                        $project->delete();
                    }
                }
                if ($vcard->user_vcard_services()->count() > 0) {
                    foreach ($vcard->user_vcard_services as $key => $service) {
                        @unlink('assets/front/img/user/services/' . $service->image);
                        $service->delete();
                    }
                }
                if ($vcard->user_vcard_testimonials()->count() > 0) {
                    foreach ($vcard->user_vcard_testimonials as $key => $testimonial) {
                        @unlink('assets/front/img/user/testimonials/' . $testimonial->image);
                        $testimonial->delete();
                    }
                }
                $vcard->delete();
            }
        }

        if ($user->processes()->count() > 0) {
            $user->processes()->delete();
        }

        if ($user->blog_categories()->count() > 0) {
            $user->blog_categories()->delete();
        }
        

        if ($user->blogs()->count() > 0) {
            $blogs = $user->blogs()->get();
            foreach ($blogs as $key => $blog) {
                @unlink('assets/front/img/user/blogs/' . $blog->image);
                $blog->delete();
            }
        }

        if ($user->basic_setting()->count() > 0) {
            $bs = $user->basic_setting;
            @unlink('assets/front/img/user/' . $bs->breadcrumb);
            @unlink('assets/front/img/user/' . $bs->logo);
            @unlink('assets/front/img/user/' . $bs->preloader);
            @unlink('assets/front/img/user/' . $bs->favicon);
            @unlink('assets/front/img/user/qr/' . $bs->qr_image);
            @unlink('assets/front/img/user/qr/' . $bs->qr_inserted_image);
            $bs->delete();
        }

        if ($user->memberships()->count() > 0) {
            foreach($user->memberships as $key => $membership) {
                @unlink('assets/front/img/membership/receipt/' . $membership->receipt);
                $membership->delete();
            }
        }
            
    
        if ($user->brands()->count() > 0) {
            $brands = $user->brands()->get();
            foreach ($brands as $key => $brand) {
                @unlink('assets/front/img/user/brands/' . $brand->brand_img);
                $brand->delete();
            }
        }
            
    
        if ($user->user_contact()->count() > 0) {
            $contact = $user->user_contact;
            @unlink('assets/front/img/user/'.$contact->contact_form_image);
            $contact->delete();
        }

        if ($user->faqs()->count() > 0) {
            $user->faqs()->delete();
        }

        if ($user->footer_quick_links()->count() > 0) {
            $user->footer_quick_links()->delete();
        }

        if ($user->footer_texts()->count() > 0) {
            $user->footer_texts()->delete();
        }

        if ($user->menus()->count() > 0) {
            $user->menus()->delete();
        }

        if ($user->pages()->count() > 0) {
            $user->pages()->delete();
        }
            
    
        if ($user->hero_sliders()->count() > 0) {
            $sliders = $user->hero_sliders()->get();
            foreach ($sliders as $key => $slider) {
                @unlink('assets/front/img/hero_slider/'.$slider->img);
                $slider->delete();
            }
        }

        if ($user->hero_static()->count() > 0) {
            $static = $user->hero_static;
            @unlink('assets/front/img/hero_static/' . $static->img);
            $static->delete();
        }

        @unlink('assets/front/img/user/' . $user->photo);
        $user->delete();

        Session::flash('success', 'User deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $user = User::findOrFail($id);

            if ($user->testimonials()->count() > 0) {
                $testimonials = $user->testimonials()->get();
                foreach ($testimonials as $key => $tstm) {
                    @unlink('assets/front/img/user/testimonials/' . $tstm->image);
                    $tstm->delete();
                }
            }
    
            if ($user->social_media()->count() > 0) {
                $user->social_media()->delete();
            }
    
            if ($user->skills()->count() > 0) {
                $user->skills()->delete();
            }
    
            if ($user->services()->count() > 0) {
                $services = $user->services()->get();
                foreach ($services as $key => $service) {
                    @unlink('assets/front/img/user/services/' . $service->image);
                    $service->delete();
                }
            }
    
            if ($user->subscribers()->count() > 0) {
                $user->subscribers()->delete();
            }
    
            if ($user->seos()->count() > 0) {
                $user->seos()->delete();
            }
    
            if ($user->portfolios()->count() > 0) {
                $portfolios = $user->portfolios()->get();
                foreach ($portfolios as $key => $portfolio) {
                    @unlink('assets/front/img/user/portfolios/' . $portfolio->image);
                    if ($portfolio->portfolio_images()->count() > 0) {
                        foreach ($portfolio->portfolio_images as $key => $pi) {
                            @unlink('assets/front/img/user/portfolios/' . $pi->image);
                            $pi->delete();
                        }
                    }
                    $portfolio->delete();
                }
            }
    
            if ($user->portfolioCategories()->count() > 0) {
                $user->portfolioCategories()->delete();
            }
    
            if ($user->permission()->count() > 0) {
                $user->permission()->delete();
            }
    
            if ($user->languages()->count() > 0) {
                $user->languages()->delete();
            }
    
            if ($user->home_page_texts()->count() > 0) {
                $homeTexts = $user->home_page_texts()->get();
                foreach ($homeTexts as $key => $homeText) {
                    @unlink('assets/front/img/user/home_settings/' . $homeText->about_image);
                    @unlink('assets/front/img/user/home_settings/' . $homeText->about_video_image);
                    @unlink('assets/front/img/user/home_settings/' . $homeText->testimonial_image);
                    @unlink('assets/front/img/user/home_settings/'. $homeText->video_section_image);
                    @unlink('assets/front/img/user/home_settings/'. $homeText->why_choose_us_section_image);
                    @unlink('assets/front/img/user/home_settings/'. $homeText->why_choose_us_section_video_image);
                    @unlink('assets/front/img/work_process/'.$homeText->work_process_section_img);
                    @unlink('assets/front/img/work_process/'.$homeText->work_process_section_video_img);
                    @unlink('assets/front/img/user/home_settings/'. $homeText->quote_section_image);
    
                    $homeText->delete();
                }
            }
    
            if ($user->home_section()->count() > 0) {
                $user->home_section()->delete();
            }
    
            if ($user->jobs()->count() > 0) {
                $user->jobs()->delete();
            }
    
            if ($user->jcategories()->count() > 0) {
                $user->jcategories()->delete();
            }
    
            if ($user->teams()->count() > 0) {
                $teams = $user->teams()->get();
                foreach ($teams as $key => $team) {
                    @unlink('/assets/front/img/user/team/'.$team->image);
                    $team->delete();
                }
            }
    
            if ($user->permissions()->count() > 0) {
                $user->permissions()->delete();
            }
    
            if ($user->qr_codes()->count() > 0) {
                $qr_codes = $user->qr_codes()->get();
                foreach ($qr_codes as $key => $qr) {
                    @unlink('assets/front/img/user/qr/' . $qr->image);
                    $qr->delete();
                }
            }
    
            if ($user->quotes()->count() > 0) {
                $user->quotes()->delete();
            }
    
            if ($user->quote_inputs()->count() > 0) {
                $quote_inputs = $user->quote_inputs()->get();
                foreach ($quote_inputs as $key => $input) {
                    if ($input->quote_input_options()->count() > 0) {
                        $input->quote_input_options()->delete();
                    }
                    $input->delete();
                }
            }
    
            if ($user->services()->count() > 0) {
                $services = $user->services()->get();
                foreach ($services as $key => $service) {
                    @unlink('assets/front/img/user/service/' . $service->image);
                    $service->delete();
                }
            }
    
            if ($user->vcards()->count() > 0) {
                $vcards = $user->vcards()->get();
                foreach ($vcards as $key => $vcard) {
                    @unlink('assets/front/img/user/vcard/' . $vcard->profile_image);
                    @unlink('assets/front/img/user/vcard/' . $vcard->cover_image);
                    if ($vcard->user_vcard_projects()->count() > 0) {
                        foreach ($vcard->user_vcard_projects as $key => $project) {
                            @unlink('assets/front/img/user/projects/' . $project->image);
                            $project->delete();
                        }
                    }
                    if ($vcard->user_vcard_services()->count() > 0) {
                        foreach ($vcard->user_vcard_services as $key => $service) {
                            @unlink('assets/front/img/user/services/' . $service->image);
                            $service->delete();
                        }
                    }
                    if ($vcard->user_vcard_testimonials()->count() > 0) {
                        foreach ($vcard->user_vcard_testimonials as $key => $testimonial) {
                            @unlink('assets/front/img/user/testimonials/' . $testimonial->image);
                            $testimonial->delete();
                        }
                    }
                    $vcard->delete();
                }
            }
    
            if ($user->processes()->count() > 0) {
                $user->processes()->delete();
            }
    
            if ($user->blog_categories()->count() > 0) {
                $user->blog_categories()->delete();
            }
            
    
            if ($user->blogs()->count() > 0) {
                $blogs = $user->blogs()->get();
                foreach ($blogs as $key => $blog) {
                    @unlink('assets/front/img/user/blogs/' . $blog->image);
                    $blog->delete();
                }
            }
    
            if ($user->basic_setting()->count() > 0) {
                $bs = $user->basic_setting;
                @unlink('assets/front/img/user/' . $bs->breadcrumb);
                @unlink('assets/front/img/user/' . $bs->logo);
                @unlink('assets/front/img/user/' . $bs->preloader);
                @unlink('assets/front/img/user/' . $bs->favicon);
                @unlink('assets/front/img/user/qr/' . $bs->qr_image);
                @unlink('assets/front/img/user/qr/' . $bs->qr_inserted_image);
                $bs->delete();
            }
    
            if ($user->memberships()->count() > 0) {
                foreach($user->memberships as $key => $membership) {
                    @unlink('assets/front/img/membership/receipt/' . $membership->receipt);
                    $membership->delete();
                }
            }
                
        
            if ($user->brands()->count() > 0) {
                $brands = $user->brands()->get();
                foreach ($brands as $key => $brand) {
                    @unlink('assets/front/img/user/brands/' . $brand->brand_img);
                    $brand->delete();
                }
            }
                
        
            if ($user->user_contact()->count() > 0) {
                $contact = $user->user_contact;
                @unlink('assets/front/img/user/'.$contact->contact_form_image);
                $contact->delete();
            }
    
            if ($user->faqs()->count() > 0) {
                $user->faqs()->delete();
            }
    
            if ($user->footer_quick_links()->count() > 0) {
                $user->footer_quick_links()->delete();
            }
    
            if ($user->footer_texts()->count() > 0) {
                $user->footer_texts()->delete();
            }

            if ($user->menus()->count() > 0) {
                $user->menus()->delete();
            }
    
            if ($user->pages()->count() > 0) {
                $user->pages()->delete();
            }
                
        
            if ($user->hero_sliders()->count() > 0) {
                $sliders = $user->hero_sliders()->get();
                foreach ($sliders as $key => $slider) {
                    @unlink('assets/front/img/hero_slider/'.$slider->img);
                    $slider->delete();
                }
            }
    
            if ($user->hero_static()->count() > 0) {
                $static = $user->hero_static;
                @unlink('assets/front/img/hero_static/' . $static->img);
                $static->delete();
            }
    
            @unlink('assets/front/img/user/' . $user->photo);
            $user->delete();
        }

        Session::flash('success', 'Users deleted successfully!');
        return "success";
    }

    public function removeCurrPackage(Request $request) {
        $userId = $request->user_id;
        $user = User::findOrFail($userId);
        $currMembership = UserPermissionHelper::currMembOrPending($userId);
        $currPackage = Package::select('title')->findOrFail($currMembership->package_id);
        $nextMembership = UserPermissionHelper::nextMembership($userId);
        $be = BasicExtended::first();
        $bs = BasicSetting::select('website_title')->first();

        $today = Carbon::now();

        // just expire the current package
        $currMembership->expire_date = $today->subDay();
        $currMembership->modified = 1;
        if ($currMembership->status == 0) {
            $currMembership->status = 2;
        }
        $currMembership->save();
            
        // if next package exists
        if (!empty($nextMembership)) {
            $nextPackage = Package::find($nextMembership->package_id);

            $nextMembership->start_date = Carbon::parse(Carbon::today()->format('d-m-Y'));
            if ($nextPackage->term == 'monthly') {
                $nextMembership->expire_date = Carbon::parse(Carbon::today()->addMonth()->format('d-m-Y'));
            } elseif ($nextPackage->term == 'yearly') {
                $nextMembership->expire_date = Carbon::parse(Carbon::today()->addYear()->format('d-m-Y'));
            } elseif ($nextPackage->term == 'lifetime') {
                $nextMembership->expire_date = Carbon::parse(Carbon::maxValue()->format('d-m-Y'));
            }
            $nextMembership->save();
        }

        $this->sendMail(NULL, NULL, $request->payment_method, $user, $bs, $be, 'admin_removed_current_package', NULL, $currPackage->title);

        Session::flash('success', 'Current Package removed successfully!');
        return back();
    }


    public function sendMail($memb, $package, $paymentMethod, $user, $bs, $be, $mailType, $replacedPackage = NULL, $removedPackage = NULL) {

        if ($mailType != 'admin_removed_current_package' && $mailType != 'admin_removed_next_package') {
            $transaction_id = UserPermissionHelper::uniqidReal(8);
            $activation = $memb->start_date;
            $expire = $memb->expire_date;
            $info['start_date'] = $activation->toFormattedDateString();
            $info['expire_date'] = $expire->toFormattedDateString();
            $info['payment_method'] = $paymentMethod;

            $file_name = $this->makeInvoice($info,"membership",$user,NULL,$package->price,"Stripe",$user->phone,$be->base_currency_symbol_position,$be->base_currency_symbol,$be->base_currency_text,$transaction_id,$package->title,$memb);
        }

        $mailer = new MegaMailer();
        $data = [
            'toMail' => $user->email,
            'toName' => $user->fname,
            'username' => $user->username,
            'website_title' => $bs->website_title,
            'templateType' => $mailType
        ];
        
        if ($mailType != 'admin_removed_current_package' && $mailType != 'admin_removed_next_package') {
            $data['package_title'] = $package->title;
            $data['package_price'] = ($be->base_currency_text_position == 'left' ? $be->base_currency_text . ' ' : '') . $package->price . ($be->base_currency_text_position == 'right' ? ' ' . $be->base_currency_text : '');
            $data['activation_date'] = $activation->toFormattedDateString();
            $data['expire_date'] = Carbon::parse($expire->toFormattedDateString())->format('Y') == '9999' ? 'Lifetime' : $expire->toFormattedDateString();
            $data['membership_invoice'] = $file_name;
        }
        if ($mailType != 'admin_removed_current_package' || $mailType != 'admin_removed_next_package') {
            $data['removed_package_title'] = $removedPackage;
        }

        if (!empty($replacedPackage)) {
            $data['replaced_package'] = $replacedPackage;
        }

        $mailer->mailFromAdmin($data);
    }


    public function changeCurrPackage(Request $request) {
        $userId = $request->user_id;
        $user = User::findOrFail($userId);
        $currMembership = UserPermissionHelper::currMembOrPending($userId);
        $nextMembership = UserPermissionHelper::nextMembership($userId);

        $be = BasicExtended::first();
        $bs = BasicSetting::select('website_title')->first();
        
        $selectedPackage = Package::find($request->package_id);
        
        // if the user has a next package to activate & selected package is 'lifetime' package
        if (!empty($nextMembership) && $selectedPackage->term == 'lifetime') {
            Session::flash('membership_warning', 'To add a Lifetime package as Current Package, You have to remove the next package');
            return back();
        }

        // expire the current package
        $currMembership->expire_date = Carbon::parse(Carbon::now()->subDay()->format('d-m-Y'));
        $currMembership->modified = 1;
        if ($currMembership->status == 0) {
            $currMembership->status = 2;
        }
        $currMembership->save();

        // calculate expire date for selected package
        if ($selectedPackage->term == 'monthly') {
            $exDate = Carbon::now()->addMonth()->format('d-m-Y');
        } elseif ($selectedPackage->term == 'yearly') {
            $exDate = Carbon::now()->addYear()->format('d-m-Y');
        } elseif ($selectedPackage->term == 'lifetime') {
            $exDate = Carbon::maxValue()->format('d-m-Y');
        }
        // store a new membership for selected package
        $selectedMemb = Membership::create([
            'price' => $selectedPackage->price,
            'currency' => $be->base_currency_text,
            'currency_symbol' => $be->base_currency_symbol,
            'payment_method' => $request->payment_method,
            'transaction_id' => uniqid(),
            'status' => 1,
            'receipt' => NULL,
            'transaction_details' => NULL,
            'settings' => json_encode($be),
            'package_id' => $selectedPackage->id,
            'user_id' => $userId,
            'start_date' => Carbon::parse(Carbon::now()->format('d-m-Y')),
            'expire_date' => Carbon::parse($exDate),
            'is_trial' => 0,
            'trial_days' => 0,
        ]);

        // if the user has a next package to activate & selected package is not 'lifetime' package
        if (!empty($nextMembership) && $selectedPackage->term != 'lifetime') {
            $nextPackage = Package::find($nextMembership->package_id);

            // calculate & store next membership's start_date
            $nextMembership->start_date = Carbon::parse(Carbon::parse($exDate)->addDay()->format('d-m-Y'));

            // calculate & store expire date for next membership
            if ($nextPackage->term == 'monthly') {
                $exDate = Carbon::parse(Carbon::parse(Carbon::parse($exDate)->addDay()->format('d-m-Y'))->addMonth()->format('d-m-Y'));
            } elseif ($nextPackage->term == 'yearly') {
                $exDate = Carbon::parse(Carbon::parse(Carbon::parse($exDate)->addDay()->format('d-m-Y'))->addYear()->format('d-m-Y'));
            } else {
                $exDate = Carbon::parse(Carbon::maxValue()->format('d-m-Y'));
            }
            $nextMembership->expire_date = $exDate;
            $nextMembership->save();
        } 
        

        $currentPackage = Package::select('title')->findOrFail($currMembership->package_id);
        $this->sendMail($selectedMemb, $selectedPackage, $request->payment_method, $user, $bs, $be, 'admin_changed_current_package', $currentPackage->title);


        Session::flash('success', 'Current Package changed successfully!');
        return back();
    }

    public function addCurrPackage(Request $request) {
        $userId = $request->user_id;
        $user = User::findOrFail($userId);
        $be = BasicExtended::first();
        $bs = BasicSetting::select('website_title')->first();
        
        $selectedPackage = Package::find($request->package_id);

        // calculate expire date for selected package
        if ($selectedPackage->term == 'monthly') {
            $exDate = Carbon::now()->addMonth()->format('d-m-Y');
        } elseif ($selectedPackage->term == 'yearly') {
            $exDate = Carbon::now()->addYear()->format('d-m-Y');
        } elseif ($selectedPackage->term == 'lifetime') {
            $exDate = Carbon::maxValue()->format('d-m-Y');
        }
        // store a new membership for selected package
        $selectedMemb = Membership::create([
            'price' => $selectedPackage->price,
            'currency' => $be->base_currency_text,
            'currency_symbol' => $be->base_currency_symbol,
            'payment_method' => $request->payment_method,
            'transaction_id' => uniqid(),
            'status' => 1,
            'receipt' => NULL,
            'transaction_details' => NULL,
            'settings' => json_encode($be),
            'package_id' => $selectedPackage->id,
            'user_id' => $userId,
            'start_date' => Carbon::parse(Carbon::now()->format('d-m-Y')),
            'expire_date' => Carbon::parse($exDate),
            'is_trial' => 0,
            'trial_days' => 0,
        ]);

        $this->sendMail($selectedMemb, $selectedPackage, $request->payment_method, $user, $bs, $be, 'admin_added_current_package');

        Session::flash('success', 'Current Package has been added successfully!');
        return back();
    }

    public function removeNextPackage(Request $request) {
        $userId = $request->user_id;
        $user = User::findOrFail($userId);
        $be = BasicExtended::first();
        $bs = BasicSetting::select('website_title')->first();
        $nextMembership = UserPermissionHelper::nextMembership($userId);
        // set the start_date to unlimited
        $nextMembership->start_date = Carbon::parse(Carbon::maxValue()->format('d-m-Y'));
        $nextMembership->modified = 1;
        $nextMembership->save();

        $nextPackage = Package::select('title')->findOrFail($nextMembership->package_id);


        $this->sendMail(NULL, NULL, $request->payment_method, $user, $bs, $be, 'admin_removed_next_package', NULL, $nextPackage->title);

        Session::flash('success', 'Next Package removed successfully!');
        return back();
    }

    public function changeNextPackage(Request $request) {
        $userId = $request->user_id;
        $user = User::findOrFail($userId);
        $bs = BasicSetting::select('website_title')->first();
        $be = BasicExtended::first();
        $nextMembership = UserPermissionHelper::nextMembership($userId);
        $nextPackage = Package::find($nextMembership->package_id);
        $selectedPackage = Package::find($request->package_id);
        
        $prevStartDate = $nextMembership->start_date;
        // set the start_date to unlimited
        $nextMembership->start_date = Carbon::parse(Carbon::maxValue()->format('d-m-Y'));
        $nextMembership->modified = 1;
        $nextMembership->save();

        // calculate expire date for selected package
        if ($selectedPackage->term == 'monthly') {
            $exDate = Carbon::parse($prevStartDate)->addMonth()->format('d-m-Y');
        } elseif ($selectedPackage->term == 'yearly') {
            $exDate = Carbon::parse($prevStartDate)->addYear()->format('d-m-Y');
        } elseif ($selectedPackage->term == 'lifetime') {
            $exDate = Carbon::parse(Carbon::maxValue()->format('d-m-Y'));
        }

        // store a new membership for selected package
        $selectedMemb = Membership::create([
            'price' => $selectedPackage->price,
            'currency' => $be->base_currency_text,
            'currency_symbol' => $be->base_currency_symbol,
            'payment_method' => $request->payment_method,
            'transaction_id' => uniqid(),
            'status' => 1,
            'receipt' => NULL,
            'transaction_details' => NULL,
            'settings' => json_encode($be),
            'package_id' => $selectedPackage->id,
            'user_id' => $userId,
            'start_date' => Carbon::parse($prevStartDate),
            'expire_date' => Carbon::parse($exDate),
            'is_trial' => 0,
            'trial_days' => 0,
        ]);

        $this->sendMail($selectedMemb, $selectedPackage, $request->payment_method, $user, $bs, $be, 'admin_changed_next_package', $nextPackage->title);

        Session::flash('success', 'Next Package changed successfully!');
        return back();
    }

    public function addNextPackage(Request $request) {
        $userId = $request->user_id;

        $hasPendingMemb = UserPermissionHelper::hasPendingMembership($userId);
        if($hasPendingMemb) {
            Session::flash('membership_warning', 'This user already has a Pending Package. Please take an action (change / remove / approve / reject) for that package first.');
            return back();
        }

        $currMembership = UserPermissionHelper::userPackage($userId);
        $currPackage = Package::find($currMembership->package_id);
        $be = BasicExtended::first();
        $user = User::findOrFail($userId);
        $bs = BasicSetting::select('website_title')->first();
        
        $selectedPackage = Package::find($request->package_id);

        if ($currMembership->is_trial == 1) {
            Session::flash('membership_warning', 'If your current package is trial package, then you have to change / remove the current package first.');
            return back();
        }


        // if current package is not lifetime package
        if ($currPackage->term != 'lifetime') {
            // calculate expire date for selected package
            if ($selectedPackage->term == 'monthly') {
                $exDate = Carbon::parse($currMembership->expire_date)->addDay()->addMonth()->format('d-m-Y');
            } elseif ($selectedPackage->term == 'yearly') {
                $exDate = Carbon::parse($currMembership->expire_date)->addDay()->addYear()->format('d-m-Y');
            } elseif ($selectedPackage->term == 'lifetime') {
                $exDate = Carbon::parse(Carbon::maxValue()->format('d-m-Y'));
            }
            // store a new membership for selected package
            $selectedMemb = Membership::create([
                'price' => $selectedPackage->price,
                'currency' => $be->base_currency_text,
                'currency_symbol' => $be->base_currency_symbol,
                'payment_method' => $request->payment_method,
                'transaction_id' => uniqid(),
                'status' => 1,
                'receipt' => NULL,
                'transaction_details' => NULL,
                'settings' => json_encode($be),
                'package_id' => $selectedPackage->id,
                'user_id' => $userId,
                'start_date' => Carbon::parse(Carbon::parse($currMembership->expire_date)->addDay()->format('d-m-Y')),
                'expire_date' => Carbon::parse($exDate),
                'is_trial' => 0,
                'trial_days' => 0,
            ]);

            $this->sendMail($selectedMemb, $selectedPackage, $request->payment_method, $user, $bs, $be, 'admin_added_next_package');
        } else {
            Session::flash('membership_warning', 'If your current package is lifetime package, then you have to change / remove the current package first.');
            return back();
        }


        Session::flash('success', 'Next Package has been added successfully!');
        return back();
    }

    public function userTemplate(Request $request)
    {
        if ($request->template == 1) {
            $prevImg = $request->file('preview_image');
            $allowedExts = array('jpg', 'png', 'jpeg');
    
            $rules = [
                'serial_number' => 'required|integer',
                'preview_image' => [
                    'required',
                    function ($attribute, $value, $fail) use ($prevImg, $allowedExts) {
                        if (!empty($prevImg)) {
                            $ext = $prevImg->getClientOriginalExtension();
                            if (!in_array($ext, $allowedExts)) {
                                return $fail("Only png, jpg, jpeg image is allowed");
                            }
                        }
                    },
                ]
            ];
    
    
            $request->validate($rules);
        }

        $user = User::where('id',$request->user_id)->first();

        if ($request->template == 1) {
            if ($request->hasFile('preview_image')) {
                @unlink('assets/front/img/template-previews/' . $user->template_img);
                $filename = uniqid() . '.' . $prevImg->getClientOriginalExtension();
                $dir = 'assets/front/img/template-previews/';
                @mkdir($dir, 0775, true);
                $request->file('preview_image')->move($dir, $filename);
                $user->template_img = $filename;
            }
            $user->template_serial_number = $request->serial_number;
        } else {
            @unlink('assets/front/img/template-previews/' . $user->template_img);
            $user->template_img = NULL;
            $user->template_serial_number = 0;
        }
        $user->preview_template = $request->template;
        $user->save();
        Session::flash('success', 'Status updated successfully!');
        return back();
    }

    public function userUpdateTemplate(Request $request)
    {
        $prevImg = $request->file('preview_image');
        $allowedExts = array('jpg', 'png', 'jpeg');

        $rules = [
            'serial_number' => 'required|integer',
            'preview_image' => [
                function ($attribute, $value, $fail) use ($prevImg, $allowedExts) {
                    if (!empty($prevImg)) {
                        $ext = $prevImg->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    }
                },
            ]
        ];
    
    
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $user = User::where('id',$request->user_id)->first();

        
        if ($request->hasFile('preview_image')) {
            @unlink('assets/front/img/template-previews/' . $user->template_img);
            $filename = uniqid() . '.' . $prevImg->getClientOriginalExtension();
            $dir = 'assets/front/img/template-previews/';
            @mkdir($dir, 0775, true);
            $request->file('preview_image')->move($dir, $filename);
            $user->template_img = $filename;
        }
        $user->template_serial_number = $request->serial_number;
        $user->save();

        
        Session::flash('success', 'Status updated successfully!');
        return "success";
    }
}

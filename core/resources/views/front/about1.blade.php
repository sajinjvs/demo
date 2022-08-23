@extends('front.layout')

@section('pagename')
    - {{__('About Us')}}
@endsection

@section('meta-description', !empty($seo) ? $seo->pricing_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->pricing_meta_keywords : '')

@section('breadcrumb-title')
    {{__('About Us')}}
@endsection
@section('breadcrumb-link')
    {{__('About Us')}}
@endsection
@section('content')

<section class="saas-analysis pt-120 pb-80">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="choose-img mb-40">
                        <img data-src="https://shadobooks.versasoftsolutions.com/assets/front/img/62ee43b140f5c.png" class="img-fluid lazy entered loaded" alt="" data-ll-status="loaded" src="https://shadobooks.versasoftsolutions.com/assets/front/img/62ee43b140f5c.png">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="choose-content-box mb-40">
                        <div class="section-title-one section-title-two mb-20">
                            
                            <h2>We believe in Customer Trust</h2>
                        </div>
                                                <p>Shadobooks presents a functionally powerful CRM that caters to the end-to-end operational needs of any business. The tool is designed by a group of experts coming from diverse fields and thus offers a comprehensive solution that covers all aspects of finance, accounting and HRM in one.<br>
<br>
From its inception ten years ago, the developers of Shadobooks continuously strived to update the software to offer the best futuristic version at all times. Being a competitive alternative option, they offer all of its features at a way cheaper subscription.<br>
<br>
With impeccable customer support, an easy-to-use interface and complete implementation assistance, Shadobooks will offer you all the assistance you’re looking for at ease.  We have also built the tool for flexible customisation and easy operations.<br>
<br>
The CRM offers a wide range of functionalities and features that can aid different operations like billing, invoice tracking, inventory, expenses or revenue management on the go. </p>
                                            </div>
                </div>
            </div>
        </div>
    </section>





@endsection

@extends('landing.layout')

@section('title', app()->getLocale() === 'ar'
    ? 'الأسعار — CRM Innovation'
    : 'Pricing — CRM Innovation')
@section('body-class', '')

@section('content')

<div class="breadcrumb-area bg-cover" style="background-image: url('{{ asset('landing/img/bg/7.png') }}');">
    <div class="container">
        <div class="breadcrumb-inner">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="page-title">{{ __('crm.pricing_page_breadcrumb') }}</h2>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <ul class="page-list">
                        <li><a href="{{ route('landing') }}">{{ __('crm.breadcrumb_home') }}</a></li>
                        <li>{{ __('crm.pricing_page_breadcrumb') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pricing-area pd-top-90 pd-bottom-90">
    <div class="container">
        <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
            <h6 class="sub-title">{{ __('crm.pricing_page_subtitle') }}</h6>
            <h2 class="title">{!! __('crm.pricing_page_title_main') !!}</h2>
            <p class="content">{{ __('crm.pricing_page_desc') }}</p>
        </div>

        <div class="row justify-content-center">
            @foreach($plans as $plan)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="single-pricing-inner style-3 {{ $plan->is_featured ? 'price-active' : '' }} wow animated fadeInUp"
                    data-wow-duration="0.8s" data-wow-delay="{{ $loop->index * 0.2 }}s">
                    @if($plan->is_featured)
                    <span class="pricing-badge">{{ __('crm.pricing_most_popular') }}</span>
                    @endif
                    <h2 class="mb-1">
                        ${{ number_format($plan->monthly_price, 0) }}
                        <sub>{{ __('crm.pricing_per_month') }}</sub>
                    </h2>
                    <p class="text-muted" style="font-size:12px;">
                        ${{ number_format($plan->yearly_price, 0) }}{{ __('crm.pricing_per_year') }}
                        {{ app()->getLocale() === 'ar' ? 'تُدفع سنوياً' : 'billed annually' }}
                    </p>
                    <h5 class="mt-2">{{ $plan->name }}</h5>
                    @if($plan->description)
                    <p class="text-muted mb-3" style="font-size:13px;">{{ $plan->description }}</p>
                    @endif
                    <hr>
                    <ul>
                        <li>
                            <i class="fa fa-check"></i>
                            <strong>{{ $plan->max_users === 0 ? __('crm.pricing_unlimited') : $plan->max_users }}</strong>
                            {{ __('crm.pricing_users') }}
                        </li>
                        <li>
                            <i class="fa fa-check"></i>
                            <strong>{{ $plan->max_contacts === 0 ? __('crm.pricing_unlimited') : number_format($plan->max_contacts) }}</strong>
                            {{ __('crm.pricing_contacts_label') }}
                        </li>
                        @foreach($plan->features ?? [] as $feature)
                        <li><i class="fa fa-check"></i>{{ ucwords(str_replace('_', ' ', $feature)) }}</li>
                        @endforeach
                    </ul>
                    <a class="btn btn-black border-radius border-radius-0 w-100 mt-3"
                        href="{{ route('register') }}?plan={{ $plan->id }}">
                        {{ __('crm.pricing_start_trial') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- FAQ --}}
        <div class="row justify-content-center mt-5">
            <div class="col-lg-8">
                <div class="section-title text-center mb-4 wow animated fadeInUp" data-wow-duration="0.8s">
                    <h3 class="title">{{ __('crm.pricing_faq_title') }}</h3>
                </div>
                <div class="accordion accordion-inner style-2 accordion-icon-left" id="pricingFAQ">
                    @php
                    $faqs = [
                        ['q' => __('crm.faq_1_q'), 'a' => __('crm.faq_1_a')],
                        ['q' => __('crm.faq_2_q'), 'a' => __('crm.faq_2_a')],
                        ['q' => __('crm.faq_3_q'), 'a' => __('crm.faq_3_a')],
                        ['q' => __('crm.faq_4_q'), 'a' => __('crm.faq_4_a')],
                    ];
                    @endphp
                    @foreach($faqs as $i => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHead{{ $i }}">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq{{ $i }}"
                                aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                                aria-controls="faq{{ $i }}">
                                {{ $faq['q'] }}
                            </button>
                        </h2>
                        <div id="faq{{ $i }}"
                            class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                            aria-labelledby="faqHead{{ $i }}"
                            data-bs-parent="#pricingFAQ">
                            <div class="accordion-body">{{ $faq['a'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

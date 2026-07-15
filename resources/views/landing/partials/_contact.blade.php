@if($contactSection?->is_active)
@php
    $sectionText = fn ($field) => $contactSection->{$field}[$lang] ?? '';
    $contactBg = $contactSection->bg_image
        ? asset(ltrim(upload_general_url($contactSection->bg_image), '/'))
        : asset('landing/img/bg/contact-bg.png');
@endphp
<div class="contact-area pd-top-120 pd-bottom-90" id="contact">
    <div class="container">
        <div class="contact-inner-1">
            <div class="row align-items-center">
                <div class="col-lg-6 wow animated fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <div class="contact-inner text-center text-lg-end">
                        @if($sectionText('cta_subtitle'))
                            <h6 class="subtitle wow animated fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.3s">{{ $sectionText('cta_subtitle') }}</h6>
                        @endif
                        <h2 class="title wow animated fadeInRight arabic-title" data-wow-duration="0.8s" data-wow-delay="0.6s">
                            @foreach($contactSection->cta_headline ?? [] as $segment)
                                @php
                                    $segmentText = $segment['title'][$lang] ?? '';
                                    $isHighlighted = !empty($segment['check']);
                                @endphp
                                @if($isHighlighted)
                                    <span>{{ $segmentText }}</span>
                                @else
                                    {{ $segmentText }}
                                @endif
                            @endforeach
                        </h2>
                        @if($sectionText('cta_intro'))
                            <p class="content ps-xl-5 wow animated fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.9s">{{ $sectionText('cta_intro') }}</p>
                        @endif
                        <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-end mt-4 wow animated fadeInRight" data-wow-duration="0.8s" data-wow-delay="1.1s">
                            @if($contactSection->cta_btn1_text[$lang] ?? null)
                                <a class="btn btn-base" href="{{ $contactSection->cta_btn1_link ?: route('register') }}">{{ $contactSection->cta_btn1_text[$lang] }}</a>
                            @endif
                            @if($contactSection->cta_btn2_text[$lang] ?? null)
                                <a class="btn btn-border-base" href="{{ $contactSection->cta_btn2_link ?: '#demo' }}">{{ $contactSection->cta_btn2_text[$lang] }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <div class="section-title mb-0">
                        @if($sectionText('form_subtitle'))
                            <h6 class="sub-title">{{ $sectionText('form_subtitle') }}</h6>
                        @endif
                        @if($sectionText('form_title'))
                            <h2 class="title">{{ $sectionText('form_title') }}</h2>
                        @endif
                        @if($sectionText('form_intro'))
                            <p class="content">{{ $sectionText('form_intro') }}</p>
                        @endif
                        <form id="landing-contact-form" class="mt-4 landing-ajax-form" action="{{ route('contact.send') }}" method="POST"
                            data-success="{{ __('crm.contact_success') }}">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="single-input-inner style-border">
                                        <input type="text" name="name" placeholder="{{ __('crm.contact_name') }}" maxlength="100">
                                        <div class="landing-field-error" data-for="name"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="single-input-inner style-border">
                                        <input type="email" name="email" placeholder="{{ __('crm.contact_email') }}" maxlength="150">
                                        <div class="landing-field-error" data-for="email"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="single-input-inner style-border">
                                        <input type="tel" name="phone" placeholder="{{ __('crm.contact_phone') }}" maxlength="30">
                                        <div class="landing-field-error" data-for="phone"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="single-input-inner style-border">
                                        <textarea name="message" placeholder="{{ __('crm.contact_message') }}" maxlength="2000"></textarea>
                                        <div class="landing-field-error" data-for="message"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-black mt-0 w-100 border-radius-5 landing-submit-btn">{{ __('crm.contact_send') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-img" style="background-image: url({{ $contactBg }});"></div>
</div>
@endif

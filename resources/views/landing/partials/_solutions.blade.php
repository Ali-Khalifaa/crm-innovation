@if($solutionSection?->is_active && $solutionSlides->isNotEmpty() && $solutionPoints->isNotEmpty())
<div class="about-area pd-top-120 pd-bottom-90" id="solutions">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 wow animated fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.3s">
                <div class="solutions-slider-wrap">
                    <div class="hero-img-slider">
                        @foreach($solutionSlides as $index => $slide)
                        <div class="hero-slide{{ $index === 0 ? ' active' : '' }}" data-focus="center">
                            <div class="hero-img-frame">
                                @if($slide->image)
                                    <img class="main-img" src="{{ asset(ltrim(upload_general_url($slide->image), '/')) }}" alt="{{ $itemText($slide, 'title') }}">
                                @else
                                    <img class="main-img" src="{{ asset('landing/img/banner-3/1.png') }}" alt="{{ $itemText($slide, 'title') }}">
                                @endif
                            </div>
                            <div class="hero-slide-caption">
                                <span class="hero-slide-badge" aria-hidden="true"></span>
                                <div>
                                    <strong>{{ $itemText($slide, 'title') }}</strong>
                                    @if($itemText($slide, 'subtitle'))
                                        <p>{{ $itemText($slide, 'subtitle') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($solutionSlides->count() > 1)
                    <div class="hero-slider-dots" aria-label="شرائح الواجهة">
                        @foreach($solutionSlides as $index => $slide)
                        <button type="button" class="{{ $index === 0 ? 'active' : '' }}" aria-label="{{ $itemText($slide, 'title') }}"></button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.3s">
                <div class="section-title mb-0 ps-lg-5">
                    @if($solutionSection->subtitle[$lang] ?? null)
                        <h6 class="sub-title">{{ $solutionSection->subtitle[$lang] }}</h6>
                    @endif
                    <h2 class="title arabic-title">
                        @foreach($solutionSection->headline ?? [] as $segment)
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
                    @if($solutionSection->description[$lang] ?? null)
                        <p class="content mb-4">{{ $solutionSection->description[$lang] }}</p>
                    @endif
                    <ul class="feature-list">
                        @foreach($solutionPoints as $point)
                        <li><i class="fas fa-check-circle"></i> {{ $itemText($point, 'text') }}</li>
                        @endforeach
                    </ul>
                    @if(($solutionSection->button['text'][$lang] ?? null) && ($solutionSection->button['url'] ?? null))
                        <a class="btn btn-base mt-4" href="{{ $solutionSection->button['url'] }}">{{ $solutionSection->button['text'][$lang] }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

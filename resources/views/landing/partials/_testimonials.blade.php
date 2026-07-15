@if($testimonialSection?->is_active && $testimonials->isNotEmpty())
@php
    $testimonialBg = $testimonialSection->bg_image
        ? asset(ltrim(upload_general_url($testimonialSection->bg_image), '/'))
        : asset('landing/img/bg/11.png');
@endphp
<div class="testimonial-area pd-top-120 pd-bottom-90" id="testimonials" style="background-image: url({{ $testimonialBg }});">
    <div class="container">
        <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
            @if($testimonialSection->subtitle[$lang] ?? null)
                <h6 class="sub-title">{{ $testimonialSection->subtitle[$lang] }}</h6>
            @endif
            <h2 class="title arabic-title">
                @foreach($testimonialSection->headline ?? [] as $segment)
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
        </div>
        <div class="testimonial-slider-1 owl-carousel slider-control-round slider-control-dots slider-control-right-top">
            @foreach($testimonials as $item)
            <div class="item">
                <div class="single-testimonial-inner style-1 text-center">
                    <h5>{{ $itemText($item, 'name') }}</h5>
                    <p class="designation mb-3">{{ $itemText($item, 'designation') }}</p>
                    <div class="icon mb-2">
                        <img src="{{ asset('landing/img/icon/25.png') }}" alt="quote">
                    </div>
                    <p>{{ $itemText($item, 'review') }}</p>
                    <div class="ratting-inner mt-4">
                        @for($s = 1; $s <= 5; $s++)
                            <i class="fa fa-star{{ $s <= ($item->rating ?? 5) ? '' : '-o' }}"></i>
                        @endfor
                    </div>
                    @if($item->image)
                    <div class="thumb">
                        <img src="{{ asset(ltrim(upload_general_url($item->image), '/')) }}" alt="{{ $itemText($item, 'name') }}">
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if($faqSection?->is_active && $faqItems->isNotEmpty())
@php
    $faqBg = $faqSection->bg_image
        ? asset(ltrim(upload_general_url($faqSection->bg_image), '/'))
        : asset('landing/img/bg/3.png');
    $half = (int) ceil($faqItems->count() / 2);
    $faqLeft  = $faqItems->take($half);
    $faqRight = $faqItems->slice($half);
@endphp
<div class="faq-area bg-cover pd-top-120 pd-bottom-110" id="faq" style="background-image: url({{ $faqBg }});">
    <div class="container">
        <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
            @if($faqSection->subtitle[$lang] ?? null)
                <h6 class="sub-title">{{ $faqSection->subtitle[$lang] }}</h6>
            @endif
            <h2 class="title arabic-title">
                @foreach($faqSection->headline ?? [] as $segment)
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
            @if($faqSection->intro[$lang] ?? null)
                <p class="content">{{ $faqSection->intro[$lang] }}</p>
            @endif
        </div>
        <div class="row">
            <div class="col-lg-6 wow animated fadeInUp" data-wow-duration="0.8s">
                <div class="accordion accordion-inner style-2 accordion-icon-left" id="faqAccordionLeft">
                    @foreach($faqLeft as $index => $item)
                    @php
                        $collapseId = 'faqCollapseLeft' . $item->id;
                        $headingId  = 'faqHeadingLeft' . $item->id;
                        $isFirst    = $index === 0;
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="{{ $headingId }}">
                            <button class="accordion-button{{ $isFirst ? '' : ' collapsed' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                aria-expanded="{{ $isFirst ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                {{ $itemText($item, 'question') }}
                            </button>
                        </h2>
                        <div id="{{ $collapseId }}" class="accordion-collapse collapse{{ $isFirst ? ' show' : '' }}"
                            aria-labelledby="{{ $headingId }}" data-bs-parent="#faqAccordionLeft">
                            <div class="accordion-body">
                                {{ $itemText($item, 'answer') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6 wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">
                <div class="accordion accordion-inner style-2 accordion-icon-left" id="faqAccordionRight">
                    @foreach($faqRight as $item)
                    @php
                        $collapseId = 'faqCollapseRight' . $item->id;
                        $headingId  = 'faqHeadingRight' . $item->id;
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="{{ $headingId }}">
                            <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                aria-expanded="false" aria-controls="{{ $collapseId }}">
                                {{ $itemText($item, 'question') }}
                            </button>
                        </h2>
                        <div id="{{ $collapseId }}" class="accordion-collapse collapse"
                            aria-labelledby="{{ $headingId }}" data-bs-parent="#faqAccordionRight">
                            <div class="accordion-body">
                                {{ $itemText($item, 'answer') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

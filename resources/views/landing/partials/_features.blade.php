@if($featureSection?->is_active && $featureItems->isNotEmpty())
<div class="service-area bg-gray pd-top-120 pd-bottom-90" id="features">
    <div class="container">
        <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
            @if($featureSection->subtitle[$lang] ?? null)
                <h6 class="sub-title">{{ $featureSection->subtitle[$lang] }}</h6>
            @endif
            <h2 class="title arabic-title">
                @foreach($featureSection->headline ?? [] as $segment)
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
        <div class="row">
            @foreach($featureItems as $index => $item)
            <div class="col-lg-3 col-md-4 col-sm-6 wow animated fadeInUp" data-wow-duration="0.8s" @if($index > 0) data-wow-delay="{{ number_format($index * 0.3, 1) }}s" @endif>
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="{{ $itemImage($item->image) }}" alt="{{ $itemText($item, 'title') }}">
                    </div>
                    <h5>{{ $itemText($item, 'title') }}</h5>
                    <p>{{ $itemText($item, 'description') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

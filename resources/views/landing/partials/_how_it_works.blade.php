@if($howSection?->is_active && $howItems->isNotEmpty())
<div class="work-process-area pd-top-120 pd-bottom-90" id="how-it-works">
    <div class="container">
        <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
            @if($howSection->subtitle[$lang] ?? null)
                <h6 class="sub-title">{{ $howSection->subtitle[$lang] }}</h6>
            @endif
            <h2 class="title arabic-title">
                @foreach($howSection->headline ?? [] as $segment)
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
        <div class="row steps-row">
            @foreach($howItems as $index => $item)
            <div class="col-lg-4 col-md-6 wow animated fadeInUp" data-wow-duration="0.8s" @if($index > 0) data-wow-delay="{{ number_format($index * 0.3, 1) }}s" @endif>
                <div class="step-card">
                    <div class="step-number">{{ $item->sort_order }}</div>
                    <div class="details text-center">
                        <div class="step-icon">
                            <img src="{{ $itemImage($item->image) }}" alt="{{ $itemText($item, 'title') }}">
                        </div>
                        <h5 class="mb-3">{{ $itemText($item, 'title') }}</h5>
                        <p>{{ $itemText($item, 'description') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

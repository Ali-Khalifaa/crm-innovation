@if($statSection?->is_active && $statItems->isNotEmpty())
<div class="counter-area bg-relative bg-cover pd-top-110 pd-bottom-100" id="statistics" style="background-image: url({{ $statBg }});">
    <div class="container pd-bottom-90">
        <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
            @if($statSection->subtitle[$lang] ?? null)
                <h6 class="sub-title">{{ $statSection->subtitle[$lang] }}</h6>
            @endif
            <h2 class="title text-white arabic-title">
                @foreach($statSection->headline ?? [] as $segment)
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
            @foreach($statItems as $index => $item)
            <div class="col-lg-3 col-md-6 wow animated fadeInUp" data-wow-duration="0.8s" @if($index > 0) data-wow-delay="{{ number_format($index * 0.3, 1) }}s" @endif>
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="{{ $itemImage($item->image) }}" alt="{{ $item->label[$lang] ?? '' }}">
                    </div>
                    <div class="stat-number">{{ ($item->value[$lang] ?? '') . ($item->suffix ?? '') }}</div>
                    <div class="stat-label">{{ $item->label[$lang] ?? '' }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

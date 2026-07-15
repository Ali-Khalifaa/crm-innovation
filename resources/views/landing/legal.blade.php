@extends('landing.layouts.Index.master')

@php
    $locale = app()->getLocale();
    $lang   = $locale === 'ar' ? 'ar' : 'en';
    $title  = $page->title[$lang] ?? '';
    $content = $page->content[$lang] ?? '';
@endphp

@section('title', $title)

@section('content')
<div class="legal-page-area pd-top-120 pd-bottom-90">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-title text-center mb-4">
                    <h1 class="title arabic-title">{{ $title }}</h1>
                </div>
                <div class="legal-page-content bg-white p-4 p-lg-5 rounded shadow-sm">
                    {!! $content !!}
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('landing') }}" class="btn btn-border-base">{{ __('crm.nav_home') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
   @include('layouts.header')
    @include('welcome.hero')
    @include('welcome.benefits')
    @include('welcome.how_it_works')
    @include('welcome.templates')
    @include('welcome.pricing')
    @include('welcome.testimonials')
    @include('welcome.cta')
    @include('welcome.faq')
       @include('layouts.footer')
@endsection
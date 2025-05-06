
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
         <show-news-component :initial-article="{{ json_encode($news) }}"></show-news-component>
    </div>
</div>
@endsection
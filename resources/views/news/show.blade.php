@extends('layouts.app')

@section('content')
<div class="container py-4">
  <a href="{{ route('news.index') }}" class="btn btn-link mb-3">&larr; Back to All News</a>

  <div class="card mb-4">
    @if($news->url_to_image)
      <img src="{{ $news->url_to_image }}"
           class="card-img-top"
           alt="{{ $news->title }}">
    @endif

    <div class="card-body">
      <h2 class="card-title">{{ $news->title }}</h2>
      <p class="text-muted">
        By <strong>{{ $news->author ?? 'Unknown' }}</strong>
        on {{ \Carbon\Carbon::parse($news->published_at)
                    ->format('F j, Y \a\t g:i A') }}
      </p>
      <p>{!! nl2br(e($news->content)) !!}</p>
      <a href="{{ $news->url }}"
         class="btn btn-outline-secondary"
         target="_blank">
        View Original
      </a>
    </div>
  </div>
</div>
@endsection

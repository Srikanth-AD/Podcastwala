@extends('app')

@section('content')

  @if($items)
    <div id="player-container" class="container-fluid">
      <div class="col-md-3"></div>
      <div class="col-md-6">
        <div class="now-playing">
          <h4 class="podcast-item-title"></h4>
        </div>
        <audio id='player' controls preload="none">
            <source src="" type="audio/mpeg">
          </audio>
      </div>
      <div class="col-md-3"></div>
    </div>
  @endif

  <div class="main container-fluid container-podcast-list">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        @if($items)
          @foreach ($items as $item)
            <div class="row podcast-item-row">
              <div class="col-md-3 podcast-thumbnail-container">
                <img class="podcast-thumbnail" width="75" height="75" 
                  src="{{asset(App\Item::find($item->id)->podcast->feed_thumbnail_location)}}" />
                <p><small>{{ date_format(date_create($item->published_at),'jS M Y') }}</small></p>
              </div>
              <div class="col-md-9">
                <h4 class="podcast-title"><small>{{App\Item::find($item->id)->podcast->name}}</small></h4>
                <h3 class="podcast-item-title"> 
                  <a target="_blank" href="{{ $item->url }}">{{ $item->title }}</a>            
                </h3>
                <p class="podcast-item-description">{{ $item->description}} 
                    <br/>
                    <a class="read-more" target="_blank" href="{{ $item->url }}"><small>Read More</small></a>
                </p>
                <div class="player-action-list">
                    <ul class="list-inline">
                      <li class='play' data-src='{{ $item->audio_url}}'>
                        <button type="button" class="btn-sm btn-primary">Play</button>
                      </li>
                      <li class="pause">
                        <button type="button" class="btn-sm btn-primary">Pause</button>
                      </li>
                  </ul>
                </div>
              </div>
            </div>
        @endforeach
        @else 
          <p class="text-white">Please <a href="{{ url('/podcast/manage') }}">add a feed</a> to view podcasts here...</p>
        @endif
    </div>
    <div class="col-md-3">      
    </div>
  </div>
  @section('js-footer')
    <script>
    jQuery(document).ready(function($) {
      $('.podcast-item-row .play').on('click', function() {
        $('#player source').attr('src', $(this).attr('data-src'));
        $('#player').trigger('load').trigger('play');
        $('#player-container .now-playing .podcast-item-title').text(
          'Now playing - ' + 
          $(this).parents('.podcast-item-row').find('.podcast-item-title > a').text());
        $('.podcast-item-row').removeClass('active');
        $(this).parents('.podcast-item-row').addClass('active');
      });
      $('.podcast-item-row .pause').on('click', function() {
        $('#player').trigger('pause');
      });
    });
    </script>
  @endsection
@endsection
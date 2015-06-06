@extends('app')

@section('content')
  <div class="main container-fluid container-podcast-list">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        @foreach ($items as $item)
          <div class="row podcast-item-row">
            <div class="col-md-2">
              <img class="podcast-thumbnail" width="75" height="75" src="{{$item->get_feed()->get_image_url()}}" />
              <p><small>{{ $item->get_date('jS F Y') }}</small></p>
            </div>
            <div class="col-md-8">
              <h4 class="podcast-title"><small>{{$item->get_feed()->get_title()}}</small></h4>
              <h3 class="podcast-item-title"> 
                <a target="_blank" href="{{ $item->get_permalink() }}">{{ $item->get_title() }}</a>            
              </h3>
              <p>{{str_limit($item->get_description(),100)}} 
                  <a class="read-more" target="_blank" href="{{ $item->get_permalink() }}"><small>Read More</small></a>
              </p>
            </div>
            <div class="col-md-2 player-action-list">
              <ul class="list-unstyled">
                <li class='play' data-src='{{ $item->get_enclosure()->get_link() }}'>
                  <button type="button" class="btn btn-default">Play</button>
                </li>
                <li class="pause">
                  <button type="button" class="btn btn-default">Pause</button>
                </li>
              </ul>
            </div>
          </div>
      @endforeach
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
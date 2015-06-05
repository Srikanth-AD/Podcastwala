@extends('app')

@section('content')
  <div class="main container-fluid">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        @foreach ($items as $item)
          <div class="item row">
            <div class="col-md-2">
              <h5>{{$item->get_feed()->get_title()}}</h5>
              <img width="50" height="50" src="{{$item->get_feed()->get_image_url()}}" />
              <p><small>{{ $item->get_date('jS F Y') }}</small></p>
            </div>
            <div class="col-md-8">
              <h3>            
                <a target="_blank" href="{{ $item->get_permalink() }}">{{ $item->get_title() }}</a>            
              </h3>              
              <p>{{str_limit($item->get_description(),100)}} 
                  <a class="read-more" target="_blank" href="{{ $item->get_permalink() }}"><small>Read More</small></a>
              </p>
            </div>
            <div class="col-md-2">
              <p class='play' data-src='{{ $item->get_enclosure()->get_link() }}'>
                <img alt="Play" title="Play" src="{{ asset('/css/icons/play.png') }}" width="30" height="30" />
              </p>
              <p class="pause">pause</p>
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
      $('.item .play').on('click', function() {
        $('#player source').attr('src', $(this).attr('data-src'));
        $('#player').trigger('load').trigger('play');
      });
      $('.item .pause').on('click', function() {
        $('#player').trigger('pause');
      });
    });
    </script>
  @endsection
@endsection
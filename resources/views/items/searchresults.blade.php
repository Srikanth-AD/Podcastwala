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
	                <img class="podcast-thumbnail" width="100" height="100"
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
	                        <li class="mark-as-favorite" data-src="{{$item->id}}">
	                          @if($item->is_mark_as_favorite)
	                            <img width="24" height="24" alt="favorited" src="{{asset('css/icons/ic_favorite_white_36dp.png')}}" /> <span>Favorited</span>
	                            @else
	                              <img width="24" height="24" alt="mark as favorite" src="{{asset('css/icons/ic_favorite_grey600_36dp.png')}}" /> <span>Mark as Fav</span>
	                          @endif
	                        </li>
	                        <li class='play' data-src='{{ $item->audio_url}}'>
	                            <img width="24" height="24" alt="play" src="{{asset('css/icons/ic_play_circle_filled_white_36dp.png')}}" /> <span>Play</span>
	                        </li>
	                    </ul>
	                </div>
	              </div>
	            </div>
        	@endforeach


      	@endif
      	</div>
    <div class="col-md-3">
    </div>
  </div>
  @section('js-footer')
    <script>
    jQuery(document).ready(function($) {
      $('.podcast-item-row .play').on('click', function() {
        $('#player-container').css('display','block');
        $('#player source').attr('src', $(this).attr('data-src'));
        $('#player').trigger('load').trigger('play');
        $('#player-container .now-playing .podcast-item-title').text(
          'Now playing - ' +
          $(this).parents('.podcast-item-row').find('.podcast-item-title > a').text());
        $('.podcast-item-row').removeClass('active');
        $(this).parents('.podcast-item-row').addClass('active');
      });
    });

    $('.mark-as-favorite').on('click', function() {
      var itemId = $(this).attr('data-src');
      $.ajax({
          type: "POST",
          cache: false,
          url: "/item/mark-as-favorite",
          data: {
              'itemId': itemId,
              '_token': "{{ csrf_token() }}"
          },
          success: function(result) {
              if(result.status === 1)
              {
                // change fav img
                if(result.currentValue === true)
                {
                  $(".mark-as-favorite[data-src=" + itemId + "]").find('img').attr('src','{{asset('css/icons/ic_favorite_white_36dp.png')}}');
                  $(".mark-as-favorite[data-src=" + itemId + "] span").text('Favorited');
                } else {
                  $(".mark-as-favorite[data-src=" + itemId + "]").find('img').attr('src','{{asset('css/icons/ic_favorite_grey600_36dp.png')}}');
                  $(".mark-as-favorite[data-src=" + itemId + "] span").text('Mark as Fav');
                }
              }
          }
      });
    });
    </script>
  @endsection


@endsection
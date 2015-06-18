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
    <div class="col-md-2"></div>
    <div class="col-md-8">
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
                        <li class="mark-all-prev-read" data-src="{{$item->id}}">
                          <img width="24" height="24" alt="mark all as read" src="{{asset('css/icons/ic_done_all_white_36dp.png')}}" /> <span>Mark all previous as read</span>
                        </li>
                        <li class="mark-as-read" data-src="{{$item->id}}">
                            <img width="24" height="24" alt="mark as read" src="{{asset('css/icons/ic_done_white_36dp.png')}}" /> <span>Mark as read</span>
                        </li>
                        <li class='play' data-src='{{ $item->audio_url}}'>
                            <img width="24" height="24" alt="play" src="{{asset('css/icons/ic_play_circle_filled_white_36dp.png')}}" /> <span>Play</span>
                        </li>
                    </ul>
                </div>
              </div>
            </div>
        @endforeach

        @if($items)
          <div class="row container-fluid">
              <?php echo $items->render()?>
          </div>
        @endif

        @else
          <p class="text-white">Please <a href="{{ url('/podcast/manage') }}">add a feed</a> to view podcasts here...</p>
      @endif
    </div>
    <div class="col-md-2">
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

    $('.mark-as-read').on('click', function() {
      if (confirm('Are you sure you want to mark this as read?')) {
          var itemId = $(this).attr('data-src');
          var itemRow = $(".mark-as-read[data-src=" + itemId + "]").parents(".podcast-item-row");
          $.ajax({
              type: "POST",
              cache: false,
              url: "/item/mark-as-read",
              data: {
                  'itemId': itemId,
                  '_token': "{{ csrf_token() }}"
              },
              success: function(result) {
                  if(result.status === 1)
                  {
                      $(itemRow).fadeOut(1000);
                  }
              }
          });
      }
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

    $('.mark-all-prev-read').on('click', function() {
      if (confirm('Are you sure you want to mark all previous episodes in this podcast as read?')) {
          var itemId = $(this).attr('data-src');
          $.ajax({
              type: "POST",
              cache: false,
              url: "/item/mark-all-prev-read",
              data: {
                  'itemId': itemId,
                  '_token': "{{ csrf_token() }}"
              },
              success: function(result) {
                  if(result.status === 1)
                  {
                    for(var i = 0; i < result.data.length; i++)
                    {
                      if($(".mark-all-prev-read[data-src=" + result.data[i] + "]"))
                      {
                        $(".mark-all-prev-read[data-src=" + result.data[i] + "]")
                        .parents(".podcast-item-row")
                        .fadeOut(1000);
                      }
                    }
                  }
              }
          });
      }
    });
    </script>
  @endsection
@endsection
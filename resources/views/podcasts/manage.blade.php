@extends('app')

@section('content')
<div class="container-fluid main container-podcast-manage">
    <div class="col-md-9">
        <h3 class="page-title">Manage Podcast Feeds</h3>
        <hr/>
        @if(DB::table('podcasts')->where('user_id','=',Auth::user()->id)->count() > 0)
            @foreach(DB::table('podcasts')->where('user_id','=',Auth::user()->id)->get() as $cast)
                <div class="col-md-4">
                    <div class="podcast-container">
                        <span class="podcast-added-on">Added on {{ date('F d, Y', strtotime($cast->created_at)) }}</span>
                        <h4 class="podcast-title">{{$cast->name}}</h4>
                        <a target="_blank" href="{{$cast->web_url}}">
                            <img class="podcast-thumbnail" width="100" height="100" 
                             src="{{asset($cast->feed_thumbnail_location)}}" />
                        </a>
                        <br/>
                        <div class="podcast-action-list">
                            <ul class="list-inline">
                                <li class='feed-delete' data-feed-machine-name="{{$cast->machine_name}}">
                                    <img width="20" height="20" alt="Delete" 
                                    src="{{asset('css/icons/ic_clear_white_36dp.png')}}" /> Delete
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif  
    </div>
    <div class="row container-fluid">
        <div class="col-md-6">
            <h4 class="section-title">Add a podcast feed</h4>
            {!! Form::model($podcast = new \App\Podcast, ['method' =>'POST','action' => ['PodcastController@add']]) !!}
                <div class="form-group">
                    {!! Form::text('feed_url', null, 
                    ['class' => 'form-control','required','placeholder' => 'Enter a Podcast Feed Url here: http://example.com/feed']) !!}
                </div>
                
                <div class="form-group">
                    {!! Form::submit('Add Feed', ['class' => 'btn btn-primary']) !!}
                </div>
            {!! Form::close() !!} 
        </div> 
    </div>
</div>
@stop
@section('js-footer')
    <script>
    jQuery(document).ready(function($) {
        $('.feed-delete').on('click', function() {
            if (confirm('Are you sure you want to delete this feed?')) {
                var feedMachineName = $.trim($(this).attr('data-feed-machine-name'));
                $.ajax({
                    type: "POST",
                    cache: false,
                    url: "/podcast/delete",
                    data: {
                        'feedMachineName': feedMachineName,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(result) {
                        if(result.status === 1)
                        {
                            location.reload(); // @todo add a response msg
                        }             
                    }
                });
            }
        });
    });
    </script>
@stop

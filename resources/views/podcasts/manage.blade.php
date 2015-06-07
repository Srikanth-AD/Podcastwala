@extends('app')

@section('content')
<div class="container-fluid main container-podcast-manage">
    <div class="col-md-6">
        <h3>Manage Podcast Feeds</h3>
        <hr/>
        @if(App\Podcast::all()->count() > 0)
             <table class="table">
                <thead>
                    <tr>
                        <th>Created on</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(App\Podcast::all() as $cast)
                        <tr>
                            <td>{{ date('F d, Y', strtotime($cast->created_at)) }}</td>
                            <td>{{$cast->name}}</td>
                            <td><button class="feed-delete" data-feed-machine-name="{{$cast->machine_name}}" type="button" class="btn btn-default">Delete</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h4>Add a feed</h4>
        {!! Form::model($podcast = new \App\Podcast, ['method' =>'POST','action' => ['PodcastController@add']]) !!}
            <div class="form-group">
                {!! Form::text('feed_url', null, 
                ['class' => 'form-control','required','placeholder' => 'Enter Feed Url here: http://example.com/feed']) !!}
            </div>
            
            <div class="form-group">
                {!! Form::submit('Add Feed', ['class' => 'btn btn-primary']) !!}
            </div>
        {!! Form::close() !!}     
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

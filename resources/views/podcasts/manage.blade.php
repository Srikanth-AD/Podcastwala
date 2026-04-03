@extends('app')

@section('content')
<div class="container-fluid main container-podcast-manage">
    <div class="col-md-9">
        <h3 class="page-title">Manage Podcast Feeds</h3>
        <hr/>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($podcasts->count())
            @foreach($podcasts as $cast)
                <div class="col-md-4">
                    <div class="podcast-container">
                        <span class="podcast-added-on">Added on {{ $cast->created_at->format('F d, Y') }}</span>
                        <h4 class="podcast-title">{{ $cast->name }}</h4>
                        <a target="_blank" href="{{ $cast->web_url }}">
                            <img class="podcast-thumbnail" width="100" height="100"
                             src="{{ asset($cast->feed_thumbnail_location) }}" />
                        </a>
                        <br/>
                        <div class="podcast-action-list">
                            <ul class="list-inline">
                                <li class='feed-delete' data-feed-machine-name="{{ $cast->machine_name }}">
                                    <img width="20" height="20" alt="Delete"
                                    src="{{ asset('css/icons/ic_clear_white_36dp.png') }}" /> Delete
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
            <form method="POST" action="{{ route('podcast.add') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="feed_url" class="form-control" required
                        placeholder="Enter a Podcast Feed URL here: http://example.com/feed"
                        value="{{ old('feed_url') }}">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Add Feed</button>
                </div>
            </form>
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
                    url: "{{ route('podcast.delete') }}",
                    data: { 'feedMachineName': feedMachineName },
                    success: function(result) {
                        if(result.status === 1) {
                            location.reload();
                        }
                    }
                });
            }
        });
    });
    </script>
@stop

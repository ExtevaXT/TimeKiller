@extends('index')
@section('content')
    @foreach (\App\Resource\Resource::load()->sortBy('name') as $item)
        {{dd($item)}}
    @endforeach

@endsection

@extends('index')
@section('content')
    <div class="d-grid" style="grid-template-columns: repeat(10, 1fr)">
        @foreach($plot as $instance)
            <span> {{$instance->instance ?? 'empty'}}</span>
        @endforeach
    </div>
@endsection

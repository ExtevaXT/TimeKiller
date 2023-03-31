@extends('index')
@section('content')
    <div>Mine seed: {{$seed}}</div>
    <livewire:mine :mine="$mine"/>
@endsection

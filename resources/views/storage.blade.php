@extends('index')
@section('content')
    <div class="d-grid" style="grid-template-columns: repeat(10, 1fr)">
        @foreach($slots as $slot)
            <div class="slot uk-height-small uk-width-small m-1 uk-background-muted">
                {{$slot != null ? $slot->item()->name. ' '. $slot->amount : null}}
            </div>
        @endforeach
    </div>

@endsection


@foreach (\App\Resource\Resource::load()->sortBy('name') as $item)
    <div>{{$item->name}}</div>
@endforeach

<div class="mine">
    @foreach($mine as $line)
        <div class="line" style="display: flex">
            @foreach($line as $ore)
                <div class="ore" style="width: 32px; height: 32px; margin: 1px">
                    @if($ore == null)
                        <div title="Stone" style="background: darkgrey; width: 100%; height: 100%" wire:click="collect('{{ 'Stone' }}')"></div>
                    @else
                        <div title="{{$ore->name}}" style="background-color: {{substr(dechex(crc32($ore->name)), 0, 6)}}; width: 100%; height: 100%" wire:click="collect('{{ $ore->name }}')"></div>
                    @endif
                </div>

            @endforeach
        </div>
    @endforeach
</div>

<div class="mine">
    @foreach($mine as $line)
        <div class="d-flex line">
            @foreach($line as $ore)
                <div class="uk-height-small uk-width-small ore m-1">
                    @if($ore == null)
                        <div title="Stone" class="w-100 h-100 uk-background-muted" wire:click="collect('{{ 'Stone' }}')"></div>
                    @else
                        <div title="{{$ore->name}}" class="w-100 h-100" style="background-color: {{substr(dechex(crc32($ore->name)), 0, 6)}}" wire:click="collect('{{ $ore->id }}')"></div>
                    @endif
                </div>

            @endforeach
        </div>
    @endforeach
</div>

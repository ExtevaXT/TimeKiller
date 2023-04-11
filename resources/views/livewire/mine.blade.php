<div class="mine">
    @foreach($mine as $line)
        <div class="d-flex line">
            @foreach($line as $ore)
                <div class="uk-height-small uk-width-small ore m-1">
                    @if($ore == null)
                        <div title="Stone" class="w-100 h-100 uk-background-muted" wire:click="collect('{{ 'Stone' }}')"></div>
                    @else
                        <div onclick="this.style.display = 'none'" title="{{((object)$ore)->name}}" class="w-100 h-100" style="background-color: {{substr(dechex(crc32(((object)$ore)->name)), 0, 6)}}" wire:click="collect('{{ ((object)$ore)->id }}')"></div>
                    @endif
                </div>

            @endforeach
        </div>
    @endforeach
</div>

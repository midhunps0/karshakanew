@props(['sgf'])
<div x-data="{
        xpand: false
    }">
    <div class="pl-5 font-bold text-secondary opacity-80 flex flex-row space-x-8 my-2">
        <span>{{$sgf->name}}</span>
        <button @click.prevent.stop="xpand = true;" type="button" class="btn btn-xs" x-show="!xpand">
            <x-easyadmin::display.icon icon="easyadmin::icons.plus" height="h-4" width="w-4"/>
        </button>
        <button @click.prevent.stop="xpand = false;" type="button" class="btn btn-xs" x-show="xpand">
            <x-easyadmin::display.icon icon="easyadmin::icons.minus" height="h-4" width="w-4"/>
        </button>
    </div>
    <div class="overflow-hidden transition-all duration-500" :style="xpand ? 'height: {{(count($sgf->subGroups) + count($sgf->accounts)) * 20}}px;' : 'height: 0px'">
        @foreach ($sgf->subGroups as $sg)
            <x-partials.account_group :sgf="$sg" />
        @endforeach
        @foreach ($sgf->accounts as $sga)
            <div class="pl-10">
                <a href="" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('accounts.account.statement')}}'+'?account_id='+{{$sga->id}}, route: 'accounts.account.statement'});" class="cursor-pointer">
                    {{$sga->name_with_district}}
                </a>
            </div>
        @endforeach
    </div>
</div>

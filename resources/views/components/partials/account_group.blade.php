@props(['sgf'])
<div x-data="{
        xpand: true,
        elid: '',
        el: null,
        elMaxHeight: 0,
        expand() {
            this.xpand = true;
            this.el.style.height = `${this.elMaxHeight}px`;
            console.log(this.el.style);
        },
        collapse() {
            this.el.style.height = '0px';
            setTimeout(() => {
                this.xpand = false;
            }, 300);

        },
        {{-- getInnerHeight(elm){
            var computed = getComputedStyle(elm);
                padding = parseInt(computed.paddingTop) + parseInt(computed.paddingBottom);

            return elm.clientHeight - padding
          } --}}
    }"
    x-init="
        elid = 'acg-'+'{{$sgf->id}}';
        $nextTick(() => {
            el = document.getElementById(elid);
            elMaxHeight = el.clientHeight;
            collapse();
            console.log('mh');
            console.log(elMaxHeight);
        });
    "
    >
    <div class="font-bold text-secondary opacity-80 flex flex-row space-x-8 my-1 bg-base-200 p-2 rounded-md">
        <div class="flex-grow">
            <span>{{$sgf->name}}</span>
        </div>
            <div>
            <button @click.prevent.stop="expand();" type="button" class="btn btn-xs" x-show="!xpand">
                <x-easyadmin::display.icon icon="easyadmin::icons.plus" height="h-4" width="w-4"/>
            </button>
            <button @click.prevent.stop="collapse()" type="button" class="btn btn-xs" x-show="xpand">
                <x-easyadmin::display.icon icon="easyadmin::icons.minus" height="h-4" width="w-4"/>
            </button>
        </div>
    </div>
    <div :id="elid" class="overflow-hidden transition-all duration-500" >
        @foreach ($sgf->subGroups as $sg)
            <div class="pl-10">
                <x-partials.account_group :sgf="$sg" />
            </div>
        @endforeach
        @foreach ($sgf->accounts as $sga)
            <div class="pl-10">
                <a href="" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('accounts.account.statement')}}'+'?account_id='+{{$sga->id}}, route: 'accounts.account.statement'});" class="cursor-pointer">
                    {{$sga->name_with_district}}
                </a>
            </div>
        @endforeach
        @if (count($sgf->accounts) == 0 && count($sgf->subGroups) == 0)
            <div class="pl-10 text-warning opacity-80">No items in this group</div>
        @endif
    </div>
</div>

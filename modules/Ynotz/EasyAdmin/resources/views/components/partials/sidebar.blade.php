<div x-data="{hidden: false}"
    x-init="hidden = window.innerWidth < 768;"
    @sidebarvisibility.window="hidden=$event.detail.hidden;"
    @resize.window="hidden = window.innerWidth < 768; if(hidden){
        $dispatch('sidebarresize', {'collapsed': false});
    }"
    class="overflow-x-hidden fixed top-0 left-0 z-50 md:relative bg-base-100 md:w-auto min-w-fit ransition-all h-full"
    :class="hidden ? 'hidden' : 'md:block w-full'">
    <div x-show="!hidden" class="md:hidden w-full text-right pt-2 fixed top-2 right-2 z-20">
        <button x-show="!hidden" @click.prevent.stop="hidden=true;" class="btn btn-md text-warning"><x-easyadmin::display.icon icon="easyadmin::icons.close"/></button>
    </div>
    <ul x-show="!hidden" x-transition class="mt-20 md:mt-0">
        @foreach ($sidebar_data as $item)
            @if ($item['type'] == 'menu_group')
                @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
                <div x-data="{
                    group_expand: false,
                    elid: '',
                    el: null,
                    elMaxHeight: '',
                    toggleMg(){
                        if(!this.group_expand) {
                            this.expand();
                        } else {
                            this.collapse();
                        }
                    },
                    expand() {
                        this.group_expand = true;
                        this.el.style.height = `${this.elMaxHeight}px`;
                    },
                    collapse() {
                        this.el.style.height = '0px';
                        setTimeout(() => {
                            this.group_expand = false;
                        }, 300);
                    },
                }"
                x-init="
                    elid = 'mg-'+'{{$loop->index}}';
                    $nextTick(() => {
                        el = document.getElementById(elid);
                        elMaxHeight = el.clientHeight;
                        collapse();
                    });
                ">
                <li class="flex flex-row items-center justify-start font-bold">
                    <x-easyadmin::partials.menu-group title="{{$item['title']}}" icon="{{$item['icon']}}"/>
                    <span class="transition-all" :class="group_expand ? 'rotate-180' : 'rotate-0'" @click.prevent.stop="toggleMg();">
                        <x-easyadmin::display.icon icon="easyadmin::icons.down" height="h-4" width="w-4" class="mx-2 z-20"/>
                    </span>
                </li>
                {{-- <ul :id="elid" x-data="{nof_items: {{count($item['menu_items'])}}, ht: 0}" x-init="ht = 34 * nof_items;"  --}}
                <ul :id="elid" class="overflow-hidden bg-base-200 bg-opacity-60 box-content transition-all" >
                    @foreach ($item['menu_items'] as $mi)
                        @if ($mi['type'] == 'menu_item' && (!isset($mi['show']) || (isset($mi['show']) && $mi['show'])))
                        <li><x-easyadmin::partials.menu-item title="{{$mi['title']}}" route="{{$mi['route']}}" href="{{route($mi['route'], $mi['route_params'])}}" icon="{{$mi['icon']}}"/></li>
                        @endif
                    @endforeach
                </ul>
                </div>
                @endif
            @elseif ($item['type'] == 'menu_item')
                @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
                <li><x-easyadmin::partials.menu-item title="{{$item['title']}}" route="{{$item['route']}}" href="{{route($item['route'], $item['route_params'])}}" icon="{{$item['icon']}}"/></li>
                @endif
            @elseif ($item['type'] == 'menu_section')
            @if (!isset($item['show']) || (isset($item['show']) && $item['show']))
            <li class="flex flex-row items-center justify-start bg-base-200 bg-opacity-50 opacity-80 mt-4 text-warning">
                <x-easyadmin::partials.menu-group title="{{$item['title']}}" icon="{{$item['icon']}}"/>
            </li>
            @endif
            @endif
        @endforeach
    </ul>
</div>

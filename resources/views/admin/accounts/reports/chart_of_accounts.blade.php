<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Chart Of Accounts</span>&nbsp;</h3>
        <div>
            @foreach ($accounts as $a)
                <div class="font-bold text-success">{{$a->name}}</div>
                @foreach ($a->subGroupsFamilyAccounts as $sgf)
                    <x-partials.account_group :sgf="$sgf" />
                    {{-- <div class="pl-5">{{$sgf->name}}</div>
                    @foreach ($sgf->subGroups as $sg)
                        <div class="pl-5">{{$sg->name}}</div>
                    @endforeach
                    @foreach ($sgf->accounts as $sga)
                        <div class="pl-5">{{$sga->name}}</div>
                    @endforeach --}}
                @endforeach

            @endforeach
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

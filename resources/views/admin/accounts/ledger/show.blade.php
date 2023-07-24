<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Ledger Account</span></h3>
        {{-- {{dd($model['item'])}} --}}
        <div>
            <div class="p-2 my-4">Name: <span class="font-bold">{{$model['item']->name}}</span></div>
            <div class="p-2 my-4">Description: <span class="font-bold">{{$model['item']->description}}</span></div>
            <div class="p-2 my-4">Group: <span class="font-bold">{{$model['item']->group->name}}</span></div>
            <div class="p-2 my-4">District: <span class="font-bold">{{$model['item']->district->name}}</span></div>
            <div class="p-2 my-4">Opening Balance: Rs. <span class="font-bold">{{$model['item']->opening_balance}}</span></div>
            <div class="p-2 my-4">Opening Balance Type: <span class="font-bold">{{$model['item']->opening_bal_type}}</span></div>
            <div class="p-2 my-4">Is Bank/Cash Account?: <span class="font-bold">{{$model['item']->cashorbank}}</span></div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

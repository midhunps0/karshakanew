<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Ledger Account</span></h3>
        {{-- {{dd($model['item'])}} --}}
        <div class="flex flex-row justify-between">
            <a class="btn btn-sm btn-success" href="" @click.stop.prevent="$dispatch('linkaction', {link: '{{route('accounts.account.statement')}}?account_id={{$model['item']->id}}'})">
                View Transactions&nbsp;<x-easyadmin::display.icon icon="easyadmin::icons.view_on" height="h-5" width="w-5" />
            </a>
            <div class="flex flex-row space-x-4 items-center">
                <a class="btn btn-sm" href="" @click.stop.prevent="window.history.back();"><x-easyadmin::display.icon icon="easyadmin::icons.go_left" height="h-5" width="w-5" />&nbsp;Go Back
                </a>
                <a class="btn btn-sm btn-warning" href="" @click.stop.prevent="$dispatch('linkaction', {link: '{{route('ledgeraccounts.edit', $model['item']->id)}}'})">Edit&nbsp;<x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-5" width="w-5" />
                </a>
            </div>
        </div>
        <div>
            <div class="p-2 my-4">Name: <span class="font-bold">{{$model['item']->name}}</span></div>
            <div class="p-2 my-4">Description: <span class="font-bold">{{$model['item']->description}}</span></div>
            <div class="p-2 my-4">Group: <span class="font-bold">{{$model['item']->group->name}}</span></div>
            <div class="p-2 my-4">District: <span class="font-bold">{{$model['item']->district->name}}</span></div>
            <div class="p-2 my-4">Opening Balance: Rs. <span class="font-bold">{{$model['item']->opening_balance}}</span></div>
            <div class="p-2 my-4">Opening Balance Type: <span class="font-bold">{{$model['item']->opening_bal_type}}</span></div>
            <div class="p-2 my-4">Is Bank/Cash Account?: <span class="font-bold">{{$model['item']->iscashorbank}}</span></div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

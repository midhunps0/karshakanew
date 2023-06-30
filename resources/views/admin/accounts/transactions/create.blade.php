<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Create Transactions</span>&nbsp;</h3>
        <div class="text-right p-4">
            <a href="" class="btn btn-sm" @click.prevent.stop="history.back();" >Back</a>
        </div>
        <div x-data=""
            class="flex flex-row space-x-4"
            >
            <a href="" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('transaction.create.journal')}}', route: 'transaction.create.journal'})" class="btn btn-sm btn-warning">Journal Entry</a>
            <a href="" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('transaction.create.receipt')}}', route: 'transaction.create.receipt'})" class="btn btn-sm btn-warning">Receipt</a>
            <a href="" @click.prevent.stop="$dispatch('linkaction', {link: '{{route('transaction.create.payment')}}', route: 'transaction.create.payment'})" class="btn btn-sm btn-warning">Payment</a>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

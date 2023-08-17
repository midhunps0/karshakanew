<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden text-error text-center"><span>{{$title ?? 'Unauthorised Action'}}</span>&nbsp;</h3>
        <div class="p-10 m-10 text-error border border-error rounded-md text-lg">{{$message}}</div>
        <div class="text-center p-10">
            <a href="" class="btn btn-sm" @click.prevent.stop="history.back()">Back</a>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

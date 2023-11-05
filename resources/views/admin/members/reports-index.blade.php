<x-easyadmin::partials.adminpanel>
    <div>
        <div x-data class="flex flex-row justify-start space-x-4">
            <a href=""
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('dashboard').'?status=Pending'}}', route: 'dashboard'});"
            class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md cursor-pointer">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold">
                    Gender-wise Report
                </div>
                <div class="text-2xl text-center">
                    {{$transfer_requests ?? 0}}
                </div>
            </a>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

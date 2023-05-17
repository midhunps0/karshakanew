<x-easyadmin::partials.adminpanel>
    <div class="p-4">
        <div x-data class="flex flex-row justify-start space-x-4">
            <div
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('members.unapproved')}}', route: 'members.unapproved'});" class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md cursor-pointer">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold">
                    Unapproved Members
                </div>
                <div class="text-2xl text-center">
                    {{$unapproved_members ?? ''}}
                </div>
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

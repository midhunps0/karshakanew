<x-easyadmin::partials.adminpanel>
    <div x-data="{
        fetchPage(page) {
            $dispatch('linkaction', {link: '{{route('members.unapproved')}}'+'?page='+page, route: 'members.unapproved'});
        }
    }"
        @pageaction.window="fetchPage($event.detail.page);"
        >
        <h3 class="text-xl font-bold pb-3">Unapproved Members</h3>
        <div class="border border-base-200">
            <table class="table table-compact w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Membership No.</th>
                        <th>Taluk</th>
                        <th>Village</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $m)
                    <tr>
                        <td>{{$m->name}}</td>
                        <td>{{$m->membership_no}}</td>
                        <td>{{$m->taluk->name}}</td>
                        <td>{{$m->village->name}}</td>
                        <td>
                            <a href="" type="button" class="btn btn-xs btn-warning">
                                <x-easyadmin::display.icon icon="easyadmin::icons.edit" height="h-4" width="w-4" @click.prevent.stop="$dispatch(
                                    'linkaction', {link: '{{route('members.edit', '_X_')}}'.replace('_X_', {{$m->id}}), route: 'members.edit'}
                                );"/>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{$members->appends(\Request::except('x_mode'))->links()}}
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

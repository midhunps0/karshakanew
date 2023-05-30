<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Roles & Permissions</span>&nbsp;</h3>
        <div x-data="{
                fixedHeader: false,
                updatePermissions(rid, pid, granted) {
                    let g = granted ? 1 : 0;
                    axios.get(

                    ).then(

                    ).catch();
                }
            }"
            >
            <table id="roles-table" class="border border-base-200">
                <thead class="font-bold bg-base-200 rounded-t-md">
                    <tr>
                        <td class="w-72 px-4 py-2">Permissions</td>
                        @foreach ($roles as $r)
                            <td class="text-center py-2">
                                {{$r->name}}
                            </td>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="block h-screen overflow-y-auto w-full">
                    @foreach ($permissions as $p)
                    <tr>
                        <td class="w-72 py-2 px-4">
                            {{$p->name}}
                        </td>
                        @foreach ($roles as $r)
                            <td x-data="{
                            @if(in_array($p->id, $r->permissions()->pluck('id')->toArray()))
                            check: true,
                            @else
                            check: false
                            @endif
                            }
                            "
                            class="text-center">
                                <input x-model="check" type="checkbox" class="checkbox checkbox-xs" :class="!check || 'checkbox-primary'" :checked="check" @change="updatePermissions({{$r->id}}, {{$p->id}}, check);">
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

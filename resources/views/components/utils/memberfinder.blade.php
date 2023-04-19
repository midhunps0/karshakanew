<div x-data="{
    suggestions: [],
    search: '',
    noMemberMsg: false,
    getMembersList() {
        this.noMemberMsg = false;
        console.log('gml');
        let temp = this.search.split('/');
        if (temp.length >= 4 && temp[3].length != 0) {
            axios.get(
                '{{ route('members.suggestionslist') }}', {
                    params: {
                        membership_no: this.search
                    }
                }
            ).then((r) => {
                if (r.data.members.length > 0) {
                    this.suggestions = r.data.members.map((m) => {
                        return {
                            id: m.id,
                            name: m.name,
                            membership_no: m.membership_no,
                            aadhaar_no: m.aadhaar_no,
                            taluk: m.taluk.name
                        }
                    });
                } else {
                    this.noMemberMsg = true;
                }
                console.log(this.suggestions);
            }).catch((e) => {
                console.log(e);
            });
        }
    },
    selectMember(id) {
        $dispatch('selectmember', { id: id });
        this.search = '';
        this.suggestions = [];
    }
}" class="relative w-full">
    {{-- <h3 class="text-sm font-bold pb-3 text-warning">Find Member</h3> --}}
    <div class="form-control w-full flex flex-row flex-wrap space-x-4 justify-start items-start">
        <label class="label w-32">
            <span class="label-text">Registration No.:</span>
        </label>
        <div>
            <input x-model="search" type="text" placeholder="Type here" class="input input-bordered flex-grow max-w-xs" @input.prevent.stop="noMemberMsg = false; getMembersList();" />
            <div x-show="noMemberMsg" x-transition class="text-error text-opacity-80 flex-grow py-2">No members matching the search term.</div>
        </div>
    </div>
    <div x-show="suggestions.length > 0" x-transition
        class="absolute left-0 top-16 z-50 bg-base-200 p-2 text-sm border border-base-content border-opacity-20 rounded-md shadow-md text-base-content !text-opacity-30 max-h-192 overflow-y-scroll"
        tabindex="0">
        <table>
            <tr class="w-full">
                <th class="text-base-content !text-opacity-70">Name</th>
                <th class="text-base-content !text-opacity-70">Registration No.</th>
                <th class="text-base-content !text-opacity-70">Aadhaar No.</th>
                <th class="text-base-content !text-opacity-70">Taluk</th>
            </tr>
            <template x-for="m in suggestions">
                <tr @click.prevent.stop="selectMember(m.id);"
                    @keypress.prevent.stop="console.log($event);  if ($event.code == 'Enter') { selectMember(m.id);}"
                    class="focus:text-warning focus:cursor-pointer hover:text-warning hover:cursor-pointer" tabindex="0">
                    <td class="p-2 text-base-content !text-opacity-70"><span x-text="m.name"></span></td>
                    <td class="p-2 text-base-content !text-opacity-70"><span x-text="m.membership_no"></span></td>
                    <td class="p-2 text-base-content !text-opacity-70"><span x-text="m.aadhaar_no"></span></td>
                    <td class="p-2 text-base-content !text-opacity-70"><span x-text="m.taluk"></span></td>
                </tr>
            </template>
        </table>
    </div>
</div>

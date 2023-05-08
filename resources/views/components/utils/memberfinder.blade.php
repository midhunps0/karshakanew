<div x-data="{
    suggestions: [],
    district: '',
    taluk: '',
    village: '',
    memNo: '',
    noMemberMsg: false,
    searchOn: false,
    searchStr() {
        let s = [
            this.district,
            this.village,
            this.taluk,
            this.memNo
        ].join('/');
        console.log('s: '+s);
        return s;
    },
    disableSearch() {
        return this.district == ''
            || this.taluk == ''
            || this.village == ''
            || this.memNo == '';
    },
    getMembersList() {
        this.noMemberMsg = false;
        this.searchOn = true;
        if (!this.disableSearch()) {
            axios.get(
                '{{ route('members.suggestionslist') }}', {
                    params: {
                        membership_no: this.searchStr()
                    }
                }
            ).then((r) => {
                if (r.data.members.length > 0) {
                    this.suggestions = r.data.members.map((m) => {
                        return {
                            id: m.id,
                            name: m.name,
                            name_mal: m.name_mal,
                            membership_no: m.membership_no,
                            aadhaar_no: m.aadhaar_no,
                            taluk: m.taluk.name
                        }
                    });
                } else {
                    this.noMemberMsg = true;
                }
            }).catch((e) => {
                console.log(e);
            }).finally(() => {
                this.searchOn = false;
            });
        }
    },
    selectMember(id) {
        $dispatch('selectmember', { id: id });
        this.district = '';
        this.taluk = '';
        this.village = '';
        this.memNo = '';
        this.suggestions = [];
    }
}" class="relative w-full">
    {{-- <h3 class="text-sm font-bold pb-3 text-warning">Find Member</h3> --}}
    <div class="form-control w-full flex flex-row flex-wrap space-x-4 justify-start items-start">
        <label class="label w-32">
            <span class="label-text">Registration No.:</span>
        </label>
        <div>
            <div class="flex flex-row space-x-2" :class="!searchOn || 'opacity-50'">
                <input x-model="district" type="text" placeholder="District" class="input input-bordered flex-grow w-20" :disabled="searchOn"/>
                <input x-model="village" type="text" placeholder="Village" class="input input-bordered flex-grow w-20" :disabled="searchOn"/>
                <input x-model="taluk" type="text" placeholder="Taluk" class="input input-bordered flex-grow w-20" :disabled="searchOn"/>
                <input x-model="memNo" type="text" placeholder="Mem. No." class="input input-bordered flex-grow w-20" @keyup.prevent.stop="if($event.code == 'Enter') {noMemberMsg = false; getMembersList();}" :disabled="searchOn"/>
                <button @click.prevent.stop="noMemberMsg = false; getMembersList();" class="btn btn-md btn-warning" :disabled="disableSearch() || searchOn">
                    Search
                </button>
            </div>
            <div x-show="noMemberMsg" x-transition class="text-error text-opacity-80 flex-grow py-2">No members matching the search term.</div>
        </div>
    </div>
    <div x-show="searchOn" x-transition class="absolute left-0 top-16 z-50 text-center animate-pulse text-warning w-full">
Searching .....
    </div>
    <div x-show="suggestions.length > 0" x-transition
        class="absolute left-0 top-16 z-50 bg-base-200 p-2 text-sm border border-base-content border-opacity-20 rounded-md shadow-md text-base-content !text-opacity-30 max-h-192 overflow-y-scroll"
        tabindex="0" @keyup="if ($event.key == 'Escape') {suggestions = [];}">
        <div class="text-right">
            <button type="button" class="btn-xs btn-error py-1 bg-opacity-50 focus:bg-opacity-100" @click.prevent.stop="suggestions = [];">
                <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-4" width="w-4"/>
            </button>
        </div>
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
                    <td class="p-2 text-base-content !text-opacity-70">
                        <span x-text="m.name"></span>
                        <span x-show="m.name != null && m.name.length > 0">/</span>
                        <span x-text="m.name_mal"></span>
                    </td>
                    <td class="p-2 text-base-content !text-opacity-70"><span x-text="m.membership_no"></span></td>
                    <td class="p-2 text-base-content !text-opacity-70"><span x-text="m.aadhaar_no"></span></td>
                    <td class="p-2 text-base-content !text-opacity-70"><span x-text="m.taluk"></span></td>
                </tr>
            </template>
        </table>
    </div>
</div>

<x-easyadmin::partials.adminpanel>
    <div class="p-4">
        <h3 class="py-3 font-bold text-warning">Members' Data:</h3>
        <div x-data="{
                loading: false,
                tableData: [],
                accessLevel: 'state',
                data: {
                    unapproved_members: 0,
                    show_unapproved: 0,
                    pending_applications: 0,
                    transfer_requests: 0,
                    new_registrations: 0,
                    active_members: 0,
                },
                {{-- fetchBoxData() {
                    this.loading = true;
                    axios.get(
                        '{{route('dashboard.box-data')}}',
                    ).then((r)  => {
                        if (r.data.success) {
                            this.data = r.data.data;
                        } else {
                            console.log(r.data.error);
                        }
                        this.loading = false;
                    })
                    .catch((e) => {
                        console.log(e);
                    });
                }, --}}
                fetchTableData() {
                    this.loading = true;
                    axios.get(
                        '{{route('dashboard.table-data')}}',
                    ).then((r)  => {
                        if (r.data.success) {
                            console.log('tableData');
                            console.log(r.data);
                            this.tableData = r.data.data;
                            this.accessLevel = r.data.level;
                        } else {
                            console.log(r.data.error);
                        }
                        this.loading = false;
                    })
                    .catch((e) => {
                        console.log(e);
                    });
                },
                showVillagesData(tid, show = true) {
                    if (show) {
                        if(typeof this.tableData[tid].villages == 'undefined' || this.tableData[tid].villages == null) {
                            this.fetchVillagesData(tid);
                        } else {
                            this.tableData[tid].showVillages = show;
                        }
                    } else {
                        this.tableData[tid].showVillages = false;
                    }
                },
                fetchVillagesData(tid) {
                    this.loading = true;
                    axios.get(
                        '{{route('dashboard.villages-data')}}',
                        {
                            params: {'taluk_id': tid}
                        }
                    ).then((r)  => {
                        if (r.data.success) {
                            console.log('villagesData');
                            console.log(r.data);
                            console.log(`this.tabledata[${tid}]:`);
                            this.tableData[tid].villages = r.data.data;
                            console.log(this.tableData[tid]);
                            this.tableData[tid].showVillages = true;
                        } else {
                            console.log(r.data.error);
                        }
                        this.loading = false;
                    })
                    .catch((e) => {
                        console.log(e);
                    });
                }
            }"
            x-init="
                console.log('init box data...>>')
                {{-- fetchBoxData(); --}}
                fetchTableData();
            "
            class="border border-base-200 rounded-lg overflow-hidden">
            <table class="table table-compact w-full">
                <thead>
                    <tr>
                        @if (auth()->user()->hasPermissionTo('Dashboard: View All District Data'))
                            <th class="bg-secondary text-white">District</th>
                        @else
                            <th class="bg-secondary text-white">Taluk</th>
                        @endif
                        <th class="bg-secondary text-white">Total Approved Members</th>
                        <th class="bg-secondary text-white">Active Members</th>
                        <th class="bg-secondary text-white">Inactive Members</th>
                        <th class="bg-secondary text-white">Members above 60 yrs</th>
                    </tr>
                </thead>

                    <template x-for="item in tableData">
                        <tbody>
                        <tr>
                            <td class="text-left flex justify-between items-center" :class="item.name == 'Total' ? 'bg-base-200 font-bold' : ''">
                                <span x-text="item.name"></span>
                                <template x-if="accessLevel == 'district' && item.name != 'Total'">
                                <button @click.prevent.stop="showVillagesData(item.id, !item.showVillages);" type="button" class="text-xs btn-xs bg-opacity-50 rounded-full" :class="item.showVillages ? 'btn-secondary' : 'btn-warning'">Villages <span x-text="item.showVillages ? '-' : '+'"></span></button>
                                </template>
                            </td>
                            <td class="text-center" :class="item.name == 'Total' ? 'bg-base-200 font-bold' : ''" x-text="item.total_approved_members"></td>
                            <td class="text-center" :class="item.name == 'Total' ? 'bg-base-200 font-bold' : ''" x-text="item.active_members"></td>
                            <td class="text-center" :class="item.name == 'Total' ? 'bg-base-200 font-bold' : ''" x-text="item.inactive_members"></td>
                            <td class="text-center" :class="item.name == 'Total' ? 'bg-base-200 font-bold' : ''" x-text="item.ageover_members"></td>
                        </tr>
                        <template x-if="item.showVillages != undefined && item.showVillages">
                            <tr>
                                <td colspan="5" class="bg-secondary bg-opacity-40">
                                    <div class="font-bold text-center my-2 text-base-content underline">
                                        Villages Members' Data For <span x-text="item.name"></span>.
                                    </div>
                                    <div class="w-full rounded-lg overflow-hidden">
                                        <table class="table table-compact w-full">
                                            <thead>
                                                <tr>
                                                    <th class="rounded-none">Village</th>
                                                    <th class="rounded-none text-center">Total Approved Members</th>
                                                    <th class="rounded-none text-center">Active Members</th>
                                                    <th class="rounded-none text-center">Inactive Mambers</th>
                                                    <th class="rounded-none text-center">Members above 60 yrs</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="v in Object.keys(item.villages)">
                                                <tr>
                                                    <td class="rounded-none" x-text="item.villages[v].name"  :class="item.villages[v].name == 'Total' ? 'bg-base-300 font-bold' : ''"></td>
                                                    <td class="rounded-none text-center" x-text="item.villages[v].total_approved_members"  :class="item.villages[v].name == 'Total' ? 'bg-base-300 font-bold' : ''"></td>
                                                    <td class="rounded-none text-center" x-text="item.villages[v].active_members"  :class="item.villages[v].name == 'Total' ? 'bg-base-300 font-bold' : ''"></td>
                                                    <td class="rounded-none text-center" x-text="item.villages[v].inactive_members"  :class="item.villages[v].name == 'Total' ? 'bg-base-300 font-bold' : ''"></td>
                                                    <td class="rounded-none text-center" x-text="item.villages[v].ageover_members"  :class="item.villages[v].name == 'Total' ? 'bg-base-300 font-bold' : ''"></td>
                                                </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    </template>

            </table>
            {{-- <span
            class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold text-center">
                    Registrations<br><span class="font-normal">(Current Month)</span>
                </div>
                <div class="text-2xl text-center">
                    <span x-text="data.new_registrations"></span>
                </div>
            </span>
            <span class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold text-center">
                    Active Members<br>&nbsp;
                </div>
                <div class="text-2xl text-center">
                    <span x-text="data.active_members"></span>
                </div>
            </span>
            <a x-show="data.show_unapproved" href=""
            @click.prevent.stop="if (data.unapproved_members > 0) { $dispatch('linkaction', {link: '{{route('members.unapproved')}}', route: 'members.unapproved'});}"  class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md "  :class="data.unapproved_members > 0 || 'cursor-default'">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold text-center">
                    Unapproved Members<br>&nbsp;
                </div>
                <div class="text-2xl text-center">
                    <span x-text="data.unapproved_members"></span>
                </div>
            </a>
            <a x-show="data.show_unapproved" href=""
            @click.prevent.stop="if (data.pending_applications > 0) { $dispatch('linkaction', {link: '{{route('allowances.report').'?status=Pending'}}', route: 'allowances.pending'}); }"
            class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md" :class="data.unapproved_members == 0 || 'cursor-pointer'">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold text-center">
                    Pending Allowance Applications
                </div>
                <div class="text-2xl text-center">
                    <span x-text="data.pending_applications"></span>
                </div>
            </a>
            <a href=""
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('members.transfer_requests').'?status=Pending'}}', route: 'members.transfer_requests'});"
            class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md" :class="data.transfer_requests == 0 || 'cursor-pointer'">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold text-center">
                    Transfer Requests<br>&nbsp;
                </div>
                <div class="text-2xl text-center">
                    <span x-text="data.transfer_requests"></span>
                </div>
            </a> --}}
        </div>
        <div class="my-8">
            <h3 class="py-3 font-bold text-warning">Fee Collections Data:</h3>
            <div x-data="{
                    data: [],
                    level: null,
                    feeTypes: null,
                    from: '',
                    to: '',
                    loading: false,
                    formatDate(el, event) {
                        let re = /[0-9,-]/g;
                        let x = (el.value.match(re) || []).join('');
                        let arr = x.split('-');
                        let newarr = [];
                        for(i = 0; i < arr.length; i++) {
                            if (i < 2) {
                                newarr.push(arr[i].padStart(2, '0'));
                            } else {
                                newarr.push(arr[i]);
                            }
                        }
                        el.value = newarr.join('-');
                    },
                    fetchData() {
                        this.loading = true;
                        axios.get(
                            '{{route('dashboard.data')}}',
                            {
                                params: { 'from': this.from, to: this.to }
                            }
                        ).then((r)  => {
                            if (r.data.success) {
                                this.data = r.data.data;
                                this.level = r.data.level;
                                this.feeTypes = r.data.fee_types;
                            } else {
                                console.log(r.data.error);
                            }
                            this.loading = false;
                        })
                        .catch((e) => {
                            console.log(e);
                        });
                    }
                }"
                x-init="
                    from = '{{$from}}';
                    to = '{{$to}}';
                    fetchData();
                "
                >
                <div x-show="loading" class="absolute h-full w-full bg-base-300 bg-opacity-20 flex flex-row justify-center z-20 pt-32">
                    <span class="animate-pulse text-warning">Loading...</span>
                </div>
                <form
                    @submit.prevent.stop="fetchData();"
                    action="">
                    <div class="flex flex-row space-x-4 mb-4 items-end">
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                              <span class="label-text">From</span>
                            </label>
                            <input x-model="from" @change="formatDate($el, $event);" type="text" name="from" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                              <span class="label-text">To</span>
                            </label>
                            <input x-model="to" @change="formatDate($el, $event);" type="text" name="to" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <button type="submit" class="btn btn-md btn-success">Get Data</button>
                        </div>
                    </div>
                </form>
                <div class="rounded-xl border border-base-content border-opacity-10 max-w-full overflow-x-scroll">
                    <table class="table-compact min-w-full">
                        <thead>
                            <tr class="bg-base-200 text-left">
                                <th class="bg-secondary text-white"><span x-text="level == 'state' ? 'Districts' : 'Taluks'"></span></th>
                                <template x-for="ft in feeTypes">
                                    <th class="w-32 break-words text-center bg-secondary text-white"><span x-text="ft"></span></th>
                                </template>
                                <th class="text-center bg-secondary text-white"><span>Total</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="key in Object.keys(data)">
                                <tr :class="key != 'Total' || 'font-bold bg-base-200'">
                                    <td>
                                        <span x-text="key"></span>
                                    </td>
                                    <template x-for="ft in feeTypes">
                                        <td class="text-center">
                                            <span x-text="data[key][ft] || 0"></span>
                                        </td>
                                    </template>
                                    <td class="text-center"><span x-text="data[key]['Total'] || 0"></span></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div class="my-8">
            <h3 class="py-3 font-bold text-warning">Welfare Scheme Applications Count:</h3>
            <div x-data="{
                    data: [],
                    level: null,
                    schemes: null,
                    branches: null,
                    from: '',
                    to: '',
                    loading: false,
                    formatDate(el, event) {
                        let re = /[0-9,-]/g;
                        let x = (el.value.match(re) || []).join('');
                        let arr = x.split('-');
                        let newarr = [];
                        for(i = 0; i < arr.length; i++) {
                            if (i < 2) {
                                newarr.push(arr[i].padStart(2, '0'));
                            } else {
                                newarr.push(arr[i]);
                            }
                        }
                        el.value = newarr.join('-');
                    },
                    fetchData() {
                        this.loading = true;
                        axios.get(
                            '{{route('dashboard.allowances_data')}}',
                            {
                                params: { 'from': this.from, to: this.to }
                            }
                        ).then((r)  => {
                            if (r.data.success) {
                                console.log('data');
                                console.log(r.data.data);
                                this.data = r.data.data;
                                this.level = r.data.level;
                                this.schemes = r.data.schemes;
                                this.branches = r.data.branches;
                            } else {
                                console.log(r.data.error);
                            }
                            this.loading = false;
                        })
                        .catch((e) => {
                            console.log(e);
                        });
                    }
                }"
                x-init="
                    from = '{{$from}}';
                    to = '{{$to}}';
                    fetchData();
                    console.log('data');
                    console.log(data);
                "
                >
                <div x-show="loading" class="absolute h-full w-full bg-base-300 bg-opacity-20 flex flex-row justify-center z-20 pt-32">
                    <span class="animate-pulse text-warning">Loading...</span>
                </div>
                <form
                    @submit.prevent.stop="fetchData();"
                    action="">
                    <div class="flex flex-row space-x-4 mb-4 items-end">
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                              <span class="label-text">From</span>
                            </label>
                            <input x-model="from" @change="formatDate($el, $event);" type="text" name="from" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <label class="label">
                              <span class="label-text">To</span>
                            </label>
                            <input x-model="to" @change="formatDate($el, $event);" type="text" name="to" class="input input-bordered w-full max-w-xs" placeholder="dd-mm-yyyy" pattern="[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9]" required/>
                        </div>
                        <div class="form-control w-full max-w-xs">
                            <button type="submit" class="btn btn-md btn-success">Get Data</button>
                        </div>
                    </div>
                </form>
                <div class="rounded-xl border border-base-content border-opacity-10 max-w-full overflow-x-scroll">
                    <table class="table-compact w-full">
                        <thead>
                            <tr class="bg-base-200">
                                <th class="bg-secondary text-white">
                                    <span>Schemes</span>
                                </th>
                                <template x-for="b in branches">
                                    <th class="w-32 break-words bg-secondary text-white"><span x-text="b"></span></th>
                                </template>
                                <th class="bg-secondary text-white"><span>Total</span></th>
                                <th class="bg-secondary text-white"><span>APR.</span></th>
                                <th class="bg-secondary text-white"><span>REJ.</span></th>
                                <th class="bg-secondary text-white"><span>PEN.</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(key, i) in Object.keys(data)">
                                <tr :class="key != 'Total' || 'font-bold bg-base-200'">
                                    <td :class="{'text-warning' : ['Total', 'Pending'].includes(key), 'text-success' : key == 'Approved', 'text-error' : key == 'Rejected'}">
                                        <span x-text="key"></span>
                                    </td>
                                    <template x-for="b in branches">
                                        <td class="text-center" :class="{'text-warning' : ['Total', 'Pending'].includes(key), 'text-success' : key == 'Approved', 'text-error' : key == 'Rejected'}">
                                            <span x-text="data[key][b] || 0"></span>
                                        </td>
                                    </template>
                                    <td class="text-center text-warning font-bold bg-base-200"><span x-text="data[key]['Total'] || 0"></span></td>
                                    <td class="text-center text-success"><span x-text="data[key]['Approved'] || 0"></span></td>
                                    <td class="text-center text-error"><span x-text="data[key]['Rejected'] || 0"></span></td>
                                    <td class="text-center text-warning"><span x-text="data[key]['Pending'] || 0"></span></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>




    </div>
</x-easyadmin::partials.adminpanel>

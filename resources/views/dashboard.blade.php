<x-easyadmin::partials.adminpanel>
    <div class="p-4">
        <div x-data class="flex flex-row justify-start space-x-4">
            @if($show_unapproved)
            <a href=""
            @click.prevent.stop="@if ($unapproved_members > 0) $dispatch('linkaction', {link: '{{route('members.unapproved')}}', route: 'members.unapproved'}); @endif"  class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md @if (!$unapproved_members > 0) cursor-default @endif">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold">
                    Unapproved Members
                </div>
                <div class="text-2xl text-center">
                    {{$unapproved_members ?? ''}}
                </div>
            </a>
            @endif
            @if($show_unapproved)
            <a href=""
            @click.prevent.stop="@if ($pending_applications > 0) $dispatch('linkaction', {link: '{{route('allowances.report').'?status=Pending'}}', route: 'allowances.pending'}); @endif"
            class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md @if (!$unapproved_members > 0) cursor-default @endif">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold">
                    Panding Applications
                </div>
                <div class="text-2xl text-center">
                    {{$pending_applications ?? 0}}
                </div>
            </a>
            @endif
            @if(isset($transfer_requests))
            <a href=""
            @click.prevent.stop="$dispatch('linkaction', {link: '{{route('members.transfer_requests').'?status=Pending'}}', route: 'members.transfer_requests'});"
            class="w-48 min-h-32 flex flex-col space-y-4 items-center bg-base-200 border border-base-300 border-opacity-80 rounded-md p-4 shadow-md cursor-pointer">
                <x-easyadmin::display.icon icon="easyadmin::icons.info" height="h-10" width="h-10" class="text-warning"/>
                <div class="font-bold">
                    Transfer Requests
                </div>
                <div class="text-2xl text-center">
                    {{$transfer_requests ?? 0}}
                </div>
            </a>
            @endif
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
                    <table class="table table-compact min-w-full">
                        <thead>
                            <th><span x-text="level == 'state' ? 'Districts' : 'Taluks'"></span></th>
                            <template x-for="ft in feeTypes">
                                <th><span x-text="ft"></span></th>
                            </template>
                        </thead>
                        <tbody>
                            <template x-for="key in Object.keys(data)">
                                <tr :class="key != 'Total' || 'font-bold'">
                                    <td>
                                        <span x-text="key"></span>
                                    </td>
                                    <template x-for="ft in feeTypes">
                                        <td>
                                            <span x-text="data[key][ft] || 0"></span>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

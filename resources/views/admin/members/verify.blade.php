<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3"><span>Create Member: Verify Aadhaar No.</span>&nbsp;</h3>
        <form x-data="{
                aadhaarNo: '',
                duplicate: false,
                message: '',
                url: '',
                doSubmit() {
                    this.duplicate = false;
                    this.message = '';
                    axios.post(
                        '{{route('members.verify_aadhaar', '_X_')}}'.replace('_X_', this.aadhaarNo)
                    ).then((r) => {
                        if (r.data.status == 'NOT AVAILED') {
                            let l = this.url+'?an='+this.aadhaarNo;
                            if (r.data.message = 'Old data') {
                                l += '&ol=1';
                            }
                            $dispatch('linkaction', {
                                link: l,
                                route: 'members.create'
                            });
                        } else {
                            this.message = r.data.message;
                            this.duplicate = true;
                        }
                    }).catch((e) => {
                        console.log(e);
                    })
                }
            }
            "
            x-init="
            url='{{route('members.create')}}';
            $watch('aadhaarNo', (v) => {
                aadhaarNo = v.replace(' ', '');
            });
            "
            class="border border-base-content border-opacity-20 rounded-md py-12 shadow-md md:w-10/12 m-auto"
            @submit.prevent.stop="doSubmit()"
            >
            <div class="flex flex-row flex-wrap sm:flex-nowrap space-x-0 sm:space-x-4 sm:w-11/12 m-auto justify-center"
                >
                <div class="form-control w-full sm:w-1/3 sm:max-w-xs">
                    <label class="label">
                      <span class="label-text">Aadhaar Number</span>
                    </label>
                    <input x-model="aadhaarNo" type="text" placeholder="Type here" class="input input-md input-bordered" required minlength="12"/>
                </div>
            </div>
            <div class="mt-8 py-4 text-center">
                <button type="submit" class="btn btn-md btn-primary px-8">Verify & Proceed</button>
            </div>
            <div x-show="duplicate" class="w-full text-center">
                <span class="label-text text-error" x-text="'Can\'t register this aadhaar number. '+message+'.'"></span>
            </div>
        </form>
    </div>
</x-easyadmin::partials.adminpanel>

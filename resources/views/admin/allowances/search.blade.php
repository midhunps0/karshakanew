<x-easyadmin::partials.adminpanel>
    <div x-data="{
            application_no: '',

            doSubmit() {
                let params = {
                    application_no: this.application_no
                };
                axios.post(
                    '{{route('allowances.search')}}',
                    params
                ).then((r) => {
                    console.log(r);
                    if(r.data.success){
                        $dispatch('linkaction', {link: r.data.link, route: r.data.route, fresh: true});
                    } else {
                        $dispatch('shownotice', {message: 'No application found with the given number.', mode: 'warning'});
                    }
                }).catch((e) => {
                    $dispatch('shownotice', {message: 'Unexpected Error. Unable to find the application.', mode: 'error'});
                });
            }
        }">
        <h3 class="text-xl font-bold pb-3 print:hidden"><span>Search Allowances</span>&nbsp;</h3>
        <form action="" class="max-w-xs p-4 border border-base-content border-opacity-20 rounded-md">
            <div class="form-control w-full max-w-xs">
                <label class="label">
                  <span class="label-text">Application No.</span>
                </label>
                <input x-model="application_no" type="text" placeholder="Appln. No." class="input input-bordered w-full max-w-xs" required />
            </div>
            <div class="text-center my-4 py-4">
                <button @click.prevent.stop="doSubmit();" type="submit" class="btn btn-success btn-sm">Find Application</button>
            </div>
        </form>
    </div>
</x-easyadmin::partials.adminpanel>

<x-easyadmin::partials.adminpanel>
    <div x-data="{
            file: null,
            loading: false,
            message: '',
            failed: [],
            doSubmit() {
                {{-- let params = {
                    _method: 'P',
                } --}}
                this.loading = true;
                let form = document.getElementById('payment-import-form');
                let formData = new FormData(form);
                axios.post(
                    '{{route('allowances.process_payment')}}',
                    formData,
                ).then((r) => {
                    console.log(r);
                    this.message = r.data.message;
                    this.failed = r.data.failed;
                    this.loading = false;
                    document.getElementById('import-file').value = '';
                    this.file = null;
                })
                .catch((e) => {
                    console.log(e);
                });
            },
            doReset() {
                this.message = '';
                this.failed = [];
                this.file = null;
                document.getElementById('import-file').value = '';
            }
        }">
        <h3 class="text-xl font-bold pb-3 print:hidden">
            <span>Allowances Bulk Payment</span>
        </h3>
        <form id="payment-import-form" action="" class="flex flex-col gap-6 w-1/2 m-3 p-3 border border-base-300 rounded-md" @submit.prevent.stop="doSubmit();">
            <div >
                <input X-on:change="file = Object.values($event.target.files)[0];" type="file" name="file" id="import-file">
            </div>
            <div class="flex justify-evenly">
                <button @click.prevent.stop="doReset();" class="btn btn-warning btn-sm bg-opacity-50" type="button" :disabled="file == null && message == ''">Reset</button>
                <button class="btn btn-success btn-sm" type="submit" :disabled="file == null">Import Payments</button>
            </div>
            <div x-show="message.length > 0" x-text="message" class="bg-warning p-4 font-bold bg-opacity-50 rounded-md"></div>
        </form>
        <div x-show="failed.length > 0" class="mt8">
            <h4 class="font-bold p-3 text-error" x-text="failed.length + ' Failed Items'"></h4>
            <div class="max-h-72 overflow-y-scroll w-1/2 my-3 border border-base-200 rouded-md">
                <table class="table table-compact table-zebra w-full">
                    <thead>
                        <tr>
                            <td>Allowance Number</td>
                            <td>Reason for failure</td>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="f in failed">
                            <tr>
                                <td x-text="f.code"></td>
                                <td x-text="f.reason"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

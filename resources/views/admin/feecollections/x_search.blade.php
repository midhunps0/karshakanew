<x-easyadmin::partials.adminpanel>
    <div>
        <h3 class="text-xl font-bold pb-3"><span>Search Receipts</span>&nbsp;</h3>
        <form
            x-data="{
                dateType: 'receipt_date',
                from: null,
                to: null,
                receipt_no: '',
                doSubmit() {
                    
                }
            }"
            action="" class="w-full md:w-3/4 m-auto p-4 border border-base-content border-opacity-20 rounded-md"
            @submit.prevent.stop="doSubmit();">
            <div class="flex flex-row space-x-4 w-full my-4">
                <select class="select select-bordered flex-grow" x-model="dateType" name="" id="">
                    <option value="created_at">Creation Date</option>
                    <option value="receipt_date">Receipt Date</option>
                </select>
                <input x-model="from" type="text" x-model="from" class="input input-md input-bordered flex-grow" placeholder="From (dd-mm-yyyy)" pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]\d\d\d">
                <input x-model="to" type="text" x-model="to" class="input input-md input-bordered flex-grow" placeholder="To (dd-mm-yyyy)" pattern="[0-3][0-9]-[0-1][0-9]-[0-2][0-9][0-9][0-9]\d\d\d">
            </div>
            <div class="flex flex-rom justify-between w-full my-4">
                <div class="flex flex-row items-baseline space-x-4">
                    <label>Receipt Date: </label>
                    <input x-model="receipt_no" type="text" placeholder="Receipt No." class="input input-md input-bordered">
                </div>
                <button type="submit" class="btn btn-success min-w-48">Submit</button>
            </div>
        </form>
    </div>
</x-easyadmin::partials.adminpanel>

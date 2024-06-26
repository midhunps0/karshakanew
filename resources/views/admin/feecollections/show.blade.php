<x-easyadmin::partials.adminpanel>
    <h3 class="text-xl font-bold pb-3"><span>Receipt</span>&nbsp;</h3>

    <div class="w-full"
        x-data="{
                receipt: null,
                printReceipt() {
                    let divContents = document.getElementById('receipt').innerHTML;
                    let a = window.open('', '', 'width=210');
                    a.document.write('<html>');
                    let head = document.getElementsByTagName('head')[0].innerHTML;
                    a.document.write('<head>');
                    a.document.write(head);
                    a.document.write('</head>');
                    a.document.write('<body>');
                    a.document.write(divContents);
                    a.document.write('</body></html>');
                    a.document.close();

                    setTimeout(() => {a.print(); a.close();}, 500);
                },
            }"
        x-init="
            theData = {{Js::from($model)}};
            receipt = theData.item;
        "
        >
        <div class="w-full md:w-10/12 m-auto p-3 border border-base-content border-opacity-50 rounded-md my-8 min-w-48 overflow-x-scroll">
            <div id="receipt">
                <div class="text-center my-4 font-bold underline">കേരള കർഷക തൊഴിലാളി ക്ഷേമനിധി ബോർഡ്<br/>
                    രസീത്
                    </div>
                <div class="flex flex-row flex-wrap justify-between items-center w-full p-2">
                    <div>
                        <div>
                            <span class="text-warning">Member: </span>
                            <span x-text="receipt.member ? receipt.member.display_name : ''"></span>
                        </div>
                        <div>
                            <span class="text-warning">Membership No.: </span>
                            <span x-text="receipt.member ? receipt.member.membership_no : ''"></span>
                        </div>
                    </div>
                    <div>
                        <div>
                            <span class="text-warning">Receipt No.: </span>
                            <span x-text="receipt.receipt_number"></span>
                        </div>
                        <div>
                            <span class="text-warning">Date: </span>
                            <span x-text="receipt.formatted_receipt_date"></span>
                        </div>
                    </div>
                </div>
                <div>
                    <table class="w-full table table-compact">
                        <tbody>
                            <tr class="border-b border-base-content border-opacity-50">
                                <td class="bg-base-200">
                                    <span>
                                        Particulars
                                    </span>
                                    <span class="hidden print:inline">
                                        From<br/>To
                                    </span>
                                </td>
                                <td class="bg-base-200 print:hidden">From</td>
                                <td class="bg-base-200 print:hidden">To</td>
                                <td class="bg-base-200 text-right">Amount</td>
                            </tr>
                            <template x-for="item in receipt.fee_items">
                                <tr>
                                    <td>
                                        <span x-text="item.fee_type.name"></span><br/>
                                        <span class="hidden print:inline" x-text="item.formatted_period_from || ''"></span><br/>
                                        <span class="hidden print:inline" x-text="item.formatted_period_to || ''"></span>
                                    </td>
                                    <td class="print:hidden" x-text="item.formatted_period_from || '--'"></td>
                                    <td class="print:hidden" x-text="item.formatted_period_to || '--'"></td>
                                    <td class="text-right" x-text="item.amount"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tbody>
                            <tr class="border-t border-base-content border-opacity-50">
                                <td colspan="3" class="font-bold print:hidden">Total: </td>
                                <td class="hidden font-bold print:table-cell">Total: </td>
                                <td colspan="1" class="text-right font-bold" x-text="receipt.total_amount"></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="hidden print:table-cell"><span class="font-bold">Notes:&nbsp;</span><br/><span x-text="receipt.notes || '--'"></span></td>
                                <td colspan="4" class="bg-base-200 print:hidden">
                                    <span class="font-bold">Notes:</span><br/>
                                    <span x-text="receipt.notes || '--'"></span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="2" class="hidden print:table-cell text-right">sd/-<br/>DEO</td>
                                <td colspan="4" class="print:hidden text-right">
                                    sd/-<br/>DEO
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex flex-row space-x-4 justify-center items-center p-4 mt-4 print:hidden">
                <button @click.prevent.stop="printReceipt()" class="btn btn-sm btn-warning">Print</button>
                {{-- @if (Gate::allows('update', $member)) --}}
                <a href="" @click.prevent.stop="$dispatch('linkaction', {
                    link: '{{route('feecollections.edit', $model['item']['id'])}}', route: 'feecollections.edit'
                });" class="btn btn-sm btn-accent">Edit</a>
                {{-- @endif --}}
                <a href="" @click.prevent.stop="history.back();" class="btn btn-sm">Back</a>
            </div>
        </div>
    </div>
</x-easyadmin::partials.adminpanel>

@props(['advSearchFields'])

<div x-data="{
        advFields: {
            none: {key: 'none', text: 'Select A Field', type: 'none', inputType: 'disabled'},
        },
        myconditions: [{
            field: 'none',
            {{-- type: '', --}}
            operation: 'none',
            value: ''
        }],
        fieldOperators: {
            none: [{ key: 'none', text: 'Choose A Condition' }],
            numeric: [
                { key: 'gt', text: 'Greater Than' },
                { key: 'lt', text: 'Less Than' },
                { key: 'gte', text: 'Greater Than Or Equal To' },
                { key: 'lte', text: 'Less Than Or Equal To' },
                { key: 'eq', text: 'Equal To' },
                { key: 'neq', text: 'Not Equal To' },
            ],
            string: [
                { key: 'is', text: 'Is exactly' },
                { key: 'ct', text: 'Contains' },
                { key: 'st', text: 'Starts With' },
                { key: 'en', text: 'Ends With' },
            ],
            list_numeric: [
                { key: 'eq', text: 'Is' },
            ],
            list_string: [
                { key: 'is', text: 'Is' },
            ]
        },
        addContition() {
            this.myconditions.push({
                field: 'none',
                {{-- type: '', --}}
                operation: 'none',
                value: ''
            });
        },
        resetAdvSearch() {
            this.myconditions = [{
                field: 'none',
                operation: 'none',
                value: ''
            }];

        },
        sanitisedConditions (cx) {
            console.log('cx');
            console.log(cx);
            return cx.filter((item) => {
                return item.field != 'none' && item.operation != 'none' && item.value != '';
            });
        },
        dispatchSearch() {
            $dispatch('advsearch', {conditions: JSON.parse(JSON.stringify(this.sanitisedConditions(this.myconditions))), str: this.conditionString()});
        },
        conditionString() {
            if (this.myconditions.length == 0) {
                return '';
            }
            let str = this.myconditions.reduce(
                (result, item) => {
                    if (
                        item.field != 'none' &&
                        item.operation != 'none' &&
                        item.value != ''
                    ) {
                        x = this.advFields[item.field];
                        result += x.text[0].toUpperCase() + x.text.substring(1);
                        result += ' ' + ((this.fieldOperators[(this.advFields[item.field]).type]).filter(
                            (x) => {
                                return x.key == item.operation;
                            }
                        ))[0].text;
                        if (typeof this.advFields[item.field].options != 'undefined') {
                            result += ' \'' + (this.advFields[item.field].options).filter((x) => {
                                    return x.key == item.value;
                                })[0].text + '\'  '
                        } else {
                            result += ' \'' + item.value + '\'  ';
                        }
                    }
                    return result;
                }, ''
            );
            return str.slice(0, -2);
        }
    }"
    x-init="
        @foreach ($advSearchFields as $field => $data)
            advFields.{{$field}} = {
                key: '{{$data['key']}}',
                text: '{{$data['display_text']}}',
                type: '{{$data['input_val_type']}}',
                @if (isset($data['options']))
                options: [
                    @if ($data['options_type'] == 'value_only')
                    @foreach ($data['options'] as $opt)
                        {key: '{{$opt}}', text: '{{$opt}}'},
                    @endforeach
                    @else
                    @foreach ($data['options'] as $key => $val)
                        {key: '{{$key}}', text: '{{$val}}'},
                    @endforeach
                    @endif
                ],
                @endif
                inputType: '{{$data['input_elm_type']}}',
            };
        @endforeach
        @if (request('adv_search', null) != null)
        ads = {{Js::from(request('adv_search'))}};
        if (ads.length > 0) {
            myconditions = [];
            ads.forEach((x) => {
                let y = x.split('::');
                myconditions.push({
                    field: y[0],
                    operation: y[1],
                    value: y[2],
                });
            });
        }
        console.log('loaded adv_search from request');
        console.log(myconditions);
        console.log(advFields);
        @endif
    "
    x-show="showAdvSearch" x-transition
    @clearadvsearch.window="resetAdvSearch();dispatchSearch();"
    class="absolute top-0 left-0 z-30 w-full flex flex-row justify-center p-16 items-start bg-base-100 bg-opacity-60 min-h-full">
    <div class="flex flex-col items-center px-4 py-6 rounded-md w-2/3 mx-auto bg-base-200 shadow-lg relative">
        <button @click.prevent.stop="showAdvSearch = false;"
            class="w-8 h-8 p-1 bg-base-100 hover:bg-base-300 hover:text-warning transition-colors text-base-content rounded-md flex flex-row items-center justify-center absolute top-2 right-2">
            <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-7" width="w-7" />
        </button>
        <div class="w-full flex flex-row justify-center">
            <h3 class="text-lg font-bold mb-4">Advanced Search</h3>
        </div>
        <div class="w-full flex flex-row justify-center mb-2">
            <div class="flex-1 px-2 py-1 mx-1 font-bold text-center border-b border-opacity-60 border-base-content">
                Field</div>
            <div class="flex-1 px-2 py-1 mx-1 font-bold text-center border-b border-opacity-60 border-base-content">
                Condition</div>
            <div class="w-24 px-2 py-1 mx-1 font-bold text-center border-b border-opacity-60 border-base-content">
                Value</div>
            <div class="w-10 px-2 flex flex-row space-x-2">
            </div>
        </div>
        <template x-for="(condition, index) in myconditions" :key="'con' + index">
            <div class="w-full flex flex-row justify-center my-2">
                <div class="w-full flex-1 mx-1">
                    <select x-model="condition.field" :id="'advf' + index"
                        class="select select-sm select-bordered py-0 w-full"
                        @change.prevent.stop="document.getElementById('advop'+index).dispatchEvent(new Event('change', { 'bubbles': false })); $nextTick(() => {
                            if (typeof advFields[condition.field].options != 'undefined' && advFields[condition.field].options != null) {
                                condition.value=advFields[condition.field].options[0].key;
                            }
                        });">
                        {{-- <option value="none">Select Field</option> --}}
                        <template x-for="field in Object.values(advFields)">
                            <option :value="field.key" :selected="condition.field == field.key"></span><span x-text="field.text"></span>
                            </option>
                        </template>
                    </select>
                </div>
                <div class="flex-1 mx-1">
                    <select x-model="condition.operation" :id="'advop' + index"
                        class="select select-sm select-bordered py-0 w-full" >
                        {{-- <option value="none">Choose Condition</option> --}}
                        <span x-text="advFields[condition.field]"></span>
                        <template x-for="op in fieldOperators[(advFields[condition.field]).type]"
                            :key="op.key">
                            <option :value="op.key" :disabled="(advFields[condition.field]).inputType == 'select' && op.key != 'is'"><span x-text="op.text"></span></option>
                        </template>
                    </select>
                </div>
                <template x-if="(advFields[condition.field]).inputType == 'disabled'">
                <div class="w-24 mx-1">
                    <input type="text" disabled class="input input-sm input-bordered w-full !bg-base-100">
                </div>
                </template>
                <template x-if="(advFields[condition.field]).inputType == 'text'">
                <div class="w-24 mx-1">
                    <input type="text" x-model="condition.value" class="input input-sm input-bordered w-full">
                </div>
                </template>
                <template x-if="(advFields[condition.field]).inputType == 'select'">
                <div class="w-24 mx-1">
                    <select x-model="condition.value" class="select select-sm select-bordered py-0 w-full">
                        <template x-for="op in (advFields[condition.field]).options"
                            :key="op.key">
                            <option :value="op.key"><span x-text="op.text"></span></option>
                        </template>
                    </select>
                </div>
                </template>
                <div class="w-10 px-2 flex flex-row items-center">
                    <button @click.prevent.stop="myconditions.splice(index, 1);"
                        class="w-6 h-6 p-1 bg-error text-base-content rounded-md flex flex-row items-center justify-center disabled:bg-opacity-70"
                        :disabled="myconditions.length == 1">
                        <x-easyadmin::display.icon icon="easyadmin::icons.close" height="h-5" width="w-5" />
                    </button>
                </div>
            </div>
        </template>
        <div class="w-full flex flex-row justify-center mt-6 mb-2">
            <div class="flex-1 flex-grow px-1">
                <button @click.prevent.stop="addContition"
                    class="btn btn-sm btn-warning p-0 w-full border border-base-100 flex felx-row items-center justify-center">
                    <x-easyadmin::display.icon icon="easyadmin::icons.plus" height="h-4" width="w-4" />&nbsp;Add
                    Condition
                </button>
            </div>
            <div class="flex-1 flex-grow px-1">
                <button
                    {{-- @click.prevent.stop="showAdvSearch = false;console.log(myconditions);" --}}
                    @click.prevent.stop="showAdvSearch = false; dispatchSearch();"
                    class="btn btn-sm btn-success p-0 w-full border border-base-100 flex felx-row items-center justify-center">
                    <x-easyadmin::display.icon icon="easyadmin::icons.go_right" height="h-4" width="w-4" />&nbsp;Get Items List
                </button>
            </div>
            <div class="w-10 px-2 flex flex-row items-center">
                <button
                    @click.prevent.stop="resetAdvSearch(); dispatchSearch();"
                    class="w-6 h-6 p-1 bg-error text-base-content rounded-md flex flex-row items-center justify-center">
                    <x-easyadmin::display.icon icon="easyadmin::icons.delete" height="h-5" width="w-5" />
                </button>
            </div>
        </div>
    </div>
</div>

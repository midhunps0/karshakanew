@props([
    'element',
    '_old' => [],
    '_current_values' => [],
    'xerrors' => [],
    'label_position' => 'top',
    'selected_date' => null
    ])
@php
// dd($_old->nominees);
    // $type = $element['input_type'];
    $name = $element['key'];
    $authorised = $element['authorised'];
    $label = $element['label'];
    $width = $element['width'] ?? 'full';
    $placeholder = $element['placeholder'] ?? null;
    $wrapper_styles = $element['wrapper_styles'] ?? null;
    $input_styles = $element['input_styles'] ?? null;
    $properties = $element['properties'] ?? [];
    $fire_input_event = $element['fire_input_event'] ?? false;
    $update_on_events = $element['update_on_events'] ?? null;
    $reset_on_events = $element['reset_on_events'] ?? null;
    $toggle_on_events = $element['toggle_on_events'] ?? null;
    $show = $element['show'] ?? true;
    $wclass = 'w-64';
    switch ($width) {
        case 'full':
            $wclass = 'w-full';
            break;
        case '1/2':
            $wclass = 'w-1/2';
            break;
        case '1/3':
            $wclass = 'w-1/3';
            break;
        case '2/3':
            $wclass = 'w-2/3';
            break;
        case '1/4':
            $wclass = 'w-1/4';
            break;
        case '3/4':
            $wclass = 'w-3/4';
            break;
    }
    $ulid = Illuminate\Support\Str::ulid();
@endphp
@if ($authorised)
    <div x-data="{
            items: [],
            dummyItem: null,
            addDummyItem() {
                this.items.push(JSON.parse(JSON.stringify(this.dummyItem)));
            },
            removeItem(i) {
                {{-- if (this.items.length > 1) { --}}
                    this.items = this.items.filter((n, index) => {
                        return i != index;
                    });
                {{-- } --}}
            }
        }"
        x-init="
            dummyItem = {
                    name: '',
                    relation: '',
                    percentage: '',
                    dob: '',
                    guardian_name: '',
                    guardian_relation: ''
                };
            {{-- addDummyItem(); --}}
            @if(isset($_old['nominees']) && count($_old['nominees']) > 0)
            items = [];
            @foreach ($_old['nominees'] as $n)
                items.push({
                    name: '{{$n->name}}',
                    relation: '{{$n->relation}}',
                    percentage: '{{$n->percentage}}',
                    dob: '{{$n->dob}}',
                    guardian_name: '{{$n->guardian_name}}',
                    guardian_relation: '{{$n->guardian_relation}}'
                });
            @endforeach
            @endif
            console.log('items');
            console.log(items);
        ">
        <div x-show="items.length == 0">
            <button class="btn btn-sm btn-warning" @click.prevent.stop="addDummyItem();">
                Add <x-easyadmin::display.icon icon="easyadmin::icons.plus"/>
            </button>
        </div>
        <table x-show="items.length > 0">
            <tr>
                <td>Name</td>
                <td>Relation</td>
                <td>Percentage</td>
                <td>Date of birth</td>
                <td>Guardian Name</td>
                <td>Guardian Relation</td>
                <td></td>
            </tr>
            <template x-for="(item, index) in items">
                <tr>
                    <td>
                        <input :name="'nominees['+index+'][name]'" class="input input-sm border border-base-content border-opacity-20 max-w-28" type="text" x-model="item.name" required>
                    </td>
                    <td>
                        <input :name="'nominees['+index+'][relation]'" class="input input-sm border border-base-content border-opacity-20 max-w-28" type="text" x-model="item.relation" required>
                    </td>
                    <td>
                        <input :name="'nominees['+index+'][percentage]'" class="input input-sm border border-base-content border-opacity-20 max-w-24" type="text" x-model="item.percentage" required>
                    </td>
                    <td>
                        <input :name="'nominees['+index+'][dob]'" class="input input-sm border border-base-content border-opacity-20 max-w-28" type="text" x-model="item.dob" required>
                    </td>
                    <td>
                        <input :name="'nominees['+index+'][guardian_name]'" class="input input-sm border border-base-content border-opacity-20 max-w-28" type="text" x-model="item.guardian_name">
                    </td>
                    <td>
                        <input :name="'nominees['+index+'][guardian_relation]'" class="input input-sm border border-base-content border-opacity-20 max-w-28" type="text" x-model="item.guardian_relation">
                    </td>
                    <td>
                        <button x-show="index == items.length -1" @click.prevent.stop="addDummyItem()" class="btn btn-sm btn-warning">
                            <x-easyadmin::display.icon icon="easyadmin::icons.plus"/>
                        </button>
                        <button @click.prevent.stop="removeItem(index)" class="btn btn-sm btn-error">
                            <x-easyadmin::display.icon icon="easyadmin::icons.delete"/>
                        </button>
                    </td>
                </tr>
            </template>
        </table>
    </div>
@endif

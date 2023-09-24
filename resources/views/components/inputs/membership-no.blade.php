@props([
    'element',
    '_old' => [],
    '_current_values' => [],
    'xerrors' => [],
    'label_position' => 'top',
    'selected_date' => null
    ])
@php
    // $type = $element['input_type'];
    $name = $element['key'];
    // $startYear = $element['start_year'];
    // $endYear = $element['end_year'];
    // $dateFormat = $element['date_format'];
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
@if ($authorised && $show)
<div x-data="{
        memNumDistrict: '',
        memNumTaluk: '',
        memNumVillage: '',
        memNumStr: '',
        memno: '',
        getMemno() {
            return this.memNumDistrict +
                '/' + this.memNumTaluk +
                '/' + this.memNumVillage +
                '/' + this.memNumStr;
        }
    }"
    x-init="
        $watch('memNumDistrict', () => {
            memno = getMemno();
        });
        $watch('memNumTaluk', () => {
            memno = getMemno();
        });
        $watch('memNumVillage', () => {
            memno = getMemno();
        });
        $watch('memNumStr', () => {
            memno = getMemno();
        });
    "
    class="form-control w-full my-3">
    <label class="label">
        <span class="label-text">Membership No.</span>
    </label>
    <input name="{{$name}}" type="hidden" :value="memno">
    <div class="w-full flex flex-row space-x-2">
        <input x-model="memNumDistrict" type="text" class="input input-md input-bordered w-16" placeholder="District" required>
        <input x-model="memNumTaluk" type="text" class="input input-md input-bordered w-16" placeholder="Taluk" required>
        <input x-model="memNumVillage" type="text" class="input input-md input-bordered w-16" placeholder="Village" required>
        <input x-model="memNumStr" type="text" class="input input-md input-bordered w-16" placeholder="Sl. No." required>
    </div>
</div>
@endif

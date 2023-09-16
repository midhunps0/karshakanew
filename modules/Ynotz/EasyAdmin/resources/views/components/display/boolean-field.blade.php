@props(['row_data', 'col'])
<td>
    <span>
        {{ $row_data[$col['fields'][0]] ? 'Yes' : 'No' }}
    </span>
</td>

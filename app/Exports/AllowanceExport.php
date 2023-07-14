<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllowanceExport implements FromArray, WithHeadings
{
    private $data;
    private $columns;

    public function __construct(array $data)
    {
        $this->data = $data['results'];
        $this->columns = $data['columns'];
    }

    public function array(): array
    {
        $arr = [];
        foreach ($this->data as $item) {
            $temp = [];
            foreach ($item as $key => $val) {
                if (in_array($key, array_keys($this->columns))) {
                    $temp[$key] = $val;
                }
            }
            $arr[] = $temp;
        }
        return $arr;
    }

    public function headings(): array
    {
        // dd($this->columns);
        return array_values($this->columns);
    }
}

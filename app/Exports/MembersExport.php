<?php
namespace App\Exports;

use App\Models\FeeType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembersExport implements FromArray, WithHeadings
{
    public function __construct(public $data, public $columns, public $headings = null, public $boolCols = [])
    {

    }

    public function array(): array
    {
        $arr = [];
        foreach ($this->data as $m) {
            $item = [];
            foreach ($this->columns as $c) {
                $item[] = $this->getValue($m, $c);
            }
            $arr[] = $item;
        }
        return $arr;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function getValue($m, $col)
    {
        $carr = explode('.', $col);
        $v = $m;
        foreach ($carr as $c) {
            $v = $v->$c;
        }
        if (!in_array($col, array_keys($this->boolCols))) {
            return $v;
        } else {
            return $v ? $this->boolCols[$col][0] : $this->boolCols[$col][1];
        }
    }
}

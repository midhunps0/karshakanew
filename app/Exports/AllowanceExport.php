<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllowanceExport implements FromArray, WithHeadings
{
    public function __construct(public $data)
    {}

    public function array(): array
    {
        $arr = [];
        foreach ($this->data as $fc) {
            foreach ($fc->feeItems as $fi) {
                $item = array(
                    $fc->receipt_date,
                    $fc->district->name,
                    $fc->receipt_number,
                    $fi->feeType->name,
                    $fi->formatted_period_from,
                    $fi->formatted_period_to,
                    $fi->tenure,
                    $fi->amount,
                    $fc->total_amount,
                    $fc->collectedBy->name
                );
                $arr[] = $item;
            }
        }
        return $arr;
    }

    public function headings(): array
    {
        return [
            'Receipt Date',
            'District',
            'Receipt Number',
            'Particulars',
            'Period From',
            'Period To',
            'Remarksr',
            'Item Amount',
            'Total Amount',
            'Collected By',
        ];
    }
}

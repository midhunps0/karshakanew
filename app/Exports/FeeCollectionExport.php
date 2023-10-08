<?php
namespace App\Exports;

use App\Models\FeeType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FeeCollectionExport implements FromArray, WithHeadings
{
    private $feeTypes = [];
    private $feeTypeNames = [];


    public function __construct(public $data)
    {
        $this->feeTypes = FeeType::all();
        $this->feeTypeNames = $this->feeTypes->pluck('name')->toArray();
    }

    public function array(): array
    {
        $arr = [];
        foreach ($this->data as $fc) {
            $item = [];
            $item[] = $fc->district->name;
            $item[] = $fc->receipt_date;
            $item[] = $fc->receipt_number;
            $item[] = $fc->member->membership_no;
            $item[] = $fc->member->name;
            // foreach ($fc->feeItems as $fi) {
            //     $item = array(
            //         $fi->feeType->name,
            //         $fi->formatted_period_from,
            //         $fi->formatted_period_to,
            //         $fi->tenure,
            //         $fi->amount,
            //     );
            //     $arr[] = $item;
            // }
            foreach ($this->feeTypes as $ft) {
                $item[] = $this->getFeeItemAmount($fc->feeItems, $ft->id);
            }
            $item[] = $fc->total_amount;
            $item[] = $fc->notes;
            $item[] = $fc->collectedBy ? $fc->collectedBy->name : '';
            $arr[] = $item;
        }
        return $arr;
    }

    public function headings(): array
    {
        $headings = [
            'District',
            'Receipt Date',
            'Receipt Number',
            'Membership No.',
            'Member Name',
        ];

        $headings = array_merge($headings, $this->feeTypeNames);
        $otherHeadings = [
            'Total Amount',
            'Remarks',
            'Collected By',
        ];
        $headings =  array_merge($headings, $otherHeadings);

        return $headings;
    }

    private function getFeeItemAmount($feeItems, $feeTypeId)
    {
        $val = '';
        foreach ($feeItems as $fi) {
            if($fi->feeType->id == $feeTypeId) {
                $val = $fi->amount;
                break;
            }
        }
        return $val;
    }
}

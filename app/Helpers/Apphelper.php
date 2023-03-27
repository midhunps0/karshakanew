<?php
namespace App\Helpers;

use App\Models\District;
use App\Models\FeeCollection;

class AppHelper
{
    public static function formatDateForSave(string $date): string
    {
        if (strpos($date, '/')) {
            $darr = explode('/', $date);
        } elseif (strpos($date, '-')) {
            $darr = explode('-', $date);
        }
        return implode("-", array_reverse($darr));
    }

    public static function getBookNumber($district)
    {
        $code = is_int($district) ? District::find($district)->short_code
            : $district->short_code;
        return 'FY23-24-'.$code;
    }

    public static function getReceiptNumber(int|District $district)
    {
        $districtId = is_int($district) ? $district : $district->id;
        $fc = FeeCollection::where('district_id', $districtId)
            ->where('manual_numbering', false)
            ->orderBy('created_at', 'desc')->withTrashed()->get()->first();
        $lastReceiptNumeric = 0;
        if ($fc != null) {
            $t = explode('/', $fc->receipt_number);
            $lastReceiptNumeric = intval(array_pop($t));
        }
        $lastReceiptNumeric++;
        return Self::getBookNumber($district).'/'.$lastReceiptNumeric;
    }
}
?>

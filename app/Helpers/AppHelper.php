<?php
namespace App\Helpers;

use App\Models\Taluk;
use App\Models\Member;
use App\Models\Village;
use App\Models\District;
use App\Models\FeeCollection;
use Illuminate\Support\Carbon;

class AppHelper
{
    /**
     * Undocumented function
     *
     * @param string $date
     * @param string|null $inputFormat
     * @param string|null $outputFormat
     * @param string $settime Expected values: 'start' or 'end'
     * @return string
     */
    public static function formatDateForSave(string $thedate, string $inputFormat = null, string $outputFormat = null, string $setTimeTo = ''): string
    {
        if ($inputFormat == null) {
            if (strpos($thedate, '/')) {
                $inputFormat = 'd/m/Y';
            } elseif (strpos($thedate, '-')) {
                $inputFormat = 'd-m-Y';
            }
        }
        $d = Carbon::createFromFormat($inputFormat, $thedate);
        switch($setTimeTo) {
            case 'end':
                $d = $d->endOfDay();
                break;
            case 'start':
                $d = $d->startOfDay();
                break;
            default:
                break;
        }
        $outputFormat = $outputFormat ?? 'Y-m-d H:i:s';

        return $d->format($outputFormat);
    }

    public function getNextDate(string $date, string $inputFormat = 'd-m-Y', string $outputFormat = 'Y-m-d'): string
    {
        $d = Carbon::createFromFormat($inputFormat, $date);
        $d->addDay()->startOfDay();
        return $d->format($outputFormat);
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

    public static function getMembershipNumber(
        int $district,
        int $taluk,
        int $village
    ):string {
            $dcode = District::find($district)->display_code;
            $tcode = Taluk::find($taluk)->display_code;
            $vcode = Village::find($village)->display_code;
            $mnoSearch = $dcode.'/'.$tcode.'/'.$vcode.'/';
            $nos = Member::select('membership_no')
                ->where('membership_no', 'like', $mnoSearch.'%')
                ->pluck('membership_no')->toArray();
            $slnos = array_map(
                function ($n) {
                    $t = explode('/', $n);
                    return intval($t[count($t) - 1]);
                },
                $nos
            );
            $nextno = count($slnos) > 0 ? max($slnos) + 1 : 1;
            return  $mnoSearch . $nextno;
    }
}
?>

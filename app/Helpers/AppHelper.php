<?php
namespace App\Helpers;

use App\Models\Taluk;
use App\Models\Member;
use App\Models\Village;
use App\Models\District;
use App\Models\FeeCollection;
use App\Models\WelfareScheme;
use Illuminate\Support\Carbon;
use Ynotz\MediaManager\Contracts\MediaOwner;

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
        $outputFormat = $outputFormat ?? 'Y-m-d';

        return $d->format($outputFormat);
    }

    public function getNextDate(string $date, string $inputFormat = 'd-m-Y', string $outputFormat = 'Y-m-d'): string
    {
        $d = Carbon::createFromFormat($inputFormat, $date);
        $d->addDay()->startOfDay();
        return $d->format($outputFormat);
    }

    public static function getFinancialYearCode(): string
    {
        $t = Carbon::today();
        $cyear = $t->year;
        $y = $cyear % 100;
        $y = $y == 0 ? $cyear : $y;
        $fystr = '';
        if ($t->month > 2) {
            $yx = $y + 1;
            if ($yx % 100 == 0) {
                $y = $cyear;
                $yx = $cyear + 1;
            }
            $fystr = str_pad($y, 2, '0', STR_PAD_LEFT)
                . '-' . str_pad($yx, 2, '0', STR_PAD_LEFT) . '-';
        } else {
            $yx = $y - 1;
            if ($yx % 100 == 0) {
                $y = $cyear;
                $yx = $cyear - 1;
            }
            $fystr = str_pad($yx, 2, '0', STR_PAD_LEFT)
                . '-' . str_pad($y, 2, '0', STR_PAD_LEFT);
        }
        return $fystr;
    }

    public static function getBookNumber($district)
    {
        $code = is_int($district) ? District::find($district)->short_code
            : $district->short_code;

        // return 'FY23-24-'.$code;
        return Self::getFinancialYearCode().'-'.$code;
    }

    public static function getWelfareApplicationNumber($member, $schemeCode): string
    {
        $district = District::find($member->district_id);
        $slno = $district->last_application_no + 1;
        return $schemeCode.'/'.Self::getFinancialYearCode().'/'
            .$district->short_code.'/'.$slno;
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

    public static function syncImageFromRequestData(MediaOwner $instance, string $property, array $data): void
    {
        if (isset($data[$property])) {
            $instance->deleteAllMedia($property);
            $instance->addOneMediaFromEAInput($property, $data[$property]);
        }
    }

    public static function jsSafe($val)
    {
        return str_replace('"', ' ', str_replace('\'', ' ', $val));
    }
}
?>

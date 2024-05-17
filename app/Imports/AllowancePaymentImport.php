<?php
namespace App\Imports;

use App\Helpers\AppHelper;
use App\Models\Allowance;
use App\Models\ApplicationPayment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AllowancePaymentImport implements ToArray, WithHeadingRow
{
    private $importedCount = 0;
    private $totalCount = 0;
    private $failed = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function array(array $rows)
    {
        foreach ($rows as $row) {
            if ($row['application_no'] != null && trim($row['application_no']) != '') {
                $allowance = Allowance::where('application_no', $row['application_no'])->get()->first();
                if ($allowance != null) {
                    if ($allowance->district_id == auth()->user()->district_id) {
                        DB::beginTransaction();
                        try {
                            ApplicationPayment::create(
                                [
                                    'allowance_application_id' => $allowance->id,
                                    'prn_no' => $row['prn_no'],
                                    'payment_date' => AppHelper::formatDateForSave($row['date']),
                                    'amount' => floatval($row['amount']),
                                ]
                            );
                            $allowance->amount_paid = $allowance->amout_paid + floatval($row['amount']);
                            DB::commit();
                            $this->importedCount++;
                        } catch (\Throwable $e) {
                            $this->failed[] = [
                                'code' => $row['application_no'],
                                'reason' => 'Duplicate prn_no'
                            ];
                            DB::rollBack();
                        }

                    } else {
                        $this->failed[] = [
                            'code' => $row['application_no'],
                            'reason' => 'Permission denied.'
                        ];
                    }
                } else {
                    $this->failed[] = [
                        'code' => $row['application_no'],
                        'reason' => 'Invalid application number.'
                    ];
                }
                $this->totalCount++;
            }
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getFailedList(): array
    {
        return $this->failed;
    }
}

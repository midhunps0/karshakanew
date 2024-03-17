<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalAssistanceApplication extends Model implements MediaOwner
{
    use HasFactory, OwnsMedia;

    protected $table = "medical_assistances";

    protected $guarded = [];

    protected $casts = [
        'member_bank_account' => 'array',
        'medical_bills' => 'array'
    ];

    protected $appends = [
        'wb_passbook_front',
        'wb_passbook_back',
        'aadhaar_card',
        'bank_passbook',
        'union_certificate',
        'ration_card',
        'one_and_same_cert',
        'medical_bills_proofs',
        'doctors_certificate',
        'op_card_discharge_summary',
    ];

    public function allowanceApplication()
    {
        return $this->morphMany(Allowance::class, 'allowanceable');
    }

    public function getMediaStorage(): array
    {
        return [
            'wbPassbookFront' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/wb_passbook_front'
            ],
            'wbPassbookBack' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/wb_passbook_back'
            ],
            'aadhaarCard' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/aadhaar_card'
            ],
            'bankPassbook' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/bank_passbook'
            ],
            'unionCertificate' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/union_certificate'
            ],
            'rationCard' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/ration_card'
            ],
            'oneAndSameCert' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/one_and_same_cert'
            ],
            'medical_bills_proofs' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/medical_bills_proofs'
            ],
            'doctors_certificate' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/doctors_certificate'
            ],
            'op_card_discharge_summary' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/medical/op_card_discharge_summary'
            ],
        ];
    }

    protected function wbPassbookFront(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('wb_passbook_front');
            },
        );
    }

    protected function wbPassbookBack(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('wb_passbook_back');
            },
        );
    }

    protected function aadhaarCard(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('aadhaar_card');
            },
        );
    }

    protected function bankPassbook(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('bank_passbook');
            },
        );
    }

    protected function unionCertificate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('union_certificate');
            },
        );
    }

    protected function rationCard(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('ration_card');
            },
        );
    }

    protected function oneAndSameCert(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('one_and_same_cert');
            },
        );
    }

    protected function medicalBillsProofs(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getAllMedia('medical_bills_proofs');
            },
        );
    }

    protected function doctorsCertificate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('doctors_certificate');
            },
        );
    }

    protected function opCardDischargeSummary(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('op_card_discharge_summary');
            },
        );
    }

    protected function feePeriodFrom(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }

    protected function feePeriodTo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }

    protected function treatmentPeriodFrom(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }

    protected function treatmentPeriodTo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }

}

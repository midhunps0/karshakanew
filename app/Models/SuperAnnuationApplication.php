<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuperAnnuationApplication extends Model implements MediaOwner
{
    use HasFactory, OwnsMedia;

    protected $table = "super_annuations";

    protected $guarded = [];

    protected $casts = [
        'member_bank_account' => 'array'
    ];

    protected $appends = [
        'wb_passbook_front',
        'wb_passbook_back',
        'aadhaar_card',
        'bank_passbook',
        'union_certificate',
        'ration_card',
        'one_and_same_cert',
    ];

    public function allowanceApplication()
    {
        return $this->morphMany(Allowance::class, 'allowanceable');
    }

    public function getMediaStorage(): array
    {
        return [
            'wb_passbook_front' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/super_annuation/wb_passbook_front'
            ],
            'wb_passbook_back' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/super_annuation/wb_passbook_back'
            ],
            'aadhaar_card' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/super_annuation/aadhaar_card'
            ],
            'bank_passbook' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/super_annuation/bank_passbook'
            ],
            'union_certificate' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/super_annuation/union_certificate'
            ],
            'ration_card' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/super_annuation/ration_card'
            ],
            'one_and_same_cert' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/super_annuation/one_and_same_cert'
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

    protected function memberDob(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }
}

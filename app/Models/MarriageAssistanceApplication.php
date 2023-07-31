<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarriageAssistanceApplication extends Model implements MediaOwner
{
    use HasFactory, OwnsMedia;

    protected $guarded = [];

    protected $casts = [
        'member_bank_account' => 'array',
    ];

    protected $appends = [
        'wb_passbook_front',
        'wb_passbook_back',
        'aadhaar_card',
        'bank_passbook',
        'union_certificate',
        'ration_card',
        'caste_certificate',
        'one_and_same_cert',
        'marriage_certificate',
    ];

    public function allowanceApplication()
    {
        return $this->morphMany(Allowance::class, 'allowanceable');
    }

    public function getMediaStorage(): array
    {
        return [
            'wbPassbookFront' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/wb_passbook_front'
            ],
            'wbPassbookBack' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/wb_passbook_back'
            ],
            'aadhaarCard' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/aadhaar_card'
            ],
            'bankPassbook' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/bank_passbook'
            ],
            'unionCertificate' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/union_certificate'
            ],
            'rationCard' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/ration_card'
            ],
            'casteCertificate' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/caste_certificate'
            ],
            'oneAndSameCert' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/one_and_same_cert'
            ],
            'marriageCertificate' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/marriage_certificate'
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

    protected function casteCertificate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('caste_certificate');
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

    protected function marriageCertificate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('marriage_certificate');
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

    protected function marriageDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }
}

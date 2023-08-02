<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaternityAssistanceApplication extends Model
{
    use HasFactory, OwnsMedia;

    protected $table = "maternity_assistances";

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
        'one_and_same_cert',
        'birth_certificate',
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
            'oneAndSameCert' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/one_and_same_cert'
            ],
            'birthCertificate' => [
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

    protected function oneAndSameCert(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('one_and_same_cert');
            },
        );
    }

    protected function birthCertificate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('birth_certificate');
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

    protected function deliveryDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }
}

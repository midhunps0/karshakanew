<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EducationSchemeApplication extends Model implements MediaOwner
{
    use HasFactory, OwnsMedia;

    protected $guarded = [];

    protected $casts = [
        'passed_exam_details' => 'array',
        'marks_scored' => 'array',
        'total_marks' => 'array',
        'member_bank_account' => 'array',
    ];

    protected $appends = [
        'mark_list',
        'tc',
        'wb_passbook_front',
        'wb_passbook_back',
        'aadhaar_card',
        'bank_passbook',
        'union_certificate',
        'ration_card',
        'caste_certificate',
        'one_and_same_cert',
    ];

    public function allowanceApplication()
    {
        return $this->morphMany(Allowance::class, 'allowanceable');
    }

    public function getMediaStorage(): array
    {
        return [
            'mark_list'=> [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/mark_list'
            ],
            'tc' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/tc'
            ],
            'wb_passbook_front' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/wb_passbook_front'
            ],
            'wb_passbook_back' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/wb_passbook_back'
            ],
            'aadhaar_card' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/aadhaar_card'
            ],
            'bank_passbook' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/bank_passbook'
            ],
            'union_certificate' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/union_certificate'
            ],
            'ration_card' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/ration_card'
            ],
            'caste_certificate' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/caste_certificate'
            ],
            'one_and_same_cert' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/education/one_and_same_cert'
            ],
        ];
    }

    protected function markList(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('mark_list');
            },
        );
    }

    protected function tc(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('tc');
            },
        );
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
}

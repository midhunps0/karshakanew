<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeathExgraciaApplication extends Model implements MediaOwner
{
    use HasFactory, OwnsMedia;

    protected $table = "dex_applications";

    protected $guarded = [];

    protected $casts = [
        'applicant_bank_details' => 'array',
    ];

    protected $appends = [
        'wb_passbook_front',
        'wb_passbook_back',
        'aadhaar_card',
        'bank_passbook',
        'ration_card',
        'one_and_same_cert',
        'death_certificate',
        'minor_age_proof',
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
                'folder' => 'public/images/applications/dex/wb_passbook_front'
            ],
            'wb_passbook_back' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/dex/wb_passbook_back'
            ],
            'aadhaar_card' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/dex/aadhaar_card'
            ],
            'bank_passbook' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/dex/bank_passbook'
            ],
            'ration_card' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/dex/ration_card'
            ],
            'death_certificate' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/dex/death_certificate'
            ],
            'one_and_same_cert' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/dex/one_and_same_cert'
            ],
            'minor_age_proof' => [
                'disk' => 's3',
                'folder' => 'public/images/applications/dex/minor_age_proof'
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

    protected function deathCertificate(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('death_certificate');
            },
        );
    }

    protected function minorAgeProof(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->getSingleMediaForDisplay('minor_age_proof');
            },
        );
    }

    protected function dateOfDeath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null ? Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y') : '',
        );
    }
}

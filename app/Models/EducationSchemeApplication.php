<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Ynotz\MediaManager\Traits\OwnsMedia;

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

    public function allowanceApplication()
    {
        return $this->morphMany(Allowance::class, 'allowanceable');
    }

    public function getMediaStorage(): array
    {
        return [
            'markList'=> [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/mark_list'
            ],
            'tc' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/tc'
            ],
            'wpPassbookFront' => [
                'disk' => 'local',
                'folder' => 'public/images/applications/education/wp_passbook_front'
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
        ];
    }
}

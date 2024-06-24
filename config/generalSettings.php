<?php
    return [
        'local_gov_bodies' => [
            'Panchayat',
            'Municipality',
            'Corporation'
        ],
        'genders' => [
            'Male',
            'Female',
            'Others'
        ],
        'relationships' => [
            'Husband', 'Wife', 'Father', 'Mother', 'Uncle', 'Aunt', 'Grand Father', 'Grand Mother'
        ],
        'marital_status' => [
            'Unmarried',
            'Married',
            'Divorced',
            'Widow',
            'Widower'
        ],
        'fee_types_with_tenure' => [
            2, 4, 5
        ],
        'allowances' => [
            'education_assistance' => 'Education Assistance',
            'death_exgracia' => 'Death Ex-Gratia',
            'marriage' => 'Marriage Assistance',
            'maternity' => 'Maternity Assistance',
            'medical' => 'Medical Assistance',
            'super_annuation' => 'Super Annuation',
            'higher_education' => 'Higher Education Assistance'
        ],
        'membership_fee_id' => 2,
        'default_payment_mode_id' => 1,
        'districts_idmap' => [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
            7 => 7,
            8 => 8,
            9 => 9,
            10 => 10,
            11 => 11,
            12 => 13,
            13 => 12,
            14 => 14,
            15 => 15
        ],
        'fee_types_map' => [
            1 => 2,
            2 => 1,
            3 => 24,
            4 => 7,
            5 => 8,
            6 => 5,
            7 => 9,
        ],
        'roles_hierarchy' => [
            'system_admin' => [
                'State Admin',
                'District Admin',
                'State Executive',
                'District Executive',
                'Union Representative',
                'DP Operator'
                // 'Member'
            ],
            'state_admin' => [
                'District Admin',
                'State Executive',
                'District Executive',
                'Union Representative',
                'DP Operator'
                // 'Member'
            ],
            'district_admin' => [
                'State Executive',
                'District Executive',
                'Union Representative',
                // 'Member'
            ],
            'state_executive' => [],
            'district_executive' => [],
            'union_representative' => [],
            'member' => []
        ],
        'amshadhayam' => 'Amshadhayam',
        'kudissika' => 'Kudissika',
        'kudissika_fine' => 'Kudissika Fine',
        'gov_token' => '$2y$10$ScAQ42urJvGJcCqO9r5txuOvTcrn.GYj1eXFTTPHY7HUdpNjYwPcS',
        'gov_ip_address' => '172.19.0.1',
        'gov_member_data_list' => [
            'd.name as district_name',
            't.name as taluk_name',
            'v.name as village_name',
            'mobile_no',
            'aadhaar_no',
            'election_card_no',
            'eshram_card_no',
            'dob',
            'gender',
            'marital_status',
            'parent_guardian',
            'current_address',
            'current_address_mal',
            'ca_pincode',
            'permanent_address',
            'permanent_address_mal',
            'pa_pincode',
            'bank_acc_no',
            'bank_name',
            'bank_branch',
            'bank_ifsc',
            'identification_mark_a',
            'identification_mark_b',
            'work_locality',
            'work_start_date',
        ]
    ];
?>

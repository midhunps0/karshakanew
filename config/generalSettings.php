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
                'DP Operartor'
                // 'Member'
            ],
            'state_admin' => [
                'District Admin',
                'State Executive',
                'District Executive',
                'Union Representative',
                'DP Operartor'
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
        ]
    ];
?>

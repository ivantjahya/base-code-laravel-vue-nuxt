<?php

namespace Database\Seeders;

use App\Models\Master\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => '0',
                'description' => 'ALL',
                'used_for' => 'APPROVAL_PO',
                'status_group' => null,
            ],
            [
                'code' => '3',
                'description' => 'Rejected',
                'used_for' => 'APPROVAL_PO',
                'status_group' => null,
            ],
            [
                'code' => '5',
                'description' => 'Supplier PO',
                'used_for' => 'APPROVAL_PO',
                'status_group' => null,
            ],
            [
                'code' => '6',
                'description' => 'Waiting-Approve-MD-SPM',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-SPM',
            ],
            [
                'code' => '7',
                'description' => 'GOLD -',
                'used_for' => 'APPROVAL_PO',
                'status_group' => null,
            ],
            [
                'code' => '8',
                'description' => 'Partially Delivered',
                'used_for' => 'APPROVAL_PO',
                'status_group' => null,
            ],
            [
                'code' => '9',
                'description' => 'Printed',
                'used_for' => 'APPROVAL_PO',
                'status_group' => null,
            ],
            [
                'code' => '10',
                'description' => 'Waiting-Approve-Buyer',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'STORE',
            ],
            [
                'code' => '11',
                'description' => 'Waiting-Approve-Store-Manager',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'STORE',
            ],
            [
                'code' => '62',
                'description' => 'Waiting-Approve-Senior-MD-SPM',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-SPM',
            ],
            [
                'code' => '63',
                'description' => 'Waiting-Approve-MD-Manager-SPM',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-SPM',
            ],
            [
                'code' => '64',
                'description' => 'Waiting-Approve-Senior-MD-Manager-SPM',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-SPM',
            ],
            [
                'code' => '65',
                'description' => 'Waiting-Approve-Director-SPM',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-SPM',
            ],
            [
                'code' => '67',
                'description' => 'Waiting-Approve-Director-X',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'DIRECTOR-X',
            ],
            [
                'code' => '68',
                'description' => 'Waiting-Approve-MD-FSH',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-FSH',
            ],
            [
                'code' => '69',
                'description' => 'Waiting-Approve-Senior-MD-FSH',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-FSH',
            ],
            [
                'code' => '70',
                'description' => 'Waiting-Approve-MD-Manager-FSH',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-FSH',
            ],
            [
                'code' => '71',
                'description' => 'Waiting-Approve-Senior-Manager-FSH',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-FSH',
            ],
            [
                'code' => '72',
                'description' => 'Waiting-Approve-Director-FSH',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MD-FSH',
            ],
            [
                'code' => '100',
                'description' => 'Waiting-Approve-GC-Buyer',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'GC',
            ],
            [
                'code' => '101',
                'description' => 'Waiting-Approve-GC-MD-Chief',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'GC',
            ],
            [
                'code' => '102',
                'description' => 'Waiting-Approve-GC-MD-Manager',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'GC',
            ],
            [
                'code' => '103',
                'description' => 'Waiting-Approve-GC-Director',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'GC',
            ],
            [
                'code' => '105',
                'description' => 'Waiting-Approve-DCL-Buyer',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'DCL',
            ],
            [
                'code' => '106',
                'description' => 'Waiting-Approve-Manager-X',
                'used_for' => 'APPROVAL_PO',
                'status_group' => 'MANAGER-X',
            ],
        ];

        foreach ($data as $item) {
            Status::firstOrCreate([
                'code' => $item['code'],
            ], [
                'description' => $item['description'],
                'used_for' => $item['used_for'],
                'status_group' => $item['status_group'],
            ]);
        }
    }
}

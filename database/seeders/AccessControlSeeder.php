<?php

namespace Database\Seeders;

use App\Models\AccessControl;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['code' => 'create', 'name' => 'Permission to create data'],
            ['code' => 'update', 'name' => 'Permission to update data'],
            ['code' => 'delete', 'name' => 'Permission to delete data'],
            ['code' => 'upload', 'name' => 'Permission to upload files'],
            ['code' => 'export', 'name' => 'Permission to download files'],
            ['code' => 'viewdetail', 'name' => 'Permission to view detail data'],
            ['code' => 'print', 'name' => 'Permission to preview data'],
            ['code' => 'approve', 'name' => 'Permission to approve data'],
        ];

        foreach ($data as $item) {
            AccessControl::firstOrCreate([
                'code' => $item['code'],
            ], [
                'name' => $item['name'],
            ]);
        }
    }
}

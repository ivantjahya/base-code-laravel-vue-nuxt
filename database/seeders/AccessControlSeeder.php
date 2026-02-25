<?php

namespace Database\Seeders;

use App\Models\Master\AccessControl;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['code' => 'create', 'name' => 'Create'],
            ['code' => 'update', 'name' => 'Update'],
            ['code' => 'delete', 'name' => 'Delete'],
            ['code' => 'upload', 'name' => 'Upload'],
            ['code' => 'export', 'name' => 'Download'],
            ['code' => 'viewdetail', 'name' => 'View Detail'],
            ['code' => 'print', 'name' => 'Print'],
            ['code' => 'approve', 'name' => 'Approve'],
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

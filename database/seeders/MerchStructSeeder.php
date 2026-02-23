<?php

namespace Database\Seeders;

use App\Models\Master\MerchStruct;
use Illuminate\Database\Seeder;

class MerchStructSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [ 'code' => '0', 'name' => 'ALL', 'parent_id' => null, ],
        ];

        foreach ($data as $item) {
            MerchStruct::firstOrCreate([
                'code' => $item['code'],
            ], [
                'name' => $item['name'],
                'parent_id' => $item['parent_id'],
            ]);
        }
    }
}

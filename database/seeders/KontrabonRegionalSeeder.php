<?php

namespace Database\Seeders;

use App\Models\Master\KontrabonRegional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KontrabonRegionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'D01',
                'initial' => 'D01',
                'name' => 'DC JAKARTA',
                'address' => 'WISMA EKA JIWA RUKO NO:26-27 JL.ARTERI MANGGA DUA RAYA.',
                'city' => 'JAKARTA PUSAT',
                'region' => 'DKI JAKARTA'
            ],
            [
                'code' => 'H01',
                'initial' => 'H01',
                'name' => 'YOGYA CENTER',
                'address' => 'JL. SOEKARNO HATTA NO.236',
                'city' => 'BANDUNG',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'H02',
                'initial' => 'H02',
                'name' => 'GRIYA CENTER',
                'address' => 'JL. SOEKARNO HATTA NO.236',
                'city' => 'BANDUNG',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'H05',
                'initial' => 'H05',
                'name' => 'JUNCTION CENTER',
                'address' => 'JL. SUNDA NO.83',
                'city' => 'BANDUNG',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'H06',
                'initial' => 'H06',
                'name' => 'RUKO SAWOJAJAR',
                'address' => 'JL. SAWOJAJAR NO.37',
                'city' => 'BOGOR',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'S02',
                'initial' => 'S02',
                'name' => 'YOGYA GARUT',
                'address' => 'JL. SILIWANGI NO.21',
                'city' => 'GARUT',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'S03',
                'initial' => 'S03',
                'name' => 'YOGYA GRAND CIREBON',
                'address' => 'JL. KARANG GETAS NO.64',
                'city' => 'CIREBON',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'S04',
                'initial' => 'S04',
                'name' => 'YOGYA PURWAKARTA',
                'address' => 'JL. JEND SUDIRMAN NO.5',
                'city' => 'PURWAKARTA',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'S05',
                'initial' => 'S05',
                'name' => 'YOGYA SUKABUMI',
                'address' => 'JL. LLRE MARTADINATA NO.3',
                'city' => 'SUKABUMI',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'S07',
                'initial' => 'S07',
                'name' => 'YOGYA TASIK HZ',
                'address' => 'JL. HZ MUSTOFA NO.124',
                'city' => 'TASIKMALAYA',
                'region' => 'JAWA BARAT'
            ],
            [
                'code' => 'S08',
                'initial' => 'S08',
                'name' => 'YOGYA TEGAL',
                'address' => 'JL. AR. HAKIM NO.16',
                'city' => 'TEGAL',
                'region' => 'JAWA TENGAH'
            ],
        ];

        foreach ($data as $item) {
            KontrabonRegional::firstOrCreate([
                'code' => $item['code'],
            ], [
                'initial' => $item['initial'],
                'name' => $item['name'],
                'address' => $item['address'],
                'city' => $item['city'],
                'region' => $item['region'],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\AccessControl;
use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Home',
                'url' => '/home',
                'icon' => 'i-lucide-house',
                'submenu' => [],
            ],
            [
                'name' => 'Master Data',
                'url' => null,
                'icon' => 'i-lucide-folder-open',
                'submenu' => [
                    [
                        'name' => 'Limits',
                        'url' => '/limits',
                        'icon' => null,
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Profiles',
                        'url' => '/profiles',
                        'icon' => null,
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Functional Profiles',
                        'url' => '/functional-profiles',
                        'icon' => null,
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Users',
                        'url' => '/users',
                        'icon' => null,
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Approval Flow',
                        'url' => '/approval-flow',
                        'icon' => null,
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Regional Site',
                        'url' => '/regional-site',
                        'icon' => null,
                        'access_control' => [],
                    ],
                    [
                        'name' => 'User Guide',
                        'url' => '/user-guide',
                        'icon' => null,
                        'access_control' => ['create', 'update', 'delete'],
                    ],
                ],
            ],
            [
                'name' => 'New Registration',
                'url' => null,
                'icon' => 'i-lucide-file-pen',
                'submenu' => [
                    [
                        'name' => 'Article',
                        'url' => '/article',
                        'icon' => null,
                        'access_control' => ['create', 'update', 'upload', 'export', 'viewdetail'],
                    ],
                    [
                        'name' => 'Supplier',
                        'url' => '/supplier',
                        'icon' => null,
                        'access_control' => ['update', 'viewdetail'],
                    ],
                ],
            ],
            [
                'name' => 'Purchase Order',
                'url' => null,
                'icon' => 'i-luicide-baggage-claim',
                'submenu' => [
                    [
                        'name' => 'PO Status Report',
                        'url' => '/po-status-report',
                        'icon' => null,
                        'access_control' => ['create', 'export', 'viewdetail'],
                    ],
                    [
                        'name' => 'PO List',
                        'url' => '/po-list',
                        'icon' => null,
                        'access_control' => ['viewdetail', 'print'],
                    ],
                    [
                        'name' => 'PO Cross Dock',
                        'url' => '/po-cross-dock',
                        'icon' => null,
                        'access_control' => ['update', 'print'],
                    ],
                    [
                        'name' => 'Return',
                        'url' => '/return',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                ],
            ],
            [
                'name' => 'Consignment',
                'url' => null,
                'icon' => 'i-lucide-handshake',
                'submenu' => [
                    [
                        'name' => 'Pengajuan Retur',
                        'url' => '/pengajuan-retur',
                        'icon' => null,
                        'access_control' => ['create', 'update', 'delete'],
                    ],
                    [
                        'name' => 'Pengajuan Acara',
                        'url' => '/pengajuan-acara',
                        'icon' => null,
                        'access_control' => ['create', 'update', 'delete', 'export'],
                    ],
                    [
                        'name' => 'Upload Brand Store',
                        'url' => '/upload-brand-store',
                        'icon' => null,
                        'access_control' => ['upload'],
                    ],
                    [
                        'name' => 'Surat Acara',
                        'url' => '/surat-acara',
                        'icon' => null,
                        'access_control' => ['export', 'viewdetail'],
                    ],
                ],
            ],
            [
                'name' => 'Principal Report',
                'url' => null,
                'icon' => 'i-lucide-file-chart-column',
                'submenu' => [
                    [
                        'name' => 'Stock',
                        'url' => '/stock',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sales',
                        'url' => '/sales',
                        'icon' => null,
                        'access_control' => ['viewdetail'],
                    ],
                    [
                        'name' => 'Frozen For Purchase',
                        'url' => '/frozen-for-purchase',
                        'icon' => null,
                        'access_control' => ['viewdetail'],
                    ],
                    [
                        'name' => 'Market Share',
                        'url' => '/market-share',
                        'icon' => null,
                        'access_control' => ['viewdetail'],
                    ],
                    [
                        'name' => 'Merchandise Structure',
                        'url' => '/merchandise-structure',
                        'icon' => null,
                        'access_control' => ['viewdetail'],
                    ],
                    [
                        'name' => 'Sell Out By Month',
                        'url' => '/sell-out-by-month',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sell Out By Div Month',
                        'url' => '/sell-out-by-div-month',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sell Out By Div By Cat By Brand',
                        'url' => '/sell-out-by-div-by-cat-by-brand',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => '50 Top SKU',
                        'url' => '/50-top-sku',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sell In By Month',
                        'url' => '/sell-in-by-month',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sell In By Div By Month',
                        'url' => '/sell-in-by-div-by-month',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'SL By Month',
                        'url' => '/sl-by-month',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'SL By Div By Month',
                        'url' => '/sl-by-div-by-month',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'SL Distributor By Div By Month',
                        'url' => '/sl-distributor-by-div-by-month',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                ],
            ],
            [
                'name' => 'Finance',
                'url' => null,
                'icon' => 'i-lucide-wallet',
                'submenu' => [
                    [
                        'name' => 'Payment Check',
                        'url' => '/payment-check',
                        'icon' => null,
                        'access_control' => ['print'],
                    ],
                    [
                        'name' => 'Invoice Input Putus',
                        'url' => '/invoice-input-putus',
                        'icon' => null,
                        'access_control' => ['create', 'upload', 'print'],
                    ],
                    [
                        'name' => 'Invoice Consignment',
                        'url' => '/invoice-consignment',
                        'icon' => null,
                        'access_control' => ['create', 'print'],
                    ],
                    [
                        'name' => 'PO Kontra Bon',
                        'url' => '/po-kontra-bon',
                        'icon' => null,
                        'access_control' => ['print'],
                    ],
                    [
                        'name' => 'Data Retur Coretax',
                        'url' => '/data-retur-coretax',
                        'icon' => null,
                        'access_control' => ['create', 'viewdetail'],
                    ],
                    [
                        'name' => 'Report Payment',
                        'url' => '/report-payment',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Tax Supplier Data',
                        'url' => '/tax-supplier-data',
                        'icon' => null,
                        'access_control' => ['export', 'viewdetail'],
                    ],
                    [
                        'name' => 'Billing',
                        'url' => '/billing',
                        'icon' => null,
                        'access_control' => ['upload', 'export', 'viewdetail'],
                    ],
                ],
            ],
            [
                'name' => 'DC Fee',
                'url' => null,
                'icon' => 'i-lucide-truck',
                'submenu' => [
                    [
                        'name' => 'DC List',
                        'url' => '/dc-list',
                        'icon' => null,
                        'access_control' => ['create', 'update', 'delete', 'viewdetail'],
                    ],
                    [
                        'name' => 'Handling Fee',
                        'url' => '/handling-fee',
                        'icon' => null,
                        'access_control' => [],
                    ],
                    [
                        'name' => 'Distribution Fee',
                        'url' => '/distribution-fee',
                        'icon' => null,
                        'access_control' => ['create', 'update', 'delete'],
                    ],
                    [
                        'name' => 'DC Fee Supplier',
                        'url' => '/dc-fee-supplier',
                        'icon' => null,
                        'access_control' => ['update'],
                    ],
                    [
                        'name' => 'Set Handling Method',
                        'url' => '/set-handling-method',
                        'icon' => null,
                        'access_control' => [],
                    ],
                    [
                        'name' => 'DC Fee Validation',
                        'url' => '/dc-fee-validation',
                        'icon' => null,
                        'access_control' => ['approve'],
                    ],
                    [
                        'name' => 'DC Fee By Receiving',
                        'url' => '/dc-fee-by-receiving',
                        'icon' => null,
                        'access_control' => ['export'],
                    ],
                ],
            ],
            [
                'name' => 'Tax Supplier Data',
                'url' => '/tax-supplier-data',
                'icon' => 'i-lucide-circle-dollar-sign',
                'submenu' => [],
            ],
        ];

        foreach ($data as $menu) {
            $parent = Menu::firstOrCreate([
                'url' => $menu['url'],
                'name' => $menu['name'],
            ], [
                'icon' => $menu['icon'],
            ]);

            foreach ($menu['submenu'] as $submenus) {
                $submenu = Menu::firstOrCreate([
                    'url' => $submenus['url'],
                ], [
                    'name' => $submenus['name'],
                    'icon' => $submenus['icon'],
                    'parent_id' => $parent->id,
                ]);

                /** Menu access control */
                foreach ($submenus['access_control'] as $code) {
                    $accessControl = AccessControl::where('code', $code)->first();
                    if ($accessControl) {
                        DB::table('menu_acc_controls')->updateOrInsert(
                            [
                                'menu_id' => $submenu->id,
                                'acc_control_id' => $accessControl->id,
                            ], []
                        );
                    }
                }
            }
        }
    }
}

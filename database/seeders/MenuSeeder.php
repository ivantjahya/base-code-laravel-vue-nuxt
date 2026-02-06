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
                'code' => 0,
                'name_code' => 'menu.home',
                'submenu' => [],
            ],
            [
                'name' => 'Master Data',
                'url' => null,
                'icon' => 'i-lucide-folder-open',
                'code' => 1000,
                'name_code' => 'menu.master-data',
                'submenu' => [
                    [
                        'name' => 'Limits',
                        'url' => '/limit-management',
                        'icon' => null,
                        'code' => 1010,
                        'name_code' => 'page.limit-management',
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Profiles',
                        'url' => '/profile-management',
                        'icon' => null,
                        'code' => 1020,
                        'name_code' => 'page.profile-management',
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Functional Profiles',
                        'url' => '/functional-profile-management',
                        'icon' => null,
                        'code' => 1030,
                        'name_code' => 'page.functional-profile-management',
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Users',
                        'url' => '/user-management',
                        'icon' => null,
                        'code' => 1040,
                        'name_code' => 'page.user-management',
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Approval Flow',
                        'url' => '/approval-flow-management',
                        'icon' => null,
                        'code' => 1050,
                        'name_code' => 'page.approval-flow-management',
                        'access_control' => ['create', 'update'],
                    ],
                    [
                        'name' => 'Regional Site',
                        'url' => '/regional-site',
                        'icon' => null,
                        'code' => 1060,
                        'name_code' => 'page.regional-site',
                        'access_control' => [],
                    ],
                    [
                        'name' => 'User Guide',
                        'url' => '/user-guide-management',
                        'icon' => null,
                        'code' => 1070,
                        'name_code' => 'page.user-guide-management',
                        'access_control' => ['create', 'update', 'delete'],
                    ],
                ],
            ],
            [
                'name' => 'New Registration',
                'url' => null,
                'icon' => 'i-lucide-file-pen',
                'code' => 2000,
                'name_code' => 'menu.new-registration',
                'submenu' => [
                    [
                        'name' => 'Article',
                        'url' => '/article',
                        'icon' => null,
                        'code' => 2010,
                        'name_code' => 'page.article',
                        'access_control' => ['create', 'update', 'upload', 'export', 'viewdetail'],
                    ],
                    [
                        'name' => 'Supplier',
                        'url' => '/supplier',
                        'icon' => null,
                        'code' => 2020,
                        'name_code' => 'page.supplier',
                        'access_control' => ['update', 'viewdetail'],
                    ],
                ],
            ],
            [
                'name' => 'Purchase Order',
                'url' => null,
                'icon' => 'i-luicide-baggage-claim',
                'code' => 3000,
                'name_code' => 'menu.purchase-order',
                'submenu' => [
                    [
                        'name' => 'PO Status Report',
                        'url' => '/po-status-report',
                        'icon' => null,
                        'code' => 3010,
                        'name_code' => 'page.po-status-report',
                        'access_control' => ['create', 'export', 'viewdetail'],
                    ],
                    [
                        'name' => 'PO List',
                        'url' => '/po-list',
                        'icon' => null,
                        'code' => 3020,
                        'name_code' => 'page.po-list',
                        'access_control' => ['viewdetail', 'print'],
                    ],
                    [
                        'name' => 'PO Cross Dock',
                        'url' => '/po-cross-dock',
                        'icon' => null,
                        'code' => 3030,
                        'name_code' => 'page.po-cross-dock',
                        'access_control' => ['update', 'print'],
                    ],
                    [
                        'name' => 'Return',
                        'url' => '/return',
                        'icon' => null,
                        'code' => 3040,
                        'name_code' => 'page.return',
                        'access_control' => ['export'],
                    ],
                ],
            ],
            [
                'name' => 'Consignment',
                'url' => null,
                'icon' => 'i-lucide-handshake',
                'code' => 4000,
                'name_code' => 'menu.consignment',
                'submenu' => [
                    [
                        'name' => 'Pengajuan Retur',
                        'url' => '/pengajuan-retur',
                        'icon' => null,
                        'code' => 4010,
                        'name_code' => 'page.pengajuan-retur',
                        'access_control' => ['create', 'update', 'delete'],
                    ],
                    [
                        'name' => 'Pengajuan Acara',
                        'url' => '/pengajuan-acara',
                        'icon' => null,
                        'code' => 4020,
                        'name_code' => 'page.pengajuan-acara',
                        'access_control' => ['create', 'update', 'delete', 'export'],
                    ],
                    [
                        'name' => 'Upload Brand Store',
                        'url' => '/upload-brand-store',
                        'icon' => null,
                        'code' => 4030,
                        'name_code' => 'page.upload-brand-store',
                        'access_control' => ['upload'],
                    ],
                    [
                        'name' => 'Surat Acara',
                        'url' => '/surat-acara',
                        'icon' => null,
                        'code' => 4040,
                        'name_code' => 'page.surat-acara',
                        'access_control' => ['export', 'viewdetail'],
                    ],
                ],
            ],
            [
                'name' => 'Principal Report',
                'url' => null,
                'icon' => 'i-lucide-file-chart-column',
                'code' => 5000,
                'name_code' => 'menu.principal-report',
                'submenu' => [
                    [
                        'name' => 'Stock',
                        'url' => '/stock-report',
                        'icon' => null,
                        'code' => 5010,
                        'name_code' => 'page.stock-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sales',
                        'url' => '/sales-report',
                        'icon' => null,
                        'code' => 5020,
                        'name_code' => 'page.sales-report',
                        'access_control' => ['viewdetail'],
                    ],
                    [
                        'name' => 'Frozen For Purchase',
                        'url' => '/frozen-for-purchase-report',
                        'icon' => null,
                        'code' => 5030,
                        'name_code' => 'page.frozen-for-purchase-report',
                        'access_control' => ['viewdetail'],
                    ],
                    [
                        'name' => 'Market Share',
                        'url' => '/market-share-report',
                        'icon' => null,
                        'code' => 5040,
                        'name_code' => 'page.market-share-report',
                        'access_control' => ['viewdetail'],
                    ],
                    [
                        'name' => 'Merchandise Structure',
                        'url' => '/merchandise-structure-report',
                        'icon' => null,
                        'code' => 5050,
                        'name_code' => 'page.merchandise-structure-report',
                        'access_control' => ['viewdetail'],
                    ],
                    [
                        'name' => 'Sell Out By Month',
                        'url' => '/sell-out-by-month-report',
                        'icon' => null,
                        'code' => 5060,
                        'name_code' => 'page.sell-out-by-month-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sell Out By Div Month',
                        'url' => '/sell-out-by-div-month-report',
                        'icon' => null,
                        'code' => 5070,
                        'name_code' => 'page.sell-out-by-div-month-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sell Out By Div By Cat By Brand',
                        'url' => '/sell-out-by-div-by-cat-by-brand-report',
                        'icon' => null,
                        'code' => 5080,
                        'name_code' => 'page.sell-out-by-div-by-cat-by-brand-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => '50 Top SKU',
                        'url' => '/50-top-sku-report',
                        'icon' => null,
                        'code' => 5090,
                        'name_code' => 'page.50-top-sku-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sell In By Month',
                        'url' => '/sell-in-by-month-report',
                        'icon' => null,
                        'code' => 5100,
                        'name_code' => 'page.sell-in-by-month-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Sell In By Div By Month',
                        'url' => '/sell-in-by-div-by-month-report',
                        'icon' => null,
                        'code' => 5110,
                        'name_code' => 'page.sell-in-by-div-by-month-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'SL By Month',
                        'url' => '/sl-by-month-report',
                        'icon' => null,
                        'code' => 5120,
                        'name_code' => 'page.sl-by-month-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'SL By Div By Month',
                        'url' => '/sl-by-div-by-month-report',
                        'icon' => null,
                        'code' => 5130,
                        'name_code' => 'page.sl-by-div-by-month-report',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'SL Distributor By Div By Month',
                        'url' => '/sl-distributor-by-div-by-month-report',
                        'icon' => null,
                        'code' => 5140,
                        'name_code' => 'page.sl-distributor-by-div-by-month-report',
                        'access_control' => ['export'],
                    ],
                ],
            ],
            [
                'name' => 'Finance',
                'url' => null,
                'icon' => 'i-lucide-wallet',
                'code' => 6000,
                'name_code' => 'page.finance',
                'submenu' => [
                    [
                        'name' => 'Payment Check',
                        'url' => '/payment-check',
                        'icon' => null,
                        'code' => 6010,
                        'name_code' => 'page.payment-check',
                        'access_control' => ['print'],
                    ],
                    [
                        'name' => 'Invoice Input Putus',
                        'url' => '/invoice-input-putus',
                        'icon' => null,
                        'code' => 6020,
                        'name_code' => 'page.invoice-input-putus',
                        'access_control' => ['create', 'upload', 'print'],
                    ],
                    [
                        'name' => 'Invoice Consignment',
                        'url' => '/invoice-consignment',
                        'icon' => null,
                        'code' => 6030,
                        'name_code' => 'page.invoice-consignment',
                        'access_control' => ['create', 'print'],
                    ],
                    [
                        'name' => 'PO Kontra Bon',
                        'url' => '/po-kontra-bon',
                        'icon' => null,
                        'code' => 6040,
                        'name_code' => 'page.po-kontra-bon',
                        'access_control' => ['print'],
                    ],
                    [
                        'name' => 'Data Retur Coretax',
                        'url' => '/data-retur-coretax',
                        'icon' => null,
                        'code' => 6050,
                        'name_code' => 'page.data-retur-coretax',
                        'access_control' => ['create', 'viewdetail'],
                    ],
                    [
                        'name' => 'Report Payment',
                        'url' => '/report-payment',
                        'icon' => null,
                        'code' => 6060,
                        'name_code' => 'page.report-payment',
                        'access_control' => ['export'],
                    ],
                    [
                        'name' => 'Billing',
                        'url' => '/billing',
                        'icon' => null,
                        'code' => 6070,
                        'name_code' => 'page.billing',
                        'access_control' => ['upload', 'export', 'viewdetail'],
                    ],
                ],
            ],
            [
                'name' => 'DC Fee',
                'url' => null,
                'icon' => 'i-lucide-truck',
                'code' => 7000,
                'name_code' => 'page.dc-fee',
                'submenu' => [
                    [
                        'name' => 'DC List',
                        'url' => '/dc-list',
                        'icon' => null,
                        'code' => 7010,
                        'name_code' => 'page.dc-list',
                        'access_control' => ['create', 'update', 'delete', 'viewdetail'],
                    ],
                    [
                        'name' => 'Handling Fee',
                        'url' => '/handling-fee',
                        'icon' => null,
                        'code' => 7020,
                        'name_code' => 'page.handling-fee',
                        'access_control' => [],
                    ],
                    [
                        'name' => 'Distribution Fee',
                        'url' => '/distribution-fee',
                        'icon' => null,
                        'code' => 7030,
                        'name_code' => 'page.distribution-fee',
                        'access_control' => ['create', 'update', 'delete'],
                    ],
                    [
                        'name' => 'DC Fee Supplier',
                        'url' => '/dc-fee-supplier',
                        'icon' => null,
                        'code' => 7040,
                        'name_code' => 'page.dc-fee-supplier',
                        'access_control' => ['update'],
                    ],
                    [
                        'name' => 'Set Handling Method',
                        'url' => '/set-handling-method',
                        'icon' => null,
                        'code' => 7050,
                        'name_code' => 'page.set-handling-method',
                        'access_control' => [],
                    ],
                    [
                        'name' => 'DC Fee Validation',
                        'url' => '/dc-fee-validation',
                        'icon' => null,
                        'code' => 7060,
                        'name_code' => 'page.dc-fee-validation',
                        'access_control' => ['approve'],
                    ],
                    [
                        'name' => 'DC Fee By Receiving',
                        'url' => '/dc-fee-by-receiving',
                        'icon' => null,
                        'code' => 7070,
                        'name_code' => 'page.dc-fee-by-receiving',
                        'access_control' => ['export'],
                    ],
                ],
            ],
            [
                'name' => 'Tax Supplier Data',
                'url' => '/tax-supplier-data',
                'icon' => 'i-lucide-circle-dollar-sign',
                'code' => 8000,
                'name_code' => 'menu.tax-supplier-data',
                'submenu' => [],
            ],
        ];

        foreach ($data as $menu) {
            $parent = Menu::firstOrCreate([
                'url' => $menu['url'],
                'name' => $menu['name'],
            ], [
                'icon' => $menu['icon'],
                'code' => $menu['code'],
                'name_code' => $menu['name_code'],
            ]);

            foreach ($menu['submenu'] as $submenus) {
                $submenu = Menu::firstOrCreate([
                    'url' => $submenus['url'],
                ], [
                    'name' => $submenus['name'],
                    'icon' => $submenus['icon'],
                    'code' => $submenus['code'],
                    'name_code' => $submenus['name_code'],
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

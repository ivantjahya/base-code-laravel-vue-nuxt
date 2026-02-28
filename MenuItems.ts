const menuItems = ref([
  {
    name: 'Home',
    url: '/home',
    icon: 'i-lucide-house',
    code: 0,
    submenu: [],
    name_code: 'menu.home',
    active: false
  },
  {
    name: 'Master Data',
    url: null,
    icon: 'i-lucide-folder-open',
    code: 1000,
    name_code: 'menu.master-data',
    submenu: [
      {
        name: 'Limits',
        url: '/limit-management',
        icon: null,
        code: 1010,
        name_code: 'page.limit-management',
        submenu: [],
        active: false
      },
      {
        name: 'Profiles',
        url: '/profile-management',
        icon: null,
        code: 1020,
        name_code: 'page.profile-management',
        submenu: [],
        active: false
      },
      {
        name: 'Functional Profiles',
        url: '/functional-profile-management',
        icon: null,
        code: 1030,
        name_code: 'page.functional-profile-management',
        submenu: [],
        active: false
      },
      {
        name: 'Users',
        url: '/user-management',
        icon: null,
        code: 1040,
        name_code: 'page.user-management',
        submenu: [],
        active: false
      },
      {
        name: 'Approval Flow',
        url: '/approval-flow-management',
        icon: null,
        code: 1050,
        name_code: 'page.approval-flow-management',
        submenu: [],
        active: false
      },
      {
        name: 'Regional Site',
        url: '/regional-site',
        icon: null,
        code: 1060,
        name_code: 'page.regional-site',
        submenu: [],
        active: false
      },
      {
        name: 'User Guide',
        url: '/user-guide-management',
        icon: null,
        code: 1070,
        name_code: 'page.user-guide-management',
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'New Registration',
    url: null,
    icon: 'i-lucide-file-pen',
    code: 2000,
    name_code: 'menu.new-registration',
    submenu: [
      {
        name: 'Article',
        url: '/article',
        icon: null,
        code: 2010,
        name_code: 'page.article',
        submenu: [],
        active: false
      },
      {
        name: 'Supplier',
        url: '/supplier',
        icon: null,
        code: 2020,
        name_code: 'page.supplier',
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Purchase Order',
    url: null,
    icon: 'i-lucide-baggage-claim',
    code: 3000,
    name_code: 'menu.purchase-order',
    submenu: [
      {
        name: 'PO Status Report',
        url: '/po-status-report',
        icon: null,
        code: 3010,
        name_code: 'page.po-status-report',
        submenu: [],
        active: false
      },
      {
        name: 'PO List',
        url: '/po-list',
        icon: null,
        code: 3020,
        name_code: 'page.po-list',
        submenu: [],
        active: false
      },
      {
        name: 'PO Cross Dock',
        url: '/po-cross-dock',
        icon: null,
        code: 3030,
        name_code: 'page.po-cross-dock',
        submenu: [],
        active: false
      },
      {
        name: 'Return',
        url: '/return',
        icon: null,
        code: 3040,
        name_code: 'page.return',
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Consignment',
    url: null,
    icon: 'i-lucide-handshake',
    code: 4000,
    name_code: 'menu.consignment',
    submenu: [
      {
        name: 'Pengajuan Retur',
        url: '/pengajuan-retur',
        icon: null,
        code: 4010,
        name_code: 'page.pengajuan-retur',
        submenu: [],
        active: false
      },
      {
        name: 'Pengajuan Acara',
        url: '/pengajuan-acara',
        icon: null,
        code: 4020,
        name_code: 'page.pengajuan-acara',
        submenu: [],
        active: false
      },
      {
        name: 'Upload Brand Store',
        url: '/upload-brand-store',
        icon: null,
        code: 4030,
        name_code: 'page.upload-brand-store',
        submenu: [],
        active: false
      },
      {
        name: 'Surat Acara',
        url: '/surat-acara',
        icon: null,
        code: 4040,
        name_code: 'page.surat-acara',
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Principal Report',
    url: null,
    icon: 'i-lucide-file-chart-column',
    code: 5000,
    name_code: 'menu.principal-report',
    submenu: [
      {
        name: 'Stock',
        url: '/stock-report',
        icon: null,
        code: 5010,
        name_code: 'page.stock-report',
        submenu: [],
        active: false
      },
      {
        name: 'Sales',
        url: '/sales-report',
        icon: null,
        code: 5020,
        name_code: 'page.sales-report',
        submenu: [],
        active: false
      },
      {
        name: 'Frozen for Purchase',
        url: '/frozen-for-purchase-report',
        icon: null,
        code: 5030,
        name_code: 'page.frozen-for-purchase-report',
        submenu: [],
        active: false
      },
      {
        name: 'Market Share',
        url: '/market-share-report',
        icon: null,
        code: 5040,
        name_code: 'page.market-share-report',
        submenu: [],
        active: false
      },
      {
        name: 'Merchandise Structure',
        url: '/merchandise-structure-report',
        icon: null,
        code: 5050,
        name_code: 'page.merchandise-structure-report',
        submenu: [],
        active: false
      },
      {
        name: 'Sell Out By Month',
        url: '/sell-out-by-month-report',
        icon: null,
        code: 5060,
        name_code: 'page.sell-out-by-month-report',
        submenu: [],
        active: false
      },
      {
        name: 'Sell Out By Div Month',
        url: '/sell-out-by-div-month-report',
        icon: null,
        code: 5070,
        name_code: 'page.sell-out-by-div-month-report',
        submenu: [],
        active: false
      },
      {
        name: 'Sell Out By Div By Cat By Brand',
        url: '/sell-out-by-div-by-cat-by-brand-report',
        icon: null,
        code: 5080,
        name_code: 'page.sell-out-by-div-by-cat-by-brand-report',
        submenu: [],
        active: false
      },
      {
        name: '50 Top SKU',
        url: '/50-top-sku-report',
        icon: null,
        code: 5090,
        name_code: 'page.50-top-sku-report',
        submenu: [],
        active: false
      },
      {
        name: 'Sell In By Month',
        url: '/sell-in-by-month-report',
        icon: null,
        code: 5100,
        name_code: 'page.sell-in-by-month-report',
        submenu: [],
        active: false
      },
      {
        name: 'Sell In By Div Month',
        url: '/sell-in-by-div-month-report',
        icon: null,
        code: 5110,
        name_code: 'page.sell-in-by-div-month-report',
        submenu: [],
        active: false
      },
      {
        name: 'SL By Month',
        url: '/sl-by-month-report',
        icon: null,
        code: 5120,
        name_code: 'page.sl-by-month-report',
        submenu: [],
        active: false
      },
      {
        name: 'SL By Div Month',
        url: '/sl-by-div-month-report',
        icon: null,
        code: 5130,
        name_code: 'page.sl-by-div-month-report',
        submenu: [],
        active: false
      },
      {
        name: 'SL Distributor By Div Month',
        url: '/sl-distributor-by-div-by-month-report',
        icon: null,
        code: 5140,
        name_code: 'page.sl-distributor-by-div-by-month-report',
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Finance',
    url: null,
    icon: 'i-lucide-wallet',
    code: 6000,
    name_code: 'menu.finance',
    submenu: [
      {
        name: 'Payment Check',
        url: '/payment-check',
        icon: null,
        code: 6010,
        name_code: 'page.payment-check',
        submenu: [],
        active: false
      },
      {
        name: 'Invoice Input Putus',
        url: '/invoice-input-putus',
        icon: null,
        code: 6020,
        name_code: 'page.invoice-input-putus',
        submenu: [],
        active: false
      },
      {
        name: 'Invoice Consignment',
        url: '/invoice-consignment',
        icon: null,
        code: 6030,
        name_code: 'page.invoice-consignment',
        submenu: [],
        active: false
      },
      {
        name: 'PO Kontra Bon',
        url: '/po-kontra-bon',
        icon: null,
        code: 6040,
        name_code: 'page.po-kontra-bon',
        submenu: [],
        active: false
      },
      {
        name: 'Data Retur Coretax',
        url: '/data-retur-coretax',
        icon: null,
        code: 6050,
        name_code: 'page.data-retur-coretax',
        submenu: [],
        active: false
      },
      {
        name: 'Report Payment',
        url: '/report-payment',
        icon: null,
        code: 6060,
        name_code: 'page.report-payment',
        submenu: [],
        active: false
      },
      {
        name: 'Billing',
        url: '/billing',
        icon: null,
        code: 6070,
        name_code: 'page.billing',
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'DC Fee',
    url: null,
    icon: 'i-lucide-truck',
    code: 7000,
    name_code: 'menu.dc-fee',
    submenu: [
      {
        name: 'DC List',
        url: '/dc-list',
        icon: null,
        code: 7010,
        name_code: 'page.dc-list',
        submenu: [],
        active: false
      },
      {
        name: 'Handling Fee',
        url: '/handling-fee',
        icon: null,
        code: 7020,
        name_code: 'page.handling-fee',
        submenu: [],
        active: false
      },
      {
        name: 'Distribution Fee',
        url: '/distribution-fee',
        icon: null,
        code: 7030,
        name_code: 'page.distribution-fee',
        submenu: [],
        active: false
      },
      {
        name: 'DC Fee Supplier',
        url: '/dc-fee-supplier',
        icon: null,
        code: 7040,
        name_code: 'page.dc-fee-supplier',
        submenu: [],
        active: false
      },
      {
        name: 'Set Handling Method',
        url: '/set-handling-method',
        icon: null,
        code: 7050,
        name_code: 'page.set-handling-method',
        submenu: [],
        active: false
      },
      {
        name: 'DC Fee Validation',
        url: '/dc-fee-validation',
        icon: null,
        code: 7060,
        name_code: 'page.dc-fee-validation',
        submenu: [],
        active: false
      },
      {
        name: 'DC Fee By Receiving',
        url: '/dc-fee-by-receiving',
        icon: null,
        code: 7070,
        name_code: 'page.dc-fee-by-receiving',
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Tax Supplier Data',
    url: '/tax-supplier-data',
    icon: 'i-lucide-circle-dollar-sign',
    code: 8000,
    submenu: [],
    name_code: 'menu.tax-supplier-data',
    active: false
  },
])
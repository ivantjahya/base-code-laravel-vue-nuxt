<?php

namespace App\Interfaces;

interface MenuConst
{
    /**
     * List of parent menus
     */
    public const MENU_HOME = 0;

    public const MENU_MASTER_DATA = 1000;

    public const MENU_NEW_REGISTRATION = 2000;

    public const MENU_PURCHASE_ORDER = 3000;

    public const MENU_CONSIGNMENT = 4000;

    public const MENU_PRINCIPAL_REPORT = 5000;

    public const MENU_FINANCE = 6000;

    public const MENU_DC_FEE = 7000;

    public const MENU_TAX_SUPPLIER_DATA = 8000;

    /**
     * List of child menus
     */
    public const SUBMENU_LIMITS = 1010;

    public const SUBMENU_PROFILES = 1020;

    public const SUBMENU_FUNC_PROFILES = 1030;

    public const SUBMENU_USERS = 1040;

    public const SUBMENU_APPROVAL_FLOW = 1050;

    public const SUBMENU_REGIONAL_SITE = 1060;

    public const SUBMENU_USER_GUIDE = 1070;

    public const SUBMENU_ARTICLE = 2010;

    public const SUBMENU_SUPPLIER = 2020;

    public const SUBMENU_PO_STATUS_REPORT = 3010;

    public const SUBMENU_PO_LIST = 3020;

    public const SUBMENU_PO_CROSS_DOCK = 3030;

    public const SUBMENU_RETURN = 3040;

    public const SUBMENU_PENGAJUAN_RETUR = 4010;

    public const SUBMENU_PENGAJUAN_ACARA = 4020;

    public const SUBMENU_UPLOAD_BRAND_STORE = 4030;

    public const SUBMENU_SURAT_ACARA = 4040;

    public const SUBMENU_STOCK = 5010;

    public const SUBMENU_SALES = 5020;

    public const SUBMENU_FROZEN_FOR_PURCHASE = 5030;

    public const SUBMENU_MARKET_SHARE = 5040;

    public const SUBMENU_MERCHANDISE_STRUCTURE = 5050;

    public const SUBMENU_SELL_OUT_BY_MONTH = 5060;

    public const SUBMENU_SELL_OUT_BY_DIV_MONTH = 5070;

    public const SUBMENU_SELL_OUT_BY_DIV_BY_CAT_BY_BRAND = 5080;

    public const SUBMENU_50_TOP_SKU = 5090;

    public const SUBMENU_SELL_IN_BY_MONTH = 5100;

    public const SUBMENU_SELL_IN_BY_DIV_BY_MONTH = 5110;

    public const SUBMENU_SL_BY_MONTH = 5120;

    public const SUBMENU_SL_BY_DIV_BY_MONTH = 5130;

    public const SUBMENU_SL_DISTRIBUTOR_BY_DIV_BY_MONTH = 5140;

    public const SUBMENU_PAYMENT_CHECK = 6010;

    public const SUBMENU_INVOICE_INPUT_PUTUS = 6020;

    public const SUBMENU_INVOICE_CONSIGNMENT = 6030;

    public const SUBMENU_PO_KONTRABON = 6040;

    public const SUBMENU_DATA_RETUR_CORETAX = 6050;

    public const SUBMENU_REPORT_PAYMENT = 6060;

    public const SUBMENU_BILLING = 6070;

    public const SUBMENU_DC_LIST = 7010;

    public const SUBMENU_HANDLING_FEE = 7020;

    public const SUBMENU_DISTRIBUTION_FEE = 7030;

    public const SUBMENU_DC_FEE_SUPPLIER = 7040;

    public const SUBMENU_SET_HANDLING_METHOD = 7050;

    public const SUBMENU_DC_FEE_VALIDATION = 7060;

    public const SUBMENU_DC_FEE_BY_RECEIVING = 7070;
}

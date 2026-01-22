<?php

namespace App\Interfaces;

interface CacheKeyConst
{
    /**
     * List of cache key
     */
    public const KEY_MENUPERM = 'menu-perm';

    public const KEY_MENUCTRLPERM = 'menu-ctrl-perm';

    /**
     * List of cache tags
     */
    public const TAG_MENUPERM = 'mst-menu-perm';

    public const TAG_MENUCTRLPERM = 'mst-menu-ctrl-perm';

    /**
     * List of cache carbon time
     */
    /** 1 Week in seconds */
    public const CACHE_MST_TIME = 604800;

    /** 1 Day in seconds */
    public const CACHE_MID_TIME = 86400;

    /** 1 Hour in seconds */
    public const CACHE_SHORT_TIME = 3600;

    /** 1 Minute in seconds */
    public const CACHE_MINI_TIME = 60;

    /**
     * List of cache for open detail page
     */
    public const TAG_OPENDETAILPAGE = 'open-detail-page';
}

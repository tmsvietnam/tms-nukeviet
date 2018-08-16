<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  <contact@thuongmaiso.vn>
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 4/12/2010, 1:27
 */

if (! defined('NV_IS_MOD_DOWNLOAD')) {
    die('Stop!!!');
}

$url = array();
$cacheFile = NV_LANG_DATA . '_sitemap_' . NV_CACHE_PREFIX . '.cache';
$cacheTTL = 7200;

if (($cache = $nv_Cache->getItem($module_name, $cacheFile, $cacheTTL)) != false) {
    $url = unserialize($cache);
} else {
    $in = array_keys($list_cats);
    $in = implode(',', $in);

    $db->sqlreset()
        ->select('catid, alias, uploadtime')
        ->from(NV_MOD_TABLE)
        ->where('catid IN (' . $in . ') AND status=1')
        ->order('uploadtime DESC')
        ->limit(1000);
    $result = $db->query($db->sql());
    while (list($cid, $alias, $publtime) = $result->fetch(3)) {
        $url[] = array(
            'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$cid]['alias'] . '/' . $alias . $global_config['rewrite_exturl'], //
            'publtime' => $publtime
        );
    }

    $cache = serialize($url);
    $nv_Cache->setItem($module_name, $cacheFile, $cache, $cacheTTL);
}

nv_xmlSitemap_generate($url);
die();

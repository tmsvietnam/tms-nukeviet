<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  (contact@thuongmaiso.vn)
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */

if (!defined('NV_IS_MOD_NEWS')) {
    die('Stop!!!');
}

global $global_config, $module_name, $module_info, $module_file, $lang_module, $client_info;

if (file_exists(NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme'] . '/block_content.tpl')) {
    $block_theme = $module_info['template'];
} else {
    $block_theme = "default";
}
$xtpl = new XTemplate('block_content.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $module_file);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('TEMPLATE', $block_theme);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('LINK_CONTENT', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['content']);
$xtpl->assign('LINK_ADD_CONTENT', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['content'] . '&amp;contentid=0&amp;checkss=' . md5('0' . NV_CHECK_SESSION));

$xtpl->parse('main');
$content = $xtpl->text('main');

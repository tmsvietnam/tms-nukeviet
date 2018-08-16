<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  <contact@thuongmaiso.vn>
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 05/07/2010 09:47
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$module_version = array(
    'name' => 'Download',
    'modfuncs' => 'main,viewcat,viewfile,down,upload,report,search,tag',
    'change_alias' => 'upload, search',
    'submenu' => 'main,upload,search',
    'is_sysmod' => (defined('SYS_DOWNLOAD_TABLE')) ? 1 : 0,
    'virtual' => (defined('SYS_DOWNLOAD_TABLE')) ? 0 : 1,
    'version' => '4.3.00',
    'date' => 'Tue, 14 Nov 2017 03:46:48 GMT',
    'author' => 'VINADES <contact@thuongmaiso.vn>',
    'note' => '',
    'uploads_dir' => array(
        $module_upload,
        $module_upload . '/temp',
        $module_upload . '/import'
    )
);

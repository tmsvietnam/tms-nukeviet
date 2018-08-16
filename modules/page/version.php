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
    'name' => 'Page',
    'modfuncs' => 'main,rss',
    'is_sysmod' => 1,
    'virtual' => 1,
    'version' => '4.3.02',
    'date' => 'Wednesday, May 2, 2018 4:00:00 PM GMT+07:00',
    'author' => 'VINADES <contact@thuongmaiso.vn>',
    'note' => '',
    'uploads_dir' => array(
        $module_upload
    )
);

<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  <contact@thuongmaiso.vn>
 * @Copyright (C) 2017 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (! defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}
if (! defined('NV_IS_AJAX')) {
    die('Wrong URL');
}

$parentid = $nv_Request->get_int('parentid', 'get', 0);

$contents = shops_show_group_list($parentid);

include NV_ROOTDIR . '/includes/header.php';
echo $contents;
include NV_ROOTDIR . '/includes/footer.php';

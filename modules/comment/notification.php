<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES (contact@thuongmaiso.vn)
 * @Copyright (C) 2014 VINADES. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Apr 20, 2010 10:47:41 AM
 */

if (! defined('NV_IS_FILE_SITEINFO')) {
    die('Stop!!!');
}

$lang_siteinfo = nv_get_lang_module($mod);

$data['title'] = sprintf($lang_siteinfo['notification_comment_queue'], $data['send_from'], nv_clean60($data['content']['content'], 120));
$data['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $data['module'] . '&amp;' . NV_OP_VARIABLE . '=edit&amp;cid=' . $data['obid'];

<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  (contact@thuongmaiso.vn)
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

if (!defined('NV_SYSTEM')) {
    die('Stop!!!');
}

define('NV_IS_MOD_PAGE', true);

// Get Config Module
$sql = 'SELECT config_name, config_value FROM ' . NV_PREFIXLANG . '_' . $module_data . '_config';
$list = $nv_Cache->db($sql, '', $module_name);
$page_config = array();
foreach ($list as $values) {
    $page_config[$values['config_name']] = $values['config_value'];
}

if ($page_config['viewtype'] != 2) {
    $id = 0;
    $page = 1;
    $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;

	//THÊM
	if($link_true)
    $alias = (!empty($array_op) and !empty($array_op[0])) ? $array_op[0] : '';
    if (substr($alias, 0, 5) == 'page-') {
        $page = intval(substr($array_op[0], 5));
        $id = 0;
        $alias = '';
    } elseif (empty($alias) and empty($page_config['viewtype'])) {
        $db_slave->sqlreset()
            ->select('*')
            ->from(NV_PREFIXLANG . '_' . $module_data)
            ->where('status=1')
            ->order('weight ASC')
            ->limit(1);
        $rowdetail = $db_slave->query($db_slave->sql())
            ->fetch();
        if (!empty($rowdetail)) {
            $id = $rowdetail['id'];
        }
    } elseif (!empty($alias)) {
        $sth = $db_slave->prepare('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE alias=:alias');
        $sth->bindParam(':alias', $alias, PDO::PARAM_STR);
        $sth->execute();
        $rowdetail = $sth->fetch();
        if (empty($rowdetail)) {
            nv_redirect_location($base_url);
        }
        $id = $rowdetail['id'];
    }
}

<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  (contact@thuongmaiso.vn)
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 03-05-2010
 */

if (! defined('NV_IS_MOD_SEARCH')) {
    die('Stop!!!');
}

$where = '';

if($id_alias > 0)
{
	$where = " AND keywords like '%". $id_alias."%' ";
}
else
{
	$where = ' AND (' . nv_like_logic('title', $dbkeyword, $logic) . ' OR ' . nv_like_logic('description', $dbkeyword, $logic) . ' OR ' . nv_like_logic('bodytext', $dbkeyword, $logic) . ')';
}

$db_slave->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $m_values['module_data'])
    ->where('status=1 '. $where);
$num_items = $db_slave->query($db_slave->sql())->fetchColumn();

if ($num_items) {
    $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=';

    $db_slave->select('id,title, alias, description, bodytext')
        ->limit($limit)
        ->offset(($page - 1) * $limit);
    $result = $db_slave->query($db_slave->sql());
    while (list($id, $tilterow, $alias, $description, $content) = $result->fetch(3)) {
        $result_array[] = array(
            'link' => $link . $alias,
            'title' => BoldKeywordInStr($tilterow, $key, $logic),
            'content' => BoldKeywordInStr($description . ' ' . $content, $key, $logic)
        );
    }
}

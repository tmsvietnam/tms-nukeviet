<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  <contact@thuongmaiso.vn>
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-9-2010 14:43
 */

if (! defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Check file error
if ($nv_Request->isset_request('linkcheck', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $id = $nv_Request->get_int('id', 'post', 0);

    $sql = 'SELECT id, linkdirect FROM ' . NV_MOD_TABLE . '_detail WHERE id=' . $id;
    list($_id, $linkdirect) = $db->query($sql)->fetch(3);

    if (empty($_id)) {
        die('BAD_' . $id);
    }

    $links = array();

    if (! empty($linkdirect)) {
        $linkdirect = explode('[NV]', $linkdirect);
        $linkdirect = array_map('trim', $linkdirect);
        foreach ($linkdirect as $ls) {
            if (! empty($ls)) {
                $ls = explode('<br />', $ls);
                $ls = array_map('trim', $ls);

                foreach ($ls as $l) {
                    if (! empty($l)) {
                        $links[] = $l;
                    }
                }
            }
        }
    }

    $sql = 'SELECT file_path FROM ' . NV_MOD_TABLE . '_files WHERE download_id=' . $id;
    $fileupload = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    
    if (! empty($fileupload)) {
        foreach ($fileupload as $file) {
            if (! empty($file)) {
                $links[] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . $file;
            }
        }
    }

    if (! empty($links)) {
        foreach ($links as $link) {
            if (! nv_is_url($link)) {
                die('NO_' . $id);
            }
            if (! nv_check_url($link)) {
                die('NO_' . $id);
            }
        }
    }

    nv_htmlOutput('OK_' . $id);
}

//Del
if ($nv_Request->isset_request('del', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $id = $nv_Request->get_int('id', 'post', 0);

    $query = 'SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_report WHERE fid=' . $id;
    $numrows = $db->query($query)->fetchColumn();
    if ($numrows != 1) {
        die('NO');
    }

    $db->query('DELETE FROM ' . NV_MOD_TABLE . '_report WHERE fid=' . $id);
    nv_status_notification(NV_LANG_DATA, $module_name, 'report', $id);

    nv_htmlOutput('OK');
}

//All del
if ($nv_Request->isset_request('alldel', 'post')) {
    $query = $db->query('SELECT fid FROM ' . NV_MOD_TABLE . '_report');
    while (list($fid) = $query->fetch(3)) {
        nv_status_notification(NV_LANG_DATA, $module_name, 'report', $fid);
    }
    $db->query('DELETE FROM ' . NV_MOD_TABLE . '_report');
    nv_htmlOutput('OK');
}

//List
$page_title = $lang_module['download_report'];

$sql = 'SELECT a.post_time AS post_time, a.post_ip AS post_ip, b.id AS id, b.title AS title, b.catid AS catid FROM ' . NV_MOD_TABLE . '_report a INNER JOIN ' . NV_MOD_TABLE . ' b ON a.fid=b.id ORDER BY a.post_time DESC';
$_array_report = $db->query($sql)->fetchAll();
$num = sizeof($_array_report);
if (! $num) {
    $contents = "<div style=\"padding-top:15px;text-align:center\">\n";
    $contents .= "<strong>" . $lang_module['report_empty'] . "</strong>";
    $contents .= "</div>\n";
    $contents .= "<meta http-equiv=\"refresh\" content=\"2;url=" . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "\" />";

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

if (empty($list_cats)) {
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat-content');
}

$array = array();
foreach ($_array_report as $row) {
    $array[$row['id']] = array(
        'id' => ( int )$row['id'],
        'title' => $row['title'],
        'cattitle' => $list_cats[$row['catid']]['title'],
        'catlink' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;catid=' . $row['catid'],
        'post_time' => nv_date('d/m/Y H:i', $row['post_time']),
        'post_ip' => $row['post_ip']
    );
}

$xtpl = new XTemplate('report.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('TABLE_CAPTION', $page_title);

if (! empty($array)) {
    foreach ($array as $row) {
        $xtpl->assign('ROW', $row);
        $xtpl->assign('EDIT_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $row['id']);
        $xtpl->parse('main.row');
    }
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';

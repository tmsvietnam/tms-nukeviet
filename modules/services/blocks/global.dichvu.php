<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  <contact@thuongmaiso.vn>
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/25/2010 18:6
 */

if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (!nv_function_exists('nv_dichvu')) {
    /**
     * nv_block_config_dichvu()
     *
     * @param mixed $module
     * @param mixed $data_block
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_dichvu($module, $data_block, $lang_block)
    {
        $html = '';
        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['title_length'] . ':</label>';
        $html .= '	<div class="col-sm-9"><input type="text" class="form-control" name="config_title_length" value="' . $data_block['title_length'] . '"/></div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '	<label class="control-label col-sm-6">' . $lang_block['numrow'] . ':</label>';
        $html .= '	<div class="col-sm-9"><input type="text" name="config_numrow" class="form-control" value="' . $data_block['numrow'] . '"/></div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * nv_block_config_dichvu_submit()
     *
     * @param mixed $module
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_dichvu_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['title_length'] = $nv_Request->get_int('config_title_length', 'post', 24);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 5);
        return $return;
    }

    /**
     * nv_dichvu()
     *
     * @param mixed $block_config
     * @return
     */
    function nv_dichvu($block_config)
    {
        global $nv_Cache, $global_config, $site_mods, $db;
        $module = $block_config['module'];

        if (!isset($site_mods[$module])) {
            return '';
        }

        $db->sqlreset()->select('id, title, alias,image, description')->from(NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'])->where('status = 1')->order('weight ASC')->limit($block_config['numrow']);

        $list = $nv_Cache->db($db->sql(), 'id', $module);

        if (!empty($list)) {
            if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/services/block.dichvu.tpl')) {
                $block_theme = $global_config['module_theme'];
            } elseif (file_exists(NV_ROOTDIR . '/themes/' . $global_config['site_theme'] . '/modules/services/block.dichvu.tpl')) {
                $block_theme = $global_config['site_theme'];
            } else {
                $block_theme = 'default';
            }

            $xtpl = new XTemplate('block.dichvu.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/services');

            foreach ($list as $l) {
                $l['title_clean60'] = nv_clean60($l['title'], $block_config['title_length']);
				$l['hometext'] = nv_clean60($l['description'], 120);
                $l['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'] . $global_config['rewrite_exturl'];
				$l['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $site_mods[$module]['module_upload'] . '/' . $l['image'];
                        
                $xtpl->assign('ROW', $l);
                $xtpl->parse('main.loop');
            }

            $xtpl->parse('main');
            return $xtpl->text('main');
        } else {
            return '';
        }
    }
}

if (defined('NV_SYSTEM')) {
    $content = nv_dichvu($block_config);
}

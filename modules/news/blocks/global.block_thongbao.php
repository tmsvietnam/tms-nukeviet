<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (! nv_function_exists('nv_block_thongbao')) {
    function nv_block_config_thongbao($module, $data_block, $lang_block)
    {
        global $nv_Cache, $site_mods;

        $html_input = '';
        $html = '';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['blockid'] . '</td>';
        $html .= '<td><select name="config_blockid" class="form-control w200">';
        $html .= '<option value="0"> -- </option>';
        $sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block_cat ORDER BY weight ASC';
        $list = $nv_Cache->db($sql, '', $module);
        foreach ($list as $l) {
            $html_input .= '<input type="hidden" id="config_blockid_' . $l['bid'] . '" value="' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module]['alias']['groups'] . '/' . $l['alias'] . '" />';
            $html .= '<option value="' . $l['bid'] . '" ' . (($data_block['blockid'] == $l['bid']) ? ' selected="selected"' : '') . '>' . $l['title'] . '</option>';
        }
        $html .= '</select>';
        $html .= $html_input;
        $html .= '<script type="text/javascript">';
        $html .= '	$("select[name=config_blockid]").change(function() {';
        $html .= '		$("input[name=title]").val($("select[name=config_blockid] option:selected").text());';
        $html .= '		$("input[name=link]").val($("#config_blockid_" + $("select[name=config_blockid]").val()).val());';
        $html .= '	});';
        $html .= '</script>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['title_length'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_title_length" size="5" value="' . $data_block['title_length'] . '"/></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . $lang_block['numrow'] . '</td>';
        $html .= '<td><input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/></td>';
        $html .= '</tr>';
     $html .= "<tr>";
		$html .= "<td>". $lang_block['direction'] ."</td>";
		$html .= "<td>";
		$sorting_array = array( 'bn1' => $lang_block['bn1'], 'bn2' => $lang_block['bn2'], 'bn3' => $lang_block['bn3'], 'bn4' => $lang_block['bn4'], 'bn5' => $lang_block['bn5'], 'bn6' => $lang_block['bn6'], 'bn7' => $lang_block['bn7'], 'bn8' => $lang_block['bn8'], 'bn9' => $lang_block['bn9'], 'bn10' => $lang_block['bn10']);
		$html .= '<select name="config_direction">';
		foreach( $sorting_array as $key => $value )
		{
			$html .= '<option value="' . $key . '" ' . ( $data_block['direction'] == $key ? 'selected="selected"' : '') . '>' . $value . '</option>';
		}
		$html .= '</select>';
		$html .= "</td";
		$html .= "	</tr>";
        return $html;
    }

    function nv_block_config_thongbao_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['blockid'] = $nv_Request->get_int('config_blockid', 'post', 0);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['title_length'] = $nv_Request->get_int('config_title_length', 'post', 20);
		$return['config']['direction'] = $nv_Request->get_title('config_direction', 'post', 0);
        return $return;
    }

    function nv_block_thongbao($block_config)
    {
        global $module_array_cat, $site_mods, $module_config, $global_config, $nv_Cache, $db;
        $module = $block_config['module'];
    
	
			

		
		
        $db->sqlreset()
            ->select('t1.id, t1.catid, t1.title, t1.alias, t1.homeimgfile, t1.homeimgthumb,t1.hometext,t1.publtime,t1.external_link')
            ->from(NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows t1')
            ->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block t2 ON t1.id = t2.id')
            ->where('t2.bid= ' . $block_config['blockid'] . ' AND t1.status= 1')
            ->order('t2.weight ASC')
            ->limit($block_config['numrow']);
        $list = $nv_Cache->db($db->sql(), '', $module);

        if (! empty($list)) {
            if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/news/block_thongbao.tpl')) {
                $block_theme = $global_config['module_theme'];
            } else {
                $block_theme = 'default';
            }
            $xtpl = new XTemplate('block_thongbao.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/news');
            $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
            $xtpl->assign('TEMPLATE', $block_theme);
			$xtpl->assign('NV_CURRENTTIME', nv_date($global_config['date_pattern']) );
			
if ($block_config['direction']=='bn1') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn1").breakingNews({effect:"slide-h",direction:bn1,autoplay:true,timer:3000,color:"red"});	');}
if ($block_config['direction']=='bn2') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn2").breakingNews({effect:"slide-h",direction:bn2,autoplay:true,timer:3000,color:"yellow"});	');}
if ($block_config['direction']=='bn3') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn3").breakingNews({effect:"slide-v",direction:bn3,autoplay:true,timer:3000,color:"turquoise",border:true});	');}
if ($block_config['direction']=='bn4') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn4").breakingNews({effect:"slide-v",direction:bn4,autoplay:true,timer:3000,color:"green",border:true});	');}
if ($block_config['direction']=='bn5') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn5").breakingNews({effect:"slide-v",direction:bn5,color:"orange"});');}
if ($block_config['direction']=='bn6') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn6").breakingNews({effect:"slide-h",direction:bn6,autoplay:true,timer:3000,color:"purple"});	');}
if ($block_config['direction']=='bn7') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn7").breakingNews({effect:"slide-v",direction:bn7,autoplay:true,timer:3000,color:"darkred"});	');}
if ($block_config['direction']=='bn8') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn8").breakingNews({effect:"slide-v",direction:bn8,color:"black"});	');}
if ($block_config['direction']=='bn9') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn9").breakingNews({effect:"slide-v",direction:bn9,autoplay:true,timer:3000,color:"light"});	');}
if ($block_config['direction']=='bn10') {$xtpl->assign('direction', $block_config['direction']);$xtpl->assign('script', '$("#bn10").breakingNews({effect:"slide-h",direction:bn10,autoplay:true,timer:3000,color:"pink"});	');}
			
			
			
            foreach ($list as $l) {
                $l['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$l['catid']]['alias'] . '/' . $l['alias'] . '-' . $l['id'] . $global_config['rewrite_exturl'];
          

                $l['title_clean'] = nv_clean60($l['title'], $block_config['title_length']);

                if ($l['external_link']) {
                    $l['target_blank'] = 'target="_blank"';
                }

                $xtpl->assign('ROW', $l);
            
                $xtpl->parse('main.loop');
            }

       

            $xtpl->parse('main');
            return $xtpl->text('main');
        }
    }
}
if (defined('NV_SYSTEM')) {
    global $site_mods, $module_name, $global_array_cat, $module_array_cat, $nv_Cache, $db;
    $module = $block_config['module'];
    if (isset($site_mods[$module])) {
        if ($module == $module_name) {
            $module_array_cat = $global_array_cat;
            unset($module_array_cat[0]);
        } else {
            $module_array_cat = array();
            $sql = 'SELECT catid, parentid, title, alias, viewcat, subcatid, numlinks, description, inhome, keywords, groups_view FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat ORDER BY sort ASC';
            $list = $nv_Cache->db($sql, 'catid', $module);
            if(!empty($list))
            {
                foreach ($list as $l) {
                    $module_array_cat[$l['catid']] = $l;
                    $module_array_cat[$l['catid']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
                }
            }
        }
        $content = nv_block_thongbao($block_config);
    }
}

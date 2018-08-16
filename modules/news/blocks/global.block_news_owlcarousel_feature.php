<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (! nv_function_exists('nv_block_news_owlcarousel_feature')) {
    function nv_block_config_news_owlcarousel_feature($module, $data_block, $lang_block)
    {
        global $nv_Cache, $site_mods;
		
        $html = '<div class="form-group">';			
		$html .= '<label class="control-label col-sm-6">' . $lang_block['blockid'] . '</label>';
		$html .= '<div class="col-sm-18"><select name="config_blockid" class="form-control w200">';
				$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block_cat ORDER BY weight ASC';
				$list = $nv_Cache->db( $sql, '', $module );
				foreach( $list as $l ){
					$html .= '<option value="' . $l['bid'] . '" ' . ( ( $data_block['blockid'] == $l['bid'] ) ? ' selected="selected"' : '' ) . '>' . $l['title'] . '</option>';
				}
		$html .= '</select></div>';		
		$html .= '</div>';
		
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">' . $lang_block['animatein'] . '</label>';
		$html .= '<div class="col-sm-18"><select name="config_animatein" class="w200 form-control">';
		$animin = array(
			'none',
			'bounceIn',
			'bounceInDown',
			'bounceInLeft',
			'bounceInRight',
			'bounceInUp',
			'fadeIn',
			'fadeInDown',
			'fadeInDownBig',
			'fadeInLeft',
			'fadeInLeftBig',
			'fadeInRight',
			'fadeInRightBig',
			'fadeInUp',
			'fadeInUpBig',
			'flipInX',
			'flipInY',
			'lightSpeedIn',
			'rotateIn',
			'rotateInDownLeft',
			'rotateInDownRight',
			'rotateInUpLeft',
			'rotateInUpRight',
			'slideInUp',
			'slideInDown',
			'slideInLeft',
			'slideInRight',
			'zoomIn',
			'zoomInDown',
			'zoomInLeft',
			'zoomInRight',
			'zoomInUp',
			'rollIn' );
		foreach( $animin as $langin ){
			$sel = ( $data_block['animatein'] == $langin ) ? ' selected' : '';
			$html .= '<option value="' . $langin . '" ' . $sel . '>' . $langin . '</option>';}
		$html .= '	</select></div>';
		$html .= '</div>';
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label col-sm-6">' . $lang_block['animateout'] . '</label>';
		$html .= '<div class="col-sm-18"><select name="config_animateout" class="w200 form-control">';
		$animout = array(
			'none',
			'bounceOut',
			'bounceOutDown',
			'bounceOutLeft',
			'bounceOutRight',
			'bounceOutUp',
			'fadeOut',
			'fadeOutDown',
			'fadeOutDownBig',
			'fadeOutLeft',
			'fadeOutLeftBig',
			'fadeOutRight',
			'fadeOutRightBig',
			'fadeOutUp',
			'fadeOutUpBig',
			'flipOutX',
			'flipOutY',
			'lightSpeedOut',
			'rotateOut',
			'rotateOutDownLeft',
			'rotateOutDownRight',
			'rotateOutUpLeft',
			'rotateOutUpRight',
			'slideOutUp',
			'slideOutDown',
			'slideOutLeft',
			'slideOutRight',
			'zoomOut',
			'zoomOutDown',
			'zoomOutLeft',
			'zoomOutRight',
			'zoomOutUp',
			'rollOut' );
		foreach( $animout as $langout )
		{
			$sel = ( $data_block['animateout'] == $langout ) ? ' selected' : '';
			$html .= '<option value="' . $langout . '" ' . $sel . '>' . $langout . '</option>';
		}
		$html .= '</select></div>';
		$html .= '</div>';		
		$html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_block['title_length_left'] . '</label>';
        $html .= '<div class="col-sm-18"><input type="text" class="form-control w200" name="config_title_length_left" size="5" value="' . $data_block['title_length_left'] . '"/></div>';
        $html .= '</div>';
		$html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_block['title_length_right'] . '</label>';
        $html .= '<div class="col-sm-18"><input type="text" class="form-control w200" name="config_title_length_right" size="5" value="' . $data_block['title_length_right'] . '"/></div>';
        $html .= '</div>';		
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label col-sm-6">' . $lang_block['numrow'] . '</label>';
        $html .= '<div class="col-sm-18"><input type="text" class="form-control w200" name="config_numrow" size="5" value="' . $data_block['numrow'] . '"/></div>';
        $html .= '</div>';
		
        return $html;
    }

    function nv_block_config_news_owlcarousel_feature_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['blockid'] = $nv_Request->get_int( 'config_blockid', 'post', 0 );	
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 0);
        $return['config']['title_length_left'] = $nv_Request->get_int('config_title_length_left', 'post', 60);	
        $return['config']['title_length_right'] = $nv_Request->get_int('config_title_length_right', 'post', 24);
		$return['config']['animatein'] = $nv_Request->get_title('config_animatein', 'post', '');	
		$return['config']['animateout'] = $nv_Request->get_title('config_animateout', 'post', '');	

        return $return;
    }

    function nv_block_news_owlcarousel_feature($block_config)
    {
        global $nv_Cache, $module_array_cat, $site_mods, $module_config, $global_config, $db;
        $module = $block_config['module'];

		$db->sqlreset()
			->select('t1.id, t1.catid, t1.title, t1.alias, t1.homeimgfile, t1.homeimgthumb, t1.hometext, t1.publtime, t1.hitstotal, t1.author, t1.external_link')
			->from(NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows t1')
			->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block t2 ON t1.id = t2.id')
			->where('t2.bid= ' . $block_config['blockid'] . ' AND t1.status= 1')
			->order('publtime DESC')
            ->limit($block_config['numrow']);
		
		$list = $nv_Cache->db($db->sql(), '', $module);

			if (! empty($list)) {
				if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme']  . '/modules/news/block_news_owlcarousel_feature.tpl')) {
					$block_theme = $global_config['module_theme'] ;
				}

				$xtpl = new XTemplate('block_news_owlcarousel_feature.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/news');
				$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
				$xtpl->assign('TEMPLATE', $block_theme);		
				$xtpl->assign('ANIMATEIN', $block_config['animatein']);			
				$xtpl->assign('ANIMATEOUT', $block_config['animateout']);
				
				foreach ($list as $l) {
					
					$db->sqlreset()
						->select('catid, title, alias')
						->from(NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat')
						->where('catid =' . $l['catid']);
					$list2 = $nv_Cache->db($db->sql(), '', $module);
					foreach ($list2 as $l2) {	
					$xtpl->assign('CAT_TITLE', $l2['title']);	
					$catlink = NV_BASE_SITEURL . NV_LANG_DATA . '/' . $module . '/' . $l2['alias'];
					$xtpl->assign('CAT_LINK', $catlink);	
					}					
					
					$l['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$l['catid']]['alias'] . '/' . $l['alias'] . '-' . $l['id'] . $global_config['rewrite_exturl'];
					if (($l['homeimgthumb'] == 1) or ($l['homeimgthumb'] == 2)){
						$l['thumb'] = NV_BASE_SITEURL . 'uploads/' . $module . '/' . $l['homeimgfile'];
					} elseif ($l['homeimgthumb'] == 3) {
						$l['thumb'] = $l['homeimgfile'];
					} elseif (! empty($show_no_image)) {
						$l['thumb'] = NV_BASE_SITEURL . $show_no_image;
					} else {
						$l['thumb'] = '';
					}
					
					$l['publtime'] = nv_date('d/m/Y', $l['publtime']);
					$l['hometext_clean'] = strip_tags($l['hometext']);

					$l['title_clean'] = nv_clean60($l['title'], $block_config['title_length_left']);
					if ($l['external_link']) {
						$l['target_blank'] = 'target="_blank"';
					}
					
					if (!empty($l['author'])) {
						$xtpl->assign('author', $l['author']);
						$xtpl->parse('main.left.author');
					}
					
					$xtpl->assign('LEFT', $l);
					if (! empty($l['thumb'])) {
						$xtpl->parse('main.left.img');
					}
					$xtpl->parse('main.left');		
				}
				
			}

			$db->sqlreset()
				->select('t1.id, t1.catid, t1.title, t1.alias, t1.homeimgfile, t1.homeimgthumb, t1.hometext, t1.publtime, t1.hitstotal, t1.author, t1.external_link')
				->from(NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_rows t1')
				->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_block t2 ON t1.id = t2.id')
				->where('t2.bid= ' . $block_config['blockid'] . ' AND t1.status= 1')
				->order('publtime DESC')
				->limit(4)->offset($block_config['numrow']);
				
			$list1 = $nv_Cache->db($db->sql(), '', $module);

			if (! empty($list1)) {
				foreach ($list1 as $l1) {
					$l1['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $module_array_cat[$l1['catid']]['alias'] . '/' . $l1['alias'] . '-' . $l1['id'] . $global_config['rewrite_exturl'];
					if (($l1['homeimgthumb'] == 1) or ($l['homeimgthumb'] == 2)){
						$l1['thumb'] = NV_BASE_SITEURL . 'uploads/' . $site_mods[$module]['module_upload'] . '/' . $l1['homeimgfile'];
					} elseif ($l['homeimgthumb'] == 3) {
						$l1['thumb'] = $l1['homeimgfile'];
					} elseif (! empty($show_no_image)) {
						$l1['thumb'] = NV_BASE_SITEURL . $show_no_image;
					} else {
						$l1['thumb'] = '';
					}
					
					$l1['publtime'] = nv_date('d/m/Y', $l1['publtime']);

					$l1['title_clean'] = nv_clean60($l1['title'], $block_config['title_length_right']);
					if ($l1['external_link']) {
						$l1['target_blank'] = 'target="_blank"';
					}

					$xtpl->assign('RIGHT', $l1);
					if (! empty($l1['thumb'])) {
						$xtpl->parse('main.right.img');
					}
					$xtpl->parse('main.right');
				}

			}

	    $xtpl->parse('main');
        return $xtpl->text('main');			
	}
}
if (defined('NV_SYSTEM')) {
    global $nv_Cache, $site_mods, $module_name, $global_array_cat, $module_array_cat;
    $module = $block_config['module'];
    if (isset($site_mods[$module])) {
        if ($module == $module_name) {
            $module_array_cat = $global_array_cat;
            unset($module_array_cat[0]);
        } else {
            $module_array_cat = array();
            $sql = 'SELECT catid, parentid, title, alias, viewcat, subcatid, numlinks, description, keywords, groups_view FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_cat ORDER BY sort ASC';
            $list = $nv_Cache->db($sql, 'catid', $module);
            if(!empty($list))
            {
                foreach ($list as $l) {
                    $module_array_cat[$l['catid']] = $l;
                    $module_array_cat[$l['catid']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $l['alias'];
                }
            }
        }
        $content = nv_block_news_owlcarousel_feature($block_config);
    }
}

<?php
/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  (contact@thuongmaiso.vn)
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

define( 'NV_IS_FILE_ADMIN', true );


$allow_func = array( 'main', 'config','content','alias','change_status','change_weight','del','view','tags');


function add_alias($id_alias, $alias, $module, $op_module)
{
	global $db_config, $module_data;
	if(!empty($alias) and !empty($module) and !empty($op_module))
	{
		$count = 0; 
		//$module_data = 'alias';
		if($id_alias > 0)
		{
			$count = $db->query("SELECT count(id) FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE id != ". $id_alias . " AND alias like '". $alias ."'")->fetchColumn();
			
			if($count == 0)
			{
				// UPDATE alias 
				$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_rows SET id=' . $id_alias . ' WHERE alias=' . $alias);
				return true;
			}
		
		}
		else
		{
			$count = $db->query("SELECT count(id) FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE alias like '". $alias ."'")->fetchColumn();
			
			if($count == 0)
			{
				// ADD alias 
				$stmt = $db->prepare( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_rows (alias, module, op, weight, status) VALUES (:alias, :module, :op, :weight, :status)' );

				$stmt->bindValue( ':status', 1, PDO::PARAM_INT );

				$weight = $db->query( 'SELECT max(status) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_rows' )->fetchColumn();
				$weight = intval( $weight ) + 1;
				$stmt->bindParam( ':weight', $weight, PDO::PARAM_INT );
				$stmt->bindParam( ':alias', $alias, PDO::PARAM_STR );
				$stmt->bindParam( ':module', $module, PDO::PARAM_STR );
				$stmt->bindParam( ':op', $op_module, PDO::PARAM_STR );

				$exc = $stmt->execute();
				if( $exc )
				{
					return true;
				}
			}
			
		}
		
		
		
	}
	
	return false;
}

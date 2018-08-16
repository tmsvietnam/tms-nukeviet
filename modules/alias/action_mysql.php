<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY COMPANY LIMITED (contact@thuongmaiso.vn)
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY COMPANY LIMITED. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

if (! defined('NV_IS_FILE_MODULES')) {
    die('Stop!!!');
}

$sql_drop_module = array();

$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows;";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config;";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . ";";

$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . " (
 id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
 title varchar(250) NOT NULL,
 alias varchar(250) NOT NULL,
 image varchar(255) DEFAULT '',
 imagealt varchar(255) DEFAULT '',
 imageposition tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
 description text,
 bodytext mediumtext NOT NULL,
 titlesite text,
 hometext text,
 ratename text,
 ratenumber text,
 keywords text,
 socialbutton tinyint(4) NOT NULL DEFAULT '0',
 activecomm varchar(255) DEFAULT '',
 layout_func varchar(100) DEFAULT '',
 gid mediumint(9) NOT NULL DEFAULT '0',
 weight smallint(4) NOT NULL DEFAULT '0',
 admin_id mediumint(8) unsigned NOT NULL DEFAULT '0',
 add_time int(11) NOT NULL DEFAULT '0',
 edit_time int(11) NOT NULL DEFAULT '0',
 status tinyint(1) unsigned NOT NULL DEFAULT '0',
 hitstotal MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
 hot_post TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY (id),
 UNIQUE KEY alias (alias)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows (
 id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
 alias varchar(250) NOT NULL,
 module varchar(50) NOT NULL,
 op varchar(50) NOT NULL,
 id_alias mediumint(8) DEFAULT '0',
 catid_alias mediumint(8) DEFAULT '0',
 weight smallint(4) NOT NULL DEFAULT '0',
 status tinyint(1) unsigned NOT NULL DEFAULT '1',
 PRIMARY KEY (id),
 UNIQUE KEY alias (alias)
) ENGINE=MyISAM";


$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config (
 config_name varchar(30) NOT NULL,
 config_value varchar(255) NOT NULL,
 UNIQUE KEY config_name (config_name)
)ENGINE=MyISAM";

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config VALUES
('viewtype', '0'),
('facebookapi', ''),
('per_page', '20'),
('news_first', '0'),
('rate_page', '0'),
('copy_page', '0'),
('alias_lower', 1)
";


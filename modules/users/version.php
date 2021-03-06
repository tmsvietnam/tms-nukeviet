<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  <contact@thuongmaiso.vn>
 * @Copyright (C) 2010 - 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 08 Apr 2012 00:00:00 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$module_version = array(
    'name' => 'Users',
    'modfuncs' => 'main,login,logout,register,lostpass,active,editinfo,avatar,lostactivelink,memberlist,groups',
    'submenu' => 'main,login,logout,register,lostpass,active,editinfo,lostactivelink,memberlist',
    'is_sysmod' => 1,
    'virtual' => 1,
    'version' => '4.3.02',
    'date' => 'Wednesday, May 2, 2018 4:00:00 PM GMT+07:00',
    'author' => 'VINADES <contact@thuongmaiso.vn>',
    'note' => ''
);

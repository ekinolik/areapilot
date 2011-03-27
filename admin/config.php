<?php

define('ADMIN_CONFIG', 1);
if ( ! defined('CONFIG')) require('../config.php');
if ( ! defined('CONFIG_SETTINGS'))	require('../config_settings.php');

$CURRENT_DIR = ROOT_DIR.'admin/';
$CURRENT_URL = ROOT_URL.'admin/';
define('ADMIN_DIR', $CURRENT_DIR);
define('ADMIN_URL', $CURRENT_URL);

if ( ! defined('LOCATION') && ! defined('LOCATIONCLASS'))
   require('lib/Location.php');
?>

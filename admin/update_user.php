<?php

if ( ! defined('config')) require('config.php');

$TITLE = 'Update User';

$CODE = $CURRENT_DIR.'code/update_user.php';
$OUTPUT = $CURRENT_DIR.'html_output/update_user.php';
$HEADER = ADMIN_DIR.'xml_output/header.php';
$FOOTER = ADMIN_DIR.'xml_output/footer.php';
define('NOLOCOK', 1);

require(ADMIN_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

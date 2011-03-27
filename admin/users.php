<?php

if ( ! defined('config')) require('config.php');

$TITLE = 'Users';

$CODE = $CURRENT_DIR.'code/users.php';
$OUTPUT = $CURRENT_DIR.'html_output/users.php';
$HEADER = ADMIN_DIR.'html_output/header.php';
$FOOTER = ADMIN_DIR.'html_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, 'basics.js');
array_push($JS, 'users.js');
array_push($CSS, '');

require(ADMIN_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

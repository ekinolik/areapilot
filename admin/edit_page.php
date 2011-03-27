<?php

if ( ! defined('config')) require('config.php');

$TITLE = 'Edit Page';

$CODE = $CURRENT_DIR.'code/edit_page.php';
$OUTPUT = $CURRENT_DIR.'html_output/edit_page.php';
$HEADER = ADMIN_DIR.'html_output/header.php';
$FOOTER = ADMIN_DIR.'html_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, 'basics.js');
array_push($CSS, '');

//if ( ! defined('SQL_CONNECT')) require(LIB_DIR.'sql_connect.php');

require(ADMIN_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

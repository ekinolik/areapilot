<?php

if ( ! defined('config')) require('config.php');

$TITLE = 'Upload Forms';

$CODE = $CURRENT_DIR.'code/upload_forms.php';
$OUTPUT = $CURRENT_DIR.'html_output/upload_forms.php';
$HEADER = ROOT_DIR.'html_output/header.php';
$FOOTER = ROOT_DIR.'html_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, 'blockUI.js');
array_push($JS, 'rounded.js');
array_push($JS, 'basics.js');
array_push($JS, 'upload_form.js');
array_push($CSS, 'admin.css');

//if ( ! defined('SQL_CONNECT')) require(LIB_DIR.'sql_connect.php');

require(ROOT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

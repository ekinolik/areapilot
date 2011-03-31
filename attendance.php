<?php

if ( ! defined('config')) require('config.php');

$TITLE .= ' - Attendance';

$CODE = $CURRENT_DIR.'code/attendance.php';
$OUTPUT = $CURRENT_DIR.'json_output/attendance.php';
$HEADER = ROOT_DIR.'json_output/header.php';
$FOOTER = ROOT_DIR.'json_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, '');
array_push($CSS, '');

require($CURRENT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

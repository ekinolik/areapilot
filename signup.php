<?php

if ( ! defined('config')) require('config.php');

$TITLE .= ' - Signup';

$CODE = $CURRENT_DIR.'code/signup.php';
$OUTPUT = $CURRENT_DIR.'html_output/signup.php';
$HEADER = ROOT_DIR.'html_output/header.php';
$FOOTER = ROOT_DIR.'html_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, '');
array_push($CSS, '');

require($CURRENT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

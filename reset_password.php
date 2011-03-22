<?php

if ( ! defined('config')) require('config.php');

$TITLE .= ' - Reset Password';

$CODE = $CURRENT_DIR.'code/reset_password.php';
$OUTPUT = $CURRENT_DIR.'html_output/forgot_password.php';
$HEADER = ROOT_DIR.'html_output/header.php';
$FOOTER = ROOT_DIR.'html_output/footer.php';
//define('NOLOCOK', 1);

array_push($JS, '');
array_push($CSS, '');

require($CURRENT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

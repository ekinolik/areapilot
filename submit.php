<?php

if ( ! defined('config')) require('config.php');

$TITLE .= ' - Submit';

$CODE = $CURRENT_DIR.'code/submit.php';
$OUTPUT = $CURRENT_DIR.'html_output/submit.php';
$HEADER = ROOT_DIR.'html_output/header.php';
$FOOTER = ROOT_DIR.'html_output/footer.php';
//define('NOLOCOK', 1);

array_push($JS, 'tp/jquery-ui-1.8.10.custom.min.js');
array_push($JS, 'basics.js');
array_push($JS, 'submit.js');
array_push($CSS, 'jquery-ui-1.8.1.custom.css');

require($CURRENT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

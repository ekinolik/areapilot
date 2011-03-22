<?php

if ( ! defined('config')) require('config.php');

$TITLE .= ' - Post an Event';

$CODE = $CURRENT_DIR.'code/submit.php';
$OUTPUT = $CURRENT_DIR.'html_output/submit.php';
$HEADER = ROOT_DIR.'html_output/header.php';
$FOOTER = ROOT_DIR.'html_output/footer.php';
//define('NOLOCOK', 1);

array_push($JS, 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js');
array_push($JS, 'submit.js');
array_push($CSS, 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/ui-darkness/jquery-ui.css');

require($CURRENT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

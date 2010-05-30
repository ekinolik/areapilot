<?php

if ( ! defined('config')) require('config.php');

$TITLE = 'View Pages';

$CODE = $CURRENT_DIR.'code/view_pages.php';
$OUTPUT = $CURRENT_DIR.'html_output/view_pages.php';
$HEADER = ROOT_DIR.'html_output/header.php';
$FOOTER = ROOT_DIR.'html_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, 'blockUI.js');
array_push($JS, 'rounded.js');
array_push($JS, 'basics.js');
array_push($CSS, '');


require(ROOT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

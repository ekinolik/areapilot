<?php

if ( ! defined('config')) require('config.php');

$TITLE = 'Add a New Category';

$CODE = $CURRENT_DIR.'code/new_category.php';
$OUTPUT = $CURRENT_DIR.'html_output/new_category.php';
$HEADER = ROOT_DIR.'html_output/header.php';
$FOOTER = ROOT_DIR.'html_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, 'rounded.js');
array_push($JS, 'basics.js');
array_push($JS, 'new_category.js');
array_push($CSS, '');

require(ROOT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

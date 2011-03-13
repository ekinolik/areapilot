<?php

if ( ! defined('config')) require('config.php');

$TITLE = 'Add an Area';

$CODE = $CURRENT_DIR.'code/add_area.php';
$OUTPUT = $CURRENT_DIR.'html_output/add_area.php';
$HEADER = ROOT_DIR.'html_output/header.php';
$FOOTER = ROOT_DIR.'html_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, 'add_area.js');
array_push($JS, 'basics.js');
array_push($CSS, 'admin.css');

require(ROOT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

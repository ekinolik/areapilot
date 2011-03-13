<?php

if ( ! defined('config')) require('config.php');

$TITLE .= ' - Comment';

$CODE = $CURRENT_DIR.'code/comment.php';
$OUTPUT = $CURRENT_DIR.'json_output/comment.php';
$HEADER = ROOT_DIR.'json_output/header.php';
$FOOTER = ROOT_DIR.'json_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, 'basics.js');
array_push($CSS, '');

require($CURRENT_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

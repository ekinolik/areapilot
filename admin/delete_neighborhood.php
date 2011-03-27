<?php

if ( ! defined('config')) require('config.php');

$TITLE = 'Delete a Neighborhood';

$CODE = $CURRENT_DIR.'code/delete_neighborhood.php';
$OUTPUT = $CURRENT_DIR.'xml_output/delete_neighborhood.php';
$HEADER = ADMIN_DIR.'xml_output/header.php';
$FOOTER = ADMIN_DIR.'xml_output/footer.php';
define('NOLOCOK', 1);

array_push($JS, '');
array_push($CSS, '');

require(ADMIN_DIR.'code/init.php');
if (isset($CODE))   require($CODE);
if (isset($HEADER)) require($HEADER);
if (isset($OUTPUT)) require($OUTPUT);
if (isset($FOOTER)) require($FOOTER);

?>

<?php

$status = 0;

if ( ! defined('LOCATION')) require('lib/Location.php');
if ( ! defined('MISC')) require(LIB_DIR.'Misc.php');

if (verify_int($_POST['state_id']) === FALSE) {
   $error_class->create_error(2, 'Invalid state id', 'Code');
   return FALSE;
}

$loc = new Location($db_class, $error_class);
$loc->get_zip_in_state($_POST['state_id']);
$loc->get_city_in_state($_POST['state_id']);
$loc->get_area_code_in_state($_POST['state_id']);
$loc->get_area_in_state($_POST['state_id']);
$loc->get_county_in_state($_POST['state_id']);
//if ($loc->get_parent_areas() === FALSE) return FALSE;
$loc->get_empty_parent_areas($_POST['state_id']);
$loc->get_child_areas($_POST['state_id']);
?>

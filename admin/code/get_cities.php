<?php

$status = 0;

if (LOGGED_IN === FALSE || $user_c->my_admin === FALSE) {
   $error_class->create_error(1, 'Not logged in', 'Code');
   return FALSE;
}

if ( ! defined('LOCATION')) require('lib/Location.php');
if ( ! defined('MISC')) require(LIB_DIR.'Misc.php');

if (verify_int($_POST['area_id']) === FALSE) {
   $error_class->create_error(2, 'Invalid area ID', 'Code');
   return FALSE;
}

$location_c = new Location($db_class, $error_class);
$location_c->area_id = $_POST['area_id'];
if (($zc = $location_c->get_cities_and_zips()) === FALSE) return FALSE;

$status = 1;

?>

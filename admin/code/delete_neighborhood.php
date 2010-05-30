<?php

$status = 0;

if (LOGGED_IN === FALSE || $user_c->my_admin === FALSE) {
   $error_class->create_error(1, 'Not logged in', 'Code');
   return FALSE;
}

if ( ! defined('LOCATION')) require(LIB_DIR.'Location.php');
if ( ! defined('MISC')) require(LIB_DIR.'Misc.php');

if (verify_int($_POST['hood_id']) === FALSE) {
   $error_class->create_error(2, 'Invalid neighborhood ID', 'Code');
   return FALSE;
}

$location_c = new Location($db_class, $error_class);
$location_c->neighborhood_id = $_POST['hood_id'];
if ($location_c->delete_neighborhood() === FALSE) return FALSE;

$status = 1;

?>

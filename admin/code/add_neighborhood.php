<?php

if (LOGGED_IN === FALSE || $user_c->my_admin === FALSE) {
   header('Location: '.ADMIN_URL.'errorpage.php');
   exit;
}

if ( ! defined('LOCATION')) require('lib/Location.php');
if ( ! defined('MISC')) require(LIB_DIR.'Misc.php');

$loc = new Location($db_class, $error_class);

if (isset($_POST['applylist']) && is_array($_POST['applylist'])) {
   $loc->neighborhood_name = $_POST['hoodname'];
   //$loc->city_id = $_POST['selectcity'];
   $loc->zips = $_POST['applylist'];

   $loc->zip_id = $_POST['applylist'][0];
   $loc->city_id = $loc->get_city_by_zip_id();
   $loc->create_neighborhood();

   header('Location: '.$CURRENT_URL.'add_neighborhood.php');
   exit;
}
$loc->get_states();
?>

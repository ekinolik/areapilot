<?php

if ( ! defined('USER')) require(LIB_DIR.'User.php');

$users = new User($db_class, $error_class);

if (LOGGED_IN === FALSE || $user_c->my_admin === FALSE) {
   $error_class->create_error(1, 'You must be logged in', 'Code');
   return FALSE;
}

if ( ! isset($_GET['id']) || verify_int($_GET['id'] === FALSE) {
   $error_class->create_error(2, 'Invalid user ID', 'Code');
   return FALSE;
}

if (isset($_GET['status']) && $_GET['status'] == 'g') {
   $users->get_user_by_id();
}


?>

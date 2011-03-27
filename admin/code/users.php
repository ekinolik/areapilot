<?php

if (LOGGED_IN === FALSE || $user_c->my_admin === FALSE) {
      header('Location: '.ADMIN_URL.'errorpage.php');
         exit;
}

if ( ! defined('USER')) require(LIB_DIR.'User.php');

$users = new User($db_class, $error_class);
$users->get_all();

?>

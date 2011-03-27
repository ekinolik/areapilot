<?php

if (LOGGED_IN === FALSE || $user_c->my_admin === FALSE) {
      header('Location: '.ADMIN_URL.'errorpage.php');
         exit;
}

if ( ! defined('PAGE')) require(LIB_DIR.'Page.php');
if ( ! defined('MISC')) require(LIB_DIR.'Misc.php');

$page_c = new Page($db_class, $error_class);
$page_c->get_all();

?>

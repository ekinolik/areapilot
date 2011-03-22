<?php

if (LOGGED_IN === TRUE) {
   header('Location: '.ROOT_URL);
   exit;
}

if (isset($_GET['code'])) {
   if ( ! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');

   $account = new Account($db_class, $error_class);

   if ($account->reset_password($_GET['code']) === FALSE) {
      return FALSE;
   }

   if ($account->login() === FALSE) {
      return FALSE;
   }

   $session->user_id = $account->id;
   if ($session->create() === FALSE) {
      return FALSE;
   }

   setcookie('rp', '1', time()+(60*60*24*GC_MAXLIFETIME), '/', COOKIE_DOMAIN);
   header('Location: '.ROOT_URL.'profile');
   exit;
}

?>

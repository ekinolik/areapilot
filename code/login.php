<?php

if (LOGGED_IN === TRUE) {
   header('Location: '.ROOT_URL);
   exit;
}

if (isset($_POST['username'])) {
   if ( ! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');

   $account = new Account($db_class, $error_class);

   $account->username = $_POST['username'];
   $account->password = $_POST['password'];

   if ($account->login() === FALSE) {
      return FALSE;
   }

   $session->user_id = $account->id;
   if ($session->create() === FALSE) {
      return FALSE;
   }

   header('Location: '.$_SERVER['HTTP_REFERER']);
}

?>

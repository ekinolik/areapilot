<?php

if (isset($_POST['username'])) {
   if ( ! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');

   $account = new Account($db_class, $error_class);

   $account->username   = $_POST['username'];
   $account->password   = $_POST['password'];
   $account->confirm    = $_POST['password2'];
   $account->email      = $_POST['email'];
   $account->first_name = $_POST['first_name'];
   $account->last_name  = $_POST['last_name'];

   if ($account->create() === FALSE) {
      return FALSE;
   }
}
?>

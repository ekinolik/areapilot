<?php

if (LOGGED_IN !== TRUE) {
   header('Location: '.ROOT_URL);
   exit;
}

if (isset($_POST['password'])) {
   if ( ! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');

   $account = new Account($db_class, $error_class);

   $account->password = $_POST['password'];
   $account->confirm  = $_POST['password2'];

   $account->id = $session->user_id;
   if (($account->username = $account->get_username()) === FALSE) {
      $error_class->create_error(1, 'Invalid user ID', 'Code');
      return FALSE;
   }

   if ($account->change_password() == FALSE) {
      return FALSE;
   }

   header('Location: '.$_SERVER['HTTP_REFERER']);
}

?>

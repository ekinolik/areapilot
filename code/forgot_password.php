<?php

if (LOGGED_IN === TRUE) {
   header('Location: '.ROOT_URL);
   exit;
}

if (isset($_POST['username']) && isset($_POST['email'])) {
   if ( ! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');

   $account = new Account($db_class, $error_class);

   $account->username = $_POST['username'];
   $account->email    = $_POST['email'];

   if ($account->forgot_password() == FALSE) {
      return FALSE;
   }

   header('Location: '.$_SERVER['HTTP_REFERER']);
    
   $blah;
}

?>

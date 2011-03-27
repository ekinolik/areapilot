<?php

if (LOGGED_IN === FALSE || $user_c->my_admin === FALSE) {
   header('Location: '.ADMIN_URL.'errorpage.php');
   exit;
}

if ( ! defined('PAGE')) require(LIB_DIR.'Page.php');
if ( ! defined('MISC')) require(LIB_DIR.'Misc.php');

$page_c = new Page($db_class, $error_class);

if ( isset($_POST['id']) && verify_int($_POST['id'])) {
   $page_c->id = $_POST['id'];
   $page_c->name = $_POST['pagename'];
   $page_c->text = $_POST['contents'];

   if ($page_c->update() === FALSE) return FALSE;

   header('Location: '.$CURRENT_URL.'view_pages.php');
   exit;
}

if ( ! isset($_GET['id']) || verify_int($_GET['id']) === FALSE) return FALSE;

$page_c->id = $_GET['id'];
$page_c->get();
?>

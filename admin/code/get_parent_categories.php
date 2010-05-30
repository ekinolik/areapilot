<?php

if ( ! defined('CATEGORIES')) require(LIB_DIR.'Categories.php');
if ( ! defined('MISC'))       require(LIB_DIR.'Misc.php');

if ( isset($_POST['t']) && verify_int($_POST['t'])) $type = $_POST['t']; else $type = 0;
if ( $type === 0) {
   if ( isset($_GET['t']) && verify_int($_GET['t'])) 
      $type = $_GET['t']; else $type = 0;
}

$categories = new Categories($db_class, $error_class);
$cat_a = $categories->get_parents($type);

?>

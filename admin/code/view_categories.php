<?php

if ( ! defined('CATEGORIES')) require(LIB_DIR.'Categories.php');

$categories_class = new Categories($db_class, $error_class);

if (isset($_GET['status']) && ($_GET['status'] == 't' || $_GET['status'] == 'f')) {
   $categories_class->change_status($_GET['status'], $_GET['id']);
} elseif (isset($_GET['status']) && $_GET['status'] == 'delete') {
   $categories_class->delete($_GET['id']);
}

if (($categories = $categories_class->get_categories()) === FALSE) return FALSE;

$categories = $categories_class->associate_parents($categories);
?>

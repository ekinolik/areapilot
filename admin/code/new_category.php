<?php

if ( ! defined('CATEGORIES')) require(LIB_DIR.'Categories.php');

$categories_class = new Categories($db_class, $error_class);

$msg = '&nbsp;';
if (isset($_POST['name']) && strlen(trim($_POST['name'])) > 0) {
   $categories_class->parent = $_POST['parent'];
   $categories_class->name = $_POST['name'];
   $categories_class->type_id = $_POST['type'];
   $categories_class->active = 't';

   if ($categories_class->create() === FALSE) {
      $msg = $error_class->error;
   } else {
      $msg = 'Category successfully created';
   }
}

$categories = $categories_class->get_parents(2);
?>

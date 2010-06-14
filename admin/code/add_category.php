<?php

if ( ! defined('CATEGORYCLASS')) require(LIB_DIR.'Category.php');

$category = new Category($db_class, $error_class);

if (isset($_POST['title'])) {
   $category->category_title    = $_POST['title'];
   $category->category_parent   = $_POST['parent'];
   $category->category_sequence = $_POST['sequence'];

   $category->create($_POST['title'], $_POST['parent']);
}

$category_cnt = $category->get_parent_categories();

?>

<?php

if ( ! defined('PAGE')) require(LIB_DIR.'Page.php');
if ( ! defined('FILE')) require(LIB_DIR.'File.php');

$page_c = new Page($db_class, $error_class);
$file_c = new File($db_class, $error_class);

$db_class->begin();

$file_c->start_form();
$page_c->type = PAGE_FORM_INT;

if (isset($_POST['id']) && verify_int($_POST['id'])) {
   $page_c->name = $_POST['title'];
   $page_c->text = $_POST['description'];
   $page_c->page_id = $_POST['id'];
   $page_c->id = $page_c->page_id;
   if ($page_c->update() === FALSE) {
      $db_class->rollback();
      return FALSE;
   }

   if ($_FILES['formfile']['size'] < 1) return TRUE;

   $file_c->table_id = $page_c->page_id;
   if ($file_c->get_file_from_reference() === FALSE) {
      $db_class->rollback();
      return FALSE;
   }

   for ($i = 0, $iz = count($file_c->files); $i < $iz; ++$i) {
      $file_c->file_name = $file_c->files[$i]['filename'];
      if ($file_c->remove_file() === FALSE) {
	 $db_class->rollback();
	 return FALSE;
      }
   }
} else if (isset($_GET['id']) && verify_int($_GET['id'])) {
   $page_c->page_id = $_GET['id'];
   $page_c->id = $page_c->page_id;

   $file_c->table_id = $page_c->page_id;
   if ($file_c->get_file_from_reference() === FALSE) {
      $db_class->rollback();
      return FALSE;
   }

   if ($page_c->delete() === FALSE) {
      $db_class->rollback();
      return FALSE;
   }

   for ($i = 0, $iz = count($file_c->files); $i < $iz; ++$i) {
      $file_c->file_name = $file_c->files[$i]['filename'];
      if ($file_c->remove_file() === FALSE) {
	 $db_class->rollback();
	 return FALSE;
      }
   }

   $db_class->commit();

   return TRUE;
} else {
   if ($_FILES['formfile']['size'] < 1) {
      $error_class->create_error(1, 'Invalid file', 'Code');
      return FALSE;
   }

   $page_c->name = $_POST['title'];
   $page_c->text = $_POST['description'];

   if ($page_c->save() === FALSE) {
      $db_class->rollback();
      return FALSE;
   }
}

$file_c->new_file = TRUE;
$file_c->table_id = $page_c->page_id;
$file_c->file_name = $_FILES['formfile']['name'];
$file_c->file_tmpname = $_FILES['formfile']['tmp_name'];

if ($file_c->add() === FALSE) {
   $db_class->rollback();
   return FALSE;
}

if ($file_c->add_file_to_db() === FALSE) {
   $db_class->rollback();
   return FALSE;
}

$db_class->commit();

?>

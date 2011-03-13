<?php

if ( ! defined('VENUECLASS')) require(LIB_DIR.'Venue.php');
if ( ! defined('CATEGORYCLASS')) require(LIB_DIR.'Category.php');

$category = new Category($db_class, $error_class);
$category->get_all_categories(TRUE);
$category->create_md();

if ( isset($_POST['title'])) {
   $venue = new Venue($db_class, $error_class);

   $venue->title       = $_POST['title'];
   $venue->date        = $_POST['date'];
   $venue->time        = trim($_POST['time']);
   $venue->meridian    = trim($_POST['meridian']);
   $venue->address     = $_POST['address'];
   $venue->zip         = $_POST['zip'];
   $venue->url         = $_POST['url'];
   $venue->description = $_POST['description'];
   $venue->user_id     = $session->user_id;
   $venue->category_id = $_POST['category'];
   $venue->name        = $_POST['venue'];
   $venue->phone       = $_POST['venuephone'];

   //$venue->tag         = explode(',', $_POST['tags']);

   if ( $venue->create() === FALSE) {
      return FALSE;
   }

   header('Location: '.ROOT_URL.$venue->uri_title);
   exit;
}

?>

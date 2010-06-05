<?php

if ( isset($_POST['title'])) {
   if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');

   $event = new Event($db_class, $error_class);

   $event->title       = $_POST['title'];
   $event->date        = $_POST['date'];
   $event->time        = trim($_POST['time']);
   $event->meridian    = trim($_POST['meridian']);
   $event->address     = $_POST['address'];
   $event->zip         = $_POST['zip'];
   $event->url         = $_POST['url'];
   $event->description = $_POST['description'];
   $event->user_id     = $session->user_id;

   $event->tag         = explode(',', $_POST['tags']);

   if ( $event->create() === FALSE) {
      return FALSE;
   }
}

?>

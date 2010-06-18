<?php

if ( ! defined('CHATCLASS')) require(LIB_DIR.'Chat.php');

if (isset($_POST['add_comment'])) {
   $comment = new Chat($db_class, $error_class);

   $comment->user_id = $session->user_id;
   $comment->event_id = $_POST['event_id'];
   $comment->msg = $_POST['add_comment'];

   if (isset($_POST['parent_id'])) $comment->parent_id = $_POST['parent_id'];

   $comment->add_comment();

   header('Location: /event.php?id='.$comment->event_id);

   exit;
}
?>

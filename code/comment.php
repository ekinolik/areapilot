<?php

if ( ! defined('CHATCLASS')) require(LIB_DIR.'Chat.php');
if ( ! defined('JSONCLASS')) require(LIB_DIR.'JSON.php');
if ( ! defined('ACCOUNTCLASS')) require(LIB_DIR.'Account.php');

$json = '';
if (isset($_POST['add_comment']) && LOGGED_IN === TRUE) {
   
   $comment = new Chat($db_class, $error_class);

   $comment->user_id = $session->user_id;
   $comment->event_id = $_POST['event_id'];
   $comment->msg = $_POST['add_comment'];

   if (isset($_POST['parent_id']) && verify_int($_POST['parent_id'])) {
      $comment->parent_id = $_POST['parent_id'];
   } else {
      $comment->parent_id = 0;
   }

   if (($username = Account::get_username($comment->user_id, $db_class)) === FALSE) {
      $error_class->create_error(1, 'No username attached to this user id', 'Code');
      $json = JSON::encode(array($error_class));
   }

   if ($comment->add_comment() === FALSE) {
      $json = JSON::encode(array($error_class));
      return FALSE;
   }

   /*
   header('Location: /event.php?id='.$comment->event_id);

   exit;
    */

   $json = JSON::encode(array('comment'=>$comment->msg,'username'=>$username, 'time'=>date("Y-m-d H:i", CURRENT_TIME), 'id'=>$comment->id, 'parent'=>$comment->parent_id));

} else if (isset($_POST['gch'])) {
   $comment = new Chat($db_class, $error_class);

   $comment->id = $_POST['gch'];
   $comment->get_comments();

   $json = JSON::encode(array('comments'=>$comment->comment));
}
?>

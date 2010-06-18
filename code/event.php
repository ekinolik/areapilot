<?php

if ( ! defined('VENUECLASS'))    require(LIB_DIR.'Venue.php');
if ( ! defined('CATEGORYCLASS')) require(LIB_DIR.'Category.php');
if ( ! defined('CHATCLASS'))     require(LIB_DIR.'Chat.php');

$venue = new Venue($db_class, $error_class);
$venue->event_id = $_GET['id'];
$venue->get_event();

$comment = new Chat($db_class, $error_class);
$comment->event_id = $_GET['id'];
$comment->get_parent_comments();

?>

<?php

if ( ! defined('VENUECLASS'))    require(LIB_DIR.'Venue.php');
if ( ! defined('CATEGORYCLASS')) require(LIB_DIR.'Category.php');
if ( ! defined('CHATCLASS'))     require(LIB_DIR.'Chat.php');

$venue = new Venue($db_class, $error_class);
//$venue->event_id = $_GET['id'];
$venue->uri_title = $_GET['title'];
if ($venue->get_event() === FALSE) return FALSE;
if (($top_events = $venue->get_top()) === FALSE) return FALSE;

$comment = new Chat($db_class, $error_class);
$comment->event_id = $venue->event_id;
$comment->get_parent_comments();

$TITLE = $venue->events[0]['title'];
?>

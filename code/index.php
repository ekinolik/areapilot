<?php

if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');
if ( ! defined('CHATCLASS'))  require(LIB_DIR.'Chat.php');

$event = new Event($db_class, $error_class);

$event->start_time = TIME_START;
$event->end_time = TIME_END;

$event->get_events();
$top_event = $event->get_top();

$event_ids = array_from_md_element($event->events, 'id');
$comment_count = Chat::get_comment_count($event_ids, $db_class);
?>

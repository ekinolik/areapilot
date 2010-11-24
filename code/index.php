<?php

if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');

$event = new Event($db_class, $error_class);

$event->start_time = TIME_START;
$event->end_time = TIME_END;

$event->get_events();
$top_event = $event->get_top();

?>

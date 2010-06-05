<?php

if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');

$event = new Event($db_class, $error_class);
$event->get_events();

?>

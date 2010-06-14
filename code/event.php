<?php

if ( ! defined('VENUECLASS')) require(LIB_DIR.'Venue.php');
if ( ! defined('CATEGORYCLASS')) require(LIB_DIR.'Category.php');

$venue = new Venue($db_class, $error_class);
$venue->event_id = $_GET['id'];
$venue->get_event();

?>

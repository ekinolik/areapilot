<?php

/*
 * param id (id)	= event id
 */


if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');
if ( ! defined('JSONCLASS'))  require(LIB_DIR.'JSON.php');

$json = '';

if ( ! isset($_GET['id'])) {
   $json = JSON::encode(array('error'=>'Heh... heh heh heh heh'));
   return FALSE;
}

if (($attendance = Event::attendance($_GET['id'], $db_class)) === FALSE) {
   $error_class->create_error(1, 'Could not get attendance', 'Code');
   $json = JSON::encode(array('error'=>$error_class));
   return FALSE;
}

$json = JSON::encode(array('id'=>$_GET['id'], 'attendees'=>$attendance));

?>

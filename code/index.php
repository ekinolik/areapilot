<?php

if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');
if ( ! defined('CHATCLASS'))  require(LIB_DIR.'Chat.php');

$event = new Event($db_class, $error_class);

$event->start_time = TIME_START;
$event->end_time = TIME_END;

$event->get_dates_with_events();
$event->get_events();
$event->get_attendance();
$top_event = $event->get_top();

$event_ids = array_from_md_element($event->events, 'id');
$comment_count = Chat::get_comment_count($event_ids, $db_class);

$fb_meta['title'] = $TITLE;
$fb_meta['type'] = 'website';
$fb_meta['url'] = ROOT_URL;
$fb_meta['image'] = IMG_URL.'images/logo.png';
$fb_meta['site_name'] = 'AreaPilot';
$fb_meta['admins'] = FACEBOOK_ADMINS;
?>

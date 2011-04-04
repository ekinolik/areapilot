<?php

if ( ! defined('VENUECLASS'))    require(LIB_DIR.'Venue.php');
if ( ! defined('CATEGORYCLASS')) require(LIB_DIR.'Category.php');
if ( ! defined('CHATCLASS'))     require(LIB_DIR.'Chat.php');

$venue = new Venue($db_class, $error_class);
$venue->uri_title = $_GET['title'];
if ($venue->get_event() === FALSE) return FALSE;
if (($top_events = $venue->get_top()) === FALSE) return FALSE;

if ($venue->get_categories() === FALSE) 
   return FALSE;
$venue->events[0]['category'] = $venue->category;
$top_event = $venue->get_top($venue->category[0]['id']);

$comment = new Chat($db_class, $error_class);
$comment->event_id = $venue->event_id;
$comment->get_parent_comments();

$TITLE = $venue->events[0]['title'];

$fb_meta['title'] = $TITLE;
$fb_meta['type'] = 'activity';
$fb_meta['url'] = ROOT_URL.$venue->events[0]['uri_title'];
$fb_meta['image'] = IMG_URL.'images/logo.png';
$fb_meta['site_name'] = 'AreaPilot';
$fb_meta['admins'] = FACEBOOK_ADMINS;

if (isset($_SERVER['HTTP_REFERER']) && 
   ($_SERVER['HTTP_REFERER'] === ROOT_URL.'post_event' ||
    $_SERVER['HTTP_REFERER'] === SROOT_URL.'post_event')) {
      $display_shares = TRUE;
}
?>

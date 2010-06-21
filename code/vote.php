<?php

/* 
 * $_GET
 * param id (id)	= event id or comment id
 * param t (type) 	= e (event)	c (comment)
 * param a (action) 	= l (like, 1)	a (attend, 2)	d (dislike, -1)
 * param r (return)	= j (json)	x (xml)
 */

if ( ! isset($_GET['r']) || $_GET['r'] !== 'j') {
   $HEADER = 'html_output/header.php';
   $OUTPUT = 'html_output/vote.php';
   $FOOTER = 'html_output/footer.php';
}

if ( ! defined('EVENTCLASS')) require(LIB_DIR.'Event.php');
if ( ! defined('JSONCLASS'))  require(LIB_DIR.'JSON.php');

$json = '';
if (isset($_GET['id']) && isset($_GET['a'])) {

   if ($_GET['t'] === 'e')      $id = $_GET['id'];
   else if ($_GET['t'] === 'c') $id = $_GET['id'];
   else {
      $error_class->create_error(1, 'Invalid vote type', 'Code');
      JSON::encode(array($error_class));
      return FALSE;
   }

   if ($_GET['a'] === 'a' && $_GET['t'] === 'e') $rating = 2;
   else if ($_GET['a'] === 'l')                  $rating = 1;
   else if ($_GET['a'] === 'd')                  $rating = -1;
   else {
      $error_class->create_error(2, 'Invalid vote', 'Code');
      JSON::encode(array($error_class));
      return FALSE;
   }

   /* Enter vote */
   if (Event::vote($_GET['t'], $session->user_id, $id, $rating, $db_class) === FALSE) {
      $error_class->create_error(3, 'Could not add vote', 'Code');
      JSON::encode(array($error_class));
      return FALSE;
   }

   /* Get new vote sum */
   if (($rating = Event::rating($_GET['t'], $id, $db_class)) === FALSE) $rating = 0;

   $json = JSON::encode(array('rating'=>$rating, 'id'=>$_GET['id']));
} else {
   $json = JSON::encode(array('error'=>'Uhhhhh.... huh huh'));
}

?>

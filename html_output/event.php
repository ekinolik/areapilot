<?php

$eventdetails = HTML::eventdetails($venue->events[0]);
//$venuedetails = HTML::venuedetails($venue->events[0]);
$venuedetails = '';
$map = HTML::map($venue->events[0]);
/*
$event = &$venue->events[0];

$id          = htmlspecialchars($event['id']);
$title       = htmlspecialchars($event['title']);
$time        = date("l g:i (m/d/y)", strtotime($event['time']));
$description = nl2br(htmlspecialchars($event['description']));
$area        = htmlspecialchars($event['area']);
$username    = htmlspecialchars($event['username']);
$venue_name  = htmlspecialchars($event['venuename']);
$address     = htmlspecialchars($event['address']);
$city        = htmlspecialchars(ucwords($event['city']));
$state       = htmlspecialchars(strtoupper($event['state']));
$zip         = htmlspecialchars($event['zip']);
 */

$comments = '';
for ($i = 0, $iz = count($comment->comment); $i < $iz; ++$i) {
   $comments .= HTML::comment($comment->comment[$i]);

   /*
   $c = &$comment->comment[$i];
   $cid   = htmlspecialchars($c['id']);
   $cmsg  = convert_links(nl2br(htmlspecialchars($c['comment'])));
   $cuser = htmlspecialchars($c['username']);
   $ctime = htmlspecialchars($c['time']);

   $comment_html .= '   <div id="comment_'.$cid.'">'."\n";
   $comment_html .= '     <span class="username">'.$cuser.'</span>'."\n";
   $comment_html .= '     <span class="time">'.$ctime.'</span>'."\n";
   $comment_html .= '     <a href="#" class="reply">Reply</a><br />'."\n";
   $comment_html .= '     <span class="message">'.$cmsg.'</span><br />'."\n";
   $comment_html .= '   </div>'."\n";
   $comment_html .= '   <hr />'."\n";
    */
}

if (LOGGED_IN === TRUE)
   $commentform = HTML::commentform($error_class, $comment->event_id);
else
   $commentform = '';

$commentlist = HTML::commentlist($comments, $commentform);

$event = HTML::event($venue->events[0]['title'], $eventdetails, $venuedetails, $map, $commentlist);

print <<<EOF


$event

EOF;

?>

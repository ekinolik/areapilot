<?php

$top_list = HTML::toplist($top_event, $venue->events[0]['category']['0']['title']);

$eventdetails = HTML::eventdetails($venue->events[0]);
$venuedetails = '';
$map = HTML::map($venue->events[0]);

$comments = '';
for ($i = 0, $iz = count($comment->comment); $i < $iz; ++$i) {
   $comments .= HTML::comment($comment->comment[$i]);
}

if (strlen($comments) < 1) {
   $comments = 'Be the first to leave a comment';
}

if (LOGGED_IN === TRUE)
   $commentform = HTML::commentform($error_class, $comment->event_id);
else
   $commentform = '';

$commentlist = HTML::commentlist($comments, $commentform);

$event = HTML::event($venue->events[0]['title'], $eventdetails, $venuedetails, $top_list, $map, $commentlist);

print <<<EOF

$event

EOF;

?>

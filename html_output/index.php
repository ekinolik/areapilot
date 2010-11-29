<?php

$datemenu = HTML::datemenu(HTML::url_friendly_category(CATEGORY_TITLE));

$entries = '';
for ($i = 0, $iz = count($event->events); $i < $iz; ++$i) {

   $entries .= HTML::entry($event->events[$i], $i+1);
   $entries = HTML::replace_comment_count($entries, $comment_count, $event->events[$i]['id']);
}

$date_text = date('D, M d, Y', TIME_START);
if (TIME_END >= TIME_START + 86400) 
   $date_text .= ' - '.date('D, M d, Y', TIME_END);

$pages = ceil($event->total / EVENT_LIST_COUNT);
$pagination = HTML::pagination($pages);
$sidecol = HTML::sidecol($top_event);
$list = HTML::pagelist('Events for '.$date_text, $entries, $sidecol, $pagination);


print <<<EOF

$datemenu
$list

EOF;

?>

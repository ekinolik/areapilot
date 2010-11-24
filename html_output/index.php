<?php

$datemenu = HTML::datemenu(HTML::url_friendly_category(CATEGORY_TITLE));

$entries = '';
for ($i = 0, $iz = count($event->events); $i < $iz; ++$i) {

   $entries .= HTML::entry($event->events[$i], $i+1);
}

$pages = ceil($event->total / EVENT_LIST_COUNT);
$pagination = HTML::pagination($pages);
$sidecol = HTML::sidecol($top_event);
$list = HTML::pagelist('Events for '.date('D, M d, Y', CURRENT_TIME), $entries, $sidecol, $pagination);


print <<<EOF

$datemenu
$list

EOF;

?>

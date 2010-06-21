<?php

$datemenu = HTML::datemenu();

$entries = '';
for ($i = 0, $iz = count($event->events); $i < $iz; ++$i) {

   $entries .= HTML::entry($event->events[$i], $i+1);
}

$sidecol = HTML::sidecol(NULL);
$list = HTML::pagelist('Events for '.date('D, M d, Y', CURRENT_TIME), $entries, $sidecol);

print <<<EOF

$datemenu
$list

EOF;

?>

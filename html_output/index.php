<?php

$html = '';
for ($i = 0, $iz = count($event->events); $i < $iz; ++$i) {
   $title       = htmlspecialchars($event->events[$i]['title']);
   $time        = htmlspecialchars($event->events[$i]['time']);
   $description = htmlspecialchars(substr($event->events[$i]['description'], 0, 400));
   $area        = htmlspecialchars($event->events[$i]['area']);
   $username    = htmlspecialchars($event->events[$i]['username']);

   $html .= '<div class="event">'."\n";
   $html .= '  <span class="time">'.$time.'</span>'."\n";
   $html .= '  <span class="title">'.$title.'</span><br />'."\n";
   $html .= '  <span class="desc">'.$description.'</span><br />'."\n";
   $html .= '  <span class="area">'.$area.'</span>'."\n";
   $html .= '  <span class="user"><span class="posted">Posted by</span>'."\n";
   $html .= '  <span class="by"> ('.$username.')</span></span><br />'."\n";
   $html .= '</div>'."\n";

   //FIXME: remove this for real design
   $html .= '<br /><br />'."\n";
}

print <<<EOF

$html

EOF;

?>

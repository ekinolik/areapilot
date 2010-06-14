<?php

$event = &$venue->events[0];
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

print <<<EOF

<div id="event_details">
  <span id="title">$title</span>
  <span id="time">$time</span><br />
  <br />
  <span id="description">$description</span><br />
  <span id="area">$area</span>,
  Posted by (<span id="username">$username</span>)<br />
</div>
<br />
<div id="venue_details">
  <span id="venue_name">$venue_name</span><br />
  <span id="address">$address</span><br />
  <span id="city">$city</span>,
  <span id="state">$state</span>
  <span id="zip">$zip</span><br />
</div>

EOF;

?>

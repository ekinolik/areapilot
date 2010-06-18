<?php

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

$comment_html = '';
for ($i = 0, $iz = count($comment->comment); $i < $iz; ++$i) {
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
}

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
<br />
<br />
<br />

<div id="comment_container">
   <span class="title">Comments:</span><br />
   <br />
$comment_html
   <br />
   <br />
   <form method="post" action="comment.php" class="comment">
      <fieldset>
	 <span class="errormsg">$error_class</span><br />
	 <ol>
	    <li><label for="add_comment">Title</label>
               <input type="hidden" name="event_id" value="$id" id="event_id" />
	       <textarea type="text" name="add_comment" id="add_comment" rows="5" cols="20"></textarea>
	    </li>
	    <li class="submit_line">
	       <button type="submit" class="submitter">Submit Comment</button>
	    </li>
	 </ol>
      </fieldset>
   </form>
</div>

EOF;

?>

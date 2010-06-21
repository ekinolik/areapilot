<?php

define('HTMLCLASS', 1);

class HTML {

   public function entry($event, $count) {
      $s = '						';

      $id          = htmlspecialchars($event['id']);
      $uri_title   = htmlspecialchars($event['uri_title']);
      $title       = htmlspecialchars($event['title']);
      $description = htmlspecialchars(substr($event['description'], 0, 400));
      $time        = htmlspecialchars($event['time']);
      $area        = htmlspecialchars($event['area']);
      $username    = htmlspecialchars($event['username']);

      $html  = $s.'<div class="entry clearfix" id="entry_'.$count.'">'."\n";
      $html .= HTML::likebox($event);
      $html .= $s.'	<h3><a href="'.ROOT_URL.$uri_title.'">'.$title.'</a></h3>'."\n";
      $html .= $s.'	<div class="description"><p>'.$description.'</p>'."\n";
      $html .= $s.'	</div><!-- end .description -->'."\n";
      $html .= $s.'	<ul class="actionlinks">'."\n";
      $html .= $s.'		<li><a href="vote.php?id='.$id.'&t=e&a=a&r=h" name="'.$id.'" class="attendthis">Attend This Event</a></li>'."\n";
      $html .= $s.'		<li><a href="#" class="attending">89 People Attending</a></li>'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.$uri_title.'" class="commentsnum">123 Comments</a></li>'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'</div><!-- end .entry -->'."\n";

      return $html;
   }

   public function likebox($event) {
      $rating = htmlspecialchars($event['rating']);
      $id     = htmlspecialchars($event['id']);
      $s = '							';
      $html  = $s.'<div class="likebox">'."\n";
      $html .= $s.'	<span class="numlikes" id="numlikes_'.$id.'">'.$rating.'</span>'."\n";
      $html .= $s.'	<span class="xtra">people like it</span>'."\n";
      $html .= $s.'	<a href="vote.php?id='.$id.'&t=e&a=l&r=h" name="'.$id.'" class="likeit">I Like It</a>'."\n";
      $html .= $s.'</div><!-- end .likebox -->'."\n";

      return $html;
   }

   public function pagelist($title, $entries, $sidecol) {
      $s = '			';
      $html  = $s.'<div id="inner" class="clearfix">'."\n";
      $html .= $s.'	<div id="maincol">'."\n";
      $html .= $s.'		<div id="posts">'."\n";
      $html .= $s.'			<h2>'.$title.'</h2>'."\n";
      $html .= $entries;
      $html .= $s.'		</div><!-- end #posts -->'."\n";
      $html .= $s.'	</div><!-- end #maincol -->'."\n";
      $html .= $sidecol;
      $html .= $s.'</div><!-- end #inner -->'."\n";

      return $html;
   }

   public function event($title, $eventdetails, $venuedetails, $commentlist) {
      $s = '			';
      $html  = $s.'<div id="inner" class="clearfix">'."\n";
      $html .= $s.'	<div id="maincol">'."\n";
      $html .= $s.'		<div id="posts">'."\n";
      $html .= $s.'			<h2>'.$title.'</h2>'."\n";
      $html .= $eventdetails;
      $html .= $s.'			<br />'."\n";
      $html .= $venuedetails;
      $html .= $s.'			<br />'."\n";
      $html .= $s.'			<br />'."\n";
      $html .= $s.'			<br />'."\n";
      $html .= $commentlist;
      $html .= $s.'		</div><!-- end #posts -->'."\n";
      $html .= $s.'	</div><!-- end #maincol -->'."\n";
      $html .= $s.'</div><!-- end #inner -->'."\n";

      return $html;
   }

   public function sidecol($list) {
      $s = '				';
      $html  = '<div id="sidecol">'."\n";
      $html .= '	<div class="sidebox" id="popincategory">'."\n";
      $html .= '		<h2>Popular In "Live Music"</h2>'."\n";
      $html .= '		<div class="minievents">'."\n";
      $html .= '			<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>'."\n";
      $html .= '			<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>'."\n";
      $html .= '			<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>'."\n";
      $html .= '			<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>'."\n";
      $html .= '			<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>'."\n";
      $html .= '		</div>'."\n";
      $html .= '	</div><!-- end #popincategory -->'."\n";
      $html .= '</div><!-- end #sidecol -->'."\n";

      return $html;
   }

   public function menu($menu) {
      $s = '			';
      $html  = $s.'<div id="subhead" class="clearfix">'."\n";
      $html .= $s.'	<ul id="categories">'."\n";
      for ($i = 0, $iz = count($menu); $i < $iz; ++$i) {
	 $link = strtolower(str_replace(' ', '_', $menu[$i]['title']));
	 $html .= $s.'		<li><a href="'.ROOT_URL.$link.'">'.$menu[$i]['title'].'</a></li>'."\n";
      }
      /*
      $html .= $s.'		<li><a href="#">Stand-Up Comedy</a></li>'."\n";
      $html .= $s.'		<li><a href="#">Live Theatre</a></li>'."\n";
      $html .= $s.'		<li><a href="#">Festivals</a></li>'."\n";
      $html .= $s.'		<li><a href="#">Club Parties</a></li>'."\n";
       */
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'</div><!-- end #subhead -->'."\n";

      return $html;
   }

   public function datemenu() {
      $s = '			';
      $html  = $s.'<div id="timeline" class="clearfix">'."\n";
      $html .= $s.'	<ul id="timeoptions">'."\n";
      $html .= $s.'		<li><a href="#" class="current" id="time-today">Today</a></li>'."\n";
      $html .= $s.'		<li><a href="#" id="time-tomorrow">Tomorrow</a></li>'."\n";
      $html .= $s.'		<li><a href="#" id="time-thisweek">This Week</a></li>'."\n";
      $html .= $s.'		<li><a href="#" id="time-nextweek">Next Week</a></li>'."\n";
      $html .= $s.'		<li><a href="#" id="time-thismonth">This Month</a></li>'."\n";
      $html .= $s.'		<li><a href="#" id="time-nextmonth">Next Month</a></li>'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'	<a href="#" id="rangeselect">Select Date Range</a>'."\n";
      $html .= $s.'</div><!-- end #timeline -->'."\n";

      return $html;
   }

   public function comment($comment) {
      $cid   = htmlspecialchars($comment['id']);
      $cmsg  = convert_links(nl2br(htmlspecialchars($comment['comment'])));
      $cuser = htmlspecialchars($comment['username']);
      $ctime = htmlspecialchars($comment['time']);

      $s = '							';
      $html  = $s.'<div id="comment_'.$cid.'">'."\n";
      $html .= $s.'	<span class="username">'.$cuser.'</span>'."\n";
      $html .= $s.'	<span class="time">'.$ctime.'</span>'."\n";
      $html .= $s.'	<a href="#" class="reply">Reply</a><br />'."\n";
      $html .= $s.'	<span class="message">'.$cmsg.'</span><br />'."\n";
      $html .= $s.'</div>'."\n";
      $html .= $s.'<hr />'."\n";

      return $html;
   }

   public function commentlist($comments, $commentform) {
      $s = '						';

      $html  = $s.'<div id="comment_container">'."\n";
      $html .= $s.'	<span class="title">Comments:</span><br />'."\n";
      $html .= $s.'	<br />'."\n";
      $html .= $comments;
      $html .= $s.'	<br />'."\n";
      $html .= $s.'	<br />'."\n";
      $html .= $commentform;
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function commentform() {
      $s = '							';
      $html  = $s.'<form method="post" action="comment.php" class="comment">'."\n";
      $html .= $s.'	<fieldset>'."\n";
      $html .= $s.'		<span class="errormsg">'.$error_class.'</span><br />'."\n";
      $html .= $s.'		<ol>'."\n";
      $html .= $s.'			<li><label for="add_comment">Title</label>'."\n";
      $html .= $s.'				<input type="hidden" name="event_id" value="$id" id="event_id" />'."\n";
      $html .= $s.'				<textarea type="text" name="add_comment" id="add_comment" rows="5" cols="20"></textarea>'."\n";
      $html .= $s.'			</li>'."\n";
      $html .= $s.'			<li class="submit_line">'."\n";
      $html .= $s.'				<button type="submit" class="submitter">Submit Comment</button>'."\n";
      $html .= $s.'			</li>'."\n";
      $html .= $s.'		</ol>'."\n";
      $html .= $s.'	</fieldset>'."\n";
      $html .= $s.'</form>'."\n";

      return $html;
   }

   public function eventdetails($event) {
      $id          = htmlspecialchars($event['id']);
      //$title       = htmlspecialchars($event['title']);
      $time        = htmlspecialchars(date("l g:i (m/d/y)", strtotime($event['time'])));
      $description = nl2br(htmlspecialchars($event['description']));
      $area        = htmlspecialchars($event['area']);
      $username    = htmlspecialchars($event['username']);

      $s = '						';
      $html  = $s.'<div id="event_details">'."\n";
      //$html .= $s.'	<span id="title">'.$title.'</span>'."\n";
      $html .= $s.'	<span id="time">'.$time.'</span><br />'."\n";
      $html .= $s.'	<br />'."\n";
      $html .= $s.'	<span id="description">'.$description.'</span><br />'."\n";
      $html .= $s.'	<span id="area">'.$area.'</span>,'."\n";
      $html .= $s.'	Posted by (<span id="username">'.$username.'</span>)<br />'."\n";
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function venuedetails($venue) {
      $venue_name  = htmlspecialchars($venue['venuename']);
      $address     = htmlspecialchars($venue['address']);
      $city        = htmlspecialchars(ucwords($venue['city']));
      $state       = htmlspecialchars(strtoupper($venue['state']));
      $zip         = htmlspecialchars($venue['zip']);

      $s = '						';
      $html  = $s.'<div id="venue_details">'."\n";
      $html .= $s.'	<span id="venue_name">'.$venue_name.'</span><br />'."\n";
      $html .= $s.'	<span id="address">'.$address.'</span><br />'."\n";
      $html .= $s.'	<span id="city">'.$city.'</span>,'."\n";
      $html .= $s.'	<span id="state">'.$state.'</span>'."\n";
      $html .= $s.'	<span id="zip">'.$zip.'</span><br />'."\n";
      $html .= $s.'</div>'."\n";

      return $html;
   }

}

?>

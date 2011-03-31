<?php

define('HTMLCLASS', 1);

class HTML {

   public function entry($event, $count) {
      $s = '						';

      $id          = htmlspecialchars($event['id']);
      $uri_title   = $event['uri_title'];
      $title       = htmlspecialchars($event['title']);
      $time = htmlspecialchars(date("l g:i A (F d, Y)", strtotime($event['time'])));
      $area        = htmlspecialchars($event['area']);
      $city        = htmlspecialchars(ucwords($event['city']));
      $username    = htmlspecialchars($event['username']);
      $attendance  = htmlspecialchars($event['attendance']);
      $category    = htmlspecialchars($event['category']);
      $venue       = htmlspecialchars($event['venue']);

      $description = substr($event['description'], 0, 400);
      $description = htmlspecialchars(add_elipsis($description, 400));

      $cat_uri     = HTML::url_friendly_category($event['category']);
      $cat_link = ROOT_URL.$cat_uri;

      if ($attendance > 1)          $attendance .= ' People Attending';
      else if ($attendance === '1') $attendance .= ' Person Attending';
      else                          $attendance =  ' 0 People Attending';

      $fb_like = HTML::FB_like('/'.$event['uri_title'], $event);
      $tweet   = HTML::Tweet('/'.$event['uri_title'], $event);

      $html  = $s.'<div class="entry clearfix" id="entry_'.$count.'">'."\n";
      $html .= HTML::likebox($event);
      $html .= $s.'	<div class="summary">'."\n";
      $html .= $s.'		<h3><a href="'.ROOT_URL.$uri_title.'">'.$title.'</a></h3>'."\n";
      $html .= $s.$tweet."\n";
      $html .= $s.$fb_like."\n";
      $html .= $s.'		<h4 class="metatop">'.$venue.' <span>&#124;</span> <span>Location : </span>'.$city.'</h4>'."\n";
      $html .= $s.'		<h4 class="metabottom"><span class="timeunderline">'.$time.'</span>&nbsp;<span>&#124; </span><span>Posted in : </span>&lt;<a href="'.$cat_link.'">'.$category.'</a>&gt;</h4>'."\n";
      $html .= $s.'		<div class="description"><p>'.$description.'</p>'."\n";
      $html .= $s.'		</div><!-- end .description -->'."\n";
      $html .= $s.'		<ul class="actionlinks">'."\n";
      $html .= $s.'			<li><a href="'.ROOT_URL.'vote.php?'.urlencode('id='.$id.'&t=e&a=a&r=h').'" name="'.$id.'" class="attendthis" rel="nofollow" >Attend This Event</a></li>'."\n";
      $html .= $s.'			<li><a href="#" class="attending">'.$attendance.'</a></li>'."\n";
      $html .= $s.'			<li><a href="'.ROOT_URL.$uri_title.'" class="commentsnum"><!--___COMMENT_COUNT___--></a></li>'."\n";
      $html .= $s.'		</ul>'."\n";
      $html .= $s.'	</div>'."\n";
      $html .= $s.'</div><!-- end .entry -->'."\n";

      return $html;
   }

   public function replace_comment_count($str, $comments, $event_id) {
      if ( ! array_key_exists($event_id, $comments)) 
	 $comments[$event_id] = '0';

      $count = $comments[$event_id];
      if ($count === 1)    $repl = '1 Comment';
      else if ($count > 1) $repl = htmlspecialchars($count).' Comments';
      else                 $repl = '0 Comments';

      $search = '<!--___COMMENT_COUNT___-->';
      $str = char_rreplace($str, $search, $repl);

      return $str;
   }

   public function likebox($event) {
      if (! isset($event['rating'])) $event['rating'] = 0;
      if ($event['rating'] == 1) {
	 $liketext = 'person likes it';
      } else {
	 if ($event['rating'] < 1) $event['rating'] = 0;
	 $liketext = 'people like it';
      }


      $rating = htmlspecialchars($event['rating']);
      $id     = htmlspecialchars($event['id']);
      $s = '								';
      $html  = $s.'<div class="likebox">'."\n";
      $html .= $s.'	<span class="numlikes" id="numlikes_'.$id.'">'.$rating.'</span>'."\n";
      $html .= $s.'	<span class="xtra">'.$liketext.'</span>'."\n";
      $html .= $s.'	<a href="'.ROOT_URL.'vote.php?'.urlencode('id='.$id.'&t=e&a=l&r=h').'" name="'.$id.'" class="likeit" rel="nofollow">I Like It</a>'."\n";
      $html .= $s.'</div><!-- end .likebox -->'."\n";

      return $html;
   }

   public function pagelist($title, $entries, $sidecol, $pagination) {
      $s = '			';
      $html  = $s.'<div id="inner" class="clearfix">'."\n";
      $html .= $s.'	<div id="maincol">'."\n";
      $html .= $s.'		<div id="posts">'."\n";
      $html .= $s.'			<h2 id="listing_title">'.$title.'</h2>'."\n";
      $html .= $entries;
      $html .= $s.'		</div><!-- end #posts -->'."\n";
      $html .= $pagination;
      $html .= $s.'	</div><!-- end #maincol -->'."\n";
      $html .= $sidecol;
      $html .= $s.'</div><!-- end #inner -->'."\n";

      return $html;
   }

   public function body_header($title) {
      $s = '			';
      $html  = $s.'<div id="inner" class="clearfix">'."\n";
      $html .= $s.'	<div id="maincol" class="onlycol">'."\n";
      $html .= $s.'		<div id="posts">'."\n";
      $html .= $s.'			<h2 class="title">'.$title.'</h2>'."\n";

      return $html;
   }

   public function body_footer() {
      $s = '			';
      $html  = $s.'		</div><!-- end #posts -->'."\n";
      $html .= $s.'	</div><!-- end #maincol -->'."\n";
      $html .= $s.'</div><!-- end #inner -->'."\n";

      return $html;
   }

   public function page_footer() {
      $s = '			';
      $html  = $s.'<br />'."\n";
      $html .= $s.'<div id="page_footer">'."\n";
      $html .= $s.'	<ul>'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.'about/">What is AreaPilot</a></li>'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'	<ul>'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.'help/">Help</a></li>'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'	<ul>'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.'feedback/">Feedback</a></li>'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'	<br />'."\n";
      $html .= $s.'	<br />'."\n";
      $html .= $s.'	<span class="copyright">AreaPilot &copy; Dire Networks, Inc.</span>'."\n";
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function document($doc) {
      $s = '		';
      $html  = $s.'<div id="doc_container">'."\n";
      $html .= $doc;
      $html .= $s.'</div>'."\n";

      return $html;
   }
   public function profile($details, $error) {
      $s = '   ';
      $html  = $s.'<div id="profilewrapper">'."\n";
      $html .= $s.'	<span class="errormsg">'.$error.'</span><br />'."\n";
      $html .= $details;
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function profile_details($account) {
      $username = htmlspecialchars($account['username']);
      $email    = htmlspecialchars($account['email']);
      $first    = htmlspecialchars($account['first_name']);
      $last     = htmlspecialchars($account['last_name']);

      $s = '      ';
      $html  = $s.'<div id="profile_details">'."\n";
      $html .= $s.'	<h3 class="subtitle" id="username">'.$username.'</h3>'."\n";
      $html .= $s.'	<h3 class="subtitle" id="email">'.$email.'</h3>'."\n";
      $html .= $s.'	<h3 class="subtitle" id="first">'.$first.' '.$last.'</h3>'."\n";
      $html .= $s.'     <br />'."\n";
      $html .= $s.'	<h3 class="subtitle"><a href="'.SROOT_URL.'/change_password" id="change_my_password">Change My Password</a></h3>'."\n";
      $html .= $s.'     <br />'."\n";
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function event($title, $eventdetails, $venuedetails, $toplist, $map, $commentlist) {
      $s = '			';

      $html  = HTML::body_header($title);
      $html .= $s.'			<div id="eventdetailswrapper">'."\n";
      $html .= $eventdetails;
      $html .= $s.'				<br />'."\n";
      $html .= $venuedetails;
      $html .= $s.'				<br />'."\n";
      $html .= $s.'				<br />'."\n";
      $html .= $s.'				<br />'."\n";
      $html .= $s.'			</div> <!-- end #eventdetailswrapper -->'."\n";
      $html .= $s.'			<div id="sidecol">'."\n";
      $html .= $toplist;
      $html .= $s.'				<br />'."\n";
      $html .= $s.'				<br />'."\n";
      $html .= $map;
      $html .= $s.'				<div class="clearfix"></div>'."\n";
      $html .= $s.'				<br />'."\n";
      $html .= $s.'			</div> <!-- end #sidecol -->'."\n";
      $html .= $s.'			<div class="clearfix"></div>'."\n";
      $html .= $commentlist;
      $html .= HTML:: body_footer();

      return $html;
   }

   public function sidecol($list) {
      $s = '				';

      $html  = $s.'<div id="sidecol">'."\n";
      $html .= HTML::toplist($list);
      $html .= $s.'</div><!-- end #sidecol -->'."\n";

      return $html;
   }

   public function toplist($list, $category_name=FALSE) {
      if (strlen(CATEGORY_PARENT_TITLE) > 1) $category = ' in "'.CATEGORY_PARENT_TITLE.'"';
      else if (strlen(CATEGORY_TITLE) > 1) $category = ' in "'.CATEGORY_TITLE.'"';
      else if ($category_name !== FALSE && strlen($category_name) > 1) $category = ' in "'.$category_name.'"';
      else $category = '';

      $s = '						';
      $html  = $s.'	<div class="sidebox" id="popincategory">'."\n";
      $html .= $s.'		<h2>Popular '.$category.'</h2>'."\n";
      $html .= $s.'		<div class="minievents">'."\n";

      /* Create entries for the sidecol */
      for ($i = 0, $iz = count($list); $i < $iz; ++$i) {
	 $url    = $list[$i]['uri_title'];
	 $rating = htmlspecialchars($list[$i]['rating']);
	 $title  = htmlspecialchars($list[$i]['title']);

	 $html .= $s.'			<a href="'.ROOT_URL.$url.'" class="minievent clearfix"><span class="numlikes">'.$rating.'</span><span class="title">'.$title.'</span></a>'."\n";
      }

      $html .= $s.'		</div>'."\n";
      $html .= $s.'	</div><!-- end #popincategory -->'."\n";

      return $html;
   }

   public function menu($menu) {
      $s = '			';
      $html  = $s.'<div id="subhead" class="clearfix">'."\n";
      $html .= $s.'	<ul id="categories">'."\n";

      /* Prefix menu with All if a category is selected */
      if (strlen(CATEGORY_TITLE) > 0) {
	 $html .= $s.'		<li><a href="'.ROOT_URL.'">All</a></li>'."\n";
      }

      if (DATE_GIVEN === FALSE) {
	 $date = '';
      } else if (DATE_START !== DATE_END) {
	 $date = 'date-'.DATE_START.'-'.DATE_END.'/';
      } else {
	 $date = 'date-'.DATE_START.'/';
      }

      /* Create category menu */
      for ($i = 0, $iz = count($menu); $i < $iz; ++$i) {

	 $child_menu = HTML::create_submenu($menu[$i]['children']);
	 if ($child_menu !== FALSE) $has_menu = ' hasmenu '; else $has_menu = ' ';

	 if ( CATEGORY_ID === $menu[$i]['parent']['id'] || HTML::category_selected_in_children($menu[$i]['children']) === TRUE) {
	    $class = 'current';
	 } else {
	    $class = ' ';
	 }

	 $link = HTML::url_friendly_category($menu[$i]['parent']['title']).'/';
	 $html .= $s.'		<li class="'.$has_menu.'"><a class="'.$class.'" href="'.ROOT_URL.$link.$date.'">'.$menu[$i]['parent']['title'].'</a>'."\n";
	 $html .= $child_menu;
	 $html .= $s.'		</li>'."\n";
      }
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'</div><!-- end #subhead -->'."\n";

      return $html;
   }

   public function category_selected_in_children($menu) {
      for ($i = 0, $iz = count($menu); $i < $iz; ++$i) {
	 if (CATEGORY_PARENT === $menu[$i]['parent']) return TRUE;
      }

      return FALSE;
   }

   public function create_submenu($menu) {
      $s = '						';

      $child_count = count($menu);
      if ($child_count < 1) return FALSE;

      if (DATE_GIVEN === FALSE) {
	 $date = '';
      } else if (DATE_START !== DATE_END) {
	 $date = 'date-'.DATE_START.'-'.DATE_END.'/';
      } else {
	 $date = 'date-'.DATE_START.'/';
      }

      $html = $s.'<ul class="submenu">'."\n";
      for ($i = 0; $i < $child_count; ++$i) {
	 $link_cat = HTML::url_friendly_category($menu[$i]['title']).'/';
	 $title = htmlspecialchars($menu[$i]['title']);
	 $html .= $s.'	<li><a href="'.ROOT_URL.$link_cat.$date.'">'.$title.'</a></li>'."\n";
      }
      $html .= $s.'</ul>'."\n";

      return $html;
   }

   public function url_friendly_category($category) {
      /* We have to urlencode twice due to mod_rewrite */
      return urlencode(urlencode(strtolower(str_replace(' ', '-', $category))));
   }

   public function datemenu($dates_with_events, $category='') {
      $s = '			';

      /* FIXME: This should be improved for better optimization */
      $current_dow = urlencode(get_current_day_of_week(CURRENT_TIME));
      $first_dow = urlencode(get_first_day_of_week(CURRENT_TIME));
      $last_dow  = urlencode(get_last_day_of_week(CURRENT_TIME));
      $first_donw = urlencode(get_first_day_of_week(CURRENT_TIME, 1)); //$last_dow + 1;
      $last_donw  = urlencode(get_last_day_of_week(CURRENT_TIME, 1)); //$last_dow + 7;
      $first_dom = urlencode(get_first_day_of_month($current_dow));
      $last_dom  = urlencode(get_last_day_of_month($first_dom));
      $first_donm = urlencode(get_first_day_of_month($current_dow, 1));
      $last_donm  = urlencode(get_last_day_of_month($first_donm));

      if (strlen($category) > 1) $category .= '/';

      if (DATE_START >= $current_dow && DATE_START <= $last_dow && DATE_END == $last_dow) {
	 $tw = 'current';
	 $nw = '';
	 $tm = '';
	 $nm = '';
      } else if (DATE_START >= $first_donw && DATE_START <= $last_donw && DATE_END <= $last_donw) {
	 $tw = '';
	 $nw = 'current';
	 $tm = '';
	 $nm = '';
      } else if (DATE_START >= $current_dow && DATE_START <= $last_dom && DATE_END == $last_dom) {
	 $tw = '';
	 $nw = '';
	 $tm = 'current';
	 $nm = '';
      } else if (DATE_START >= $first_donm && DATE_START <= $last_donm) {
	 $tw = '';
	 $nw = '';
	 $tm = '';
	 $nm = 'current';
      } else {
	 $tw = '';
	 $nw = '';
	 $tm = '';
	 $nm = '';
      }

      $html  = $s.'<div id="timeline" class="clearfix">'."\n";
      $html .= $s.'	<ul id="timeoptions">'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.$category.'date-'.$current_dow.'-'.$last_dow.'" class="'.$tw.'" id="time-thisweek">This Week</a></li>'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.$category.'date-'.$first_donw.'-'.$last_donw.'" class="'.$nw.'" id="time-nextweek">Next Week</a></li>'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.$category.'date-'.$current_dow.'-'.$last_dom.'" class="'.$tm.'" id="time-thismonth">This Month</a></li>'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.$category.'date-'.$first_donm.'-'.$last_donm.'" class="'.$nm.'" id="time-nextmonth">Next Month</a></li>'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= HTML::sub_datemenu(DATE_START, $category, $dates_with_events);
      $html .= $s.'	<form method="post" action="#">'."\n";
      $html .= $s.'		<p>'."\n";
      $html .= $s.'			<input type="hidden" name="rangestart" id="rangestart" />'."\n";
      $html .= $s.'			<input type="hidden" name="rangeend" id="rangeend" />'."\n";
      $html .= $s.'		<a href="#" id="rangeselect">Select Date Range</a>'."\n";
      $html .= $s.'		</p>'."\n";
      $html .= $s.'	</form>'."\n";
      $html .= $s.'</div><!-- end #timeline -->'."\n";

      return $html;
   }

   public function sub_datemenu($week_start, $category, $dates_with_events) {
      $s = '				';

      $year  = substr($week_start, 0, 4);
      $month = substr($week_start, 4, 2);
      $day   = substr($week_start, 6, 2);

      $current_date = get_current_day_of_week(CURRENT_TIME);

      $ts = urlencode(get_start_of_day($week_start));
      $first_dow = urlencode(get_first_day_of_week($ts));
      $last_dow = urlencode(get_last_day_of_week($ts));
      $ts = urlencode(get_timestamp_from_datestamp($first_dow));

      if ($first_dow < $current_date)
	 $first_dow = $current_date;

      if ($first_dow == DATE_START && $last_dow == DATE_END)
	 $class = 'current';
      else
	 $class = '';

      $html  = $s.'<ul id="suboptions">'."\n";
      $html .= $s.'	<li><a href="'.ROOT_URL.$category.'date-'.$first_dow.'-'.$last_dow.'" class="'.$class.'">Any Day</a></li>'."\n";
      $html .= $s.'	<li><a href="#" class="" id="prevdates">&lt;&lt;</a></li>'."\n";


      for ($h = -5; $h < 8; ++$h) {
	 for ($i = 0 + ($h * 7); $i < 7 + ($h * 7);  ++$i) {
	    $cur_ts = $ts + ($i * 86400);
	    $week[date("Ymd", $cur_ts)] = date('D', $cur_ts);;
	 }

	 if ($h === 0) $class = 'show'; else $class = 'hidden';
	 $html .= HTML::create_dates($week, $current_date, $category, $dates_with_events, $class);
	 unset($week);
      }

      
      $html .= $s.'	<li><a href="#" class="" id="futuredates">&gt;&gt;</a></li>'."\n";
      $html .= $s.'</ul>'."\n";

      return $html;
   }

   public function create_dates($week, $current_date, $category, $dates_with_events, $class = ' ') {
      $s = '					';
      $html = '';
      while ((list($date, $dow) = each($week)) !== FALSE) {

	 /* Highlight the selected date */
	 if ($date == DATE_START && $date == DATE_END) 
	    $aclass = ' current ';
	 else
	    $aclass = ' ';

	 if (isset($dates_with_events[$date])) {
	    $nofollow = '';
	 } else {
	    $nofollow = ' rel="nofollow" ';
	 }

	 $month_day = substr($date, 4, 2).'/'.substr($date, 6, 2);
	 if ($current_date > $date) {
	    /* If this date is in the past, no link */
	    $html .= $s.'<li class="selectdate '.$class.'"><span>'.$dow.'&nbsp;&nbsp;('.$month_day.')</span></li>'."\n";
	 } else {
	    /* If this date is not in the past, link */
	    $html .= $s.'<li class="selectdate '.$class.'"><a href="'.ROOT_URL.$category.'date-'.urlencode($date).'" class="'.$aclass.'" '.$nofollow.'>'.$dow.'&nbsp;&nbsp;('.$month_day.')</a></li>'."\n";
	 }
      }

      return $html;
   }

   public function comment($comment) {
      $cid   = htmlspecialchars($comment['id']);
      $cmsg  = convert_links(nl2br(htmlspecialchars($comment['comment'])));
      $cuser = htmlspecialchars($comment['username']);
      $age = htmlspecialchars(time_age($comment['time']));
      $ctime = htmlspecialchars($comment['time']);

      if ($comment['replies'] > 1)
	 $reply_txt = $comment['replies'].' replies &raquo;';
      else if ($comment['replies'] == 1)
	 $reply_txt = '1 reply &raquo;';

      $s = '							';
      $html  = $s.'<div class="comment" id="comment_'.$cid.'">'."\n";
      $html .= $s.'	<span class="username">'.$cuser.'</span>'."\n";
      $html .= $s.'	<span class="time">'.$age.'</span><br />'."\n";
      $html .= $s.'	<div class="message">'.$cmsg.'</div>'."\n";
      if (LOGGED_IN === TRUE)
	 $html .= $s.'	<span class="comment_footer"><a href="#" class="reply">reply</a></span>'."\n";
      if ($comment['replies'] > 0) 
	 $html .= $s.'	<span class="comment_footer"><a href="#" class="expand">'.$reply_txt.'</a></span>'."\n";
      $html .= $s.'</div>'."\n";

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
      $html .= $s.'</div> <!-- end #comment_container -->'."\n";

      return $html;
   }

   public function commentform($error, $id) {
      $s = '							';
      $html  = $s.'<form method="post" action="'.SROOT_URL.'comment.php" class="comment parent">'."\n";
      $html .= $s.'	<fieldset>'."\n";
      $html .= $s.'		<span class="errormsg">'.$error.'</span><br />'."\n";
      $html .= $s.'		<ol>'."\n";
      $html .= $s.'			<li><label for="add_comment">Comment</label><br />'."\n";
      $html .= $s.'				<input type="hidden" name="event_id" value="'.$id.'" id="event_id" />'."\n";
      $html .= $s.'				<textarea type="text" name="add_comment" id="add_comment" rows="5" cols="60"></textarea>'."\n";
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
      $likebox = HTML::likebox($event);
      $fb_like = HTML::FB_like($_SERVER['REQUEST_URI'], $event);
      $tweet   = HTML::Tweet($_SERVER['REQUEST_URI'], $event);

      $id          = htmlspecialchars($event['id']);
      //$title       = htmlspecialchars($event['title']);
      $time        = htmlspecialchars(date("l g:i A (F d, Y)", strtotime($event['time'])));
      $description = nl2br(htmlspecialchars($event['description']));
      $area        = htmlspecialchars($event['area']);
      $username    = htmlspecialchars($event['username']);

      $venue       = htmlspecialchars($event['venuename']);
      $address     = htmlspecialchars($event['address']);
      $city        = htmlspecialchars(ucwords($event['city']));
      $state       = htmlspecialchars(strtoupper($event['state']));
      $zip         = htmlspecialchars($event['zip']);

      $categories = '';
      for ($i = 0, $iz = count($event['category']); $i < $iz; ++$i) {
	 if (strlen($event['category'][$i]['title']) < 1) continue;
	 if (strlen($categories) > 0) $categories .= ', ';

	 $ct = HTML::url_friendly_category($event['category'][$i]['title']);
	 $categories .= '<a href="'.ROOT_URL.strtolower(str_replace(' ', '-', $ct)).'">';
	 $categories .= $ct;
	 $categories .= '</a>';
      }

      $s = '							';
      $html  = $s.'<div id="event_details">'."\n";
      $html .= $s.'	<h3 class="subtitle" id="time">'.$time.'</h3>'."\n";
      $html .= $s.'	<h3 class="subtitle" id="venue">'.$venue.'</h3>'."\n";
      $html .= $s.'	<h3 class="subtitle" id="address">'.$address.'</h3>'."\n";
      $html .= $s.'	<h3 class="subtitle" id="citystatezip">'.$city.', '.$state.' '.$zip.'</h3><br />'."\n";
      $html .= $likebox;
      $html .= $s.'	<span id="description">'.$description.'</span><br />'."\n";
      $html .= $s.'	<br />'."\n";
      $html .= $s.'	<h6 class="footer">Submitted by ( <span id="username">'.$username.'</span> ) to &lt; '.$categories.' &gt;</h6>'."\n";
      $html .= $s.'	<br />'."\n";
      $html .= $s.'	<h6 class="footer">Share this event</h6>'."\n";
      $html .= $fb_like;
      $html .= $tweet;
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

   public function pagination($total) {
      $s = '					';

      if (strlen(CATEGORY_TITLE) > 0) {
	 $cat = HTML::url_friendly_category(CATEGORY_TITLE).'/';
      } else {
	 $cat = '';
      }

      if (DATE_GIVEN === FALSE) {
	 $date = '';
      } else if (DATE_START !== DATE_END) {
	 $date = urlencode('date-'.DATE_START.'-'.DATE_END).'/';
      } else {
	 $date = urlencode('date-'.DATE_START).'/';
      }

      $mid_page_h = ceil(MAX_PAGES / 2);
      $mid_page_l = floor(MAX_PAGES / 2);
      if ($total <= MAX_PAGES) {
	 /* Ex: Page 3 out of 5 */
	 $start = 1;
	 $end = $total;
      } elseif (PAGE >= $mid_page_h && $total - PAGE >= $mid_page_h) {
	 /* Ex: Page 10 out of 20 */
	 $start = PAGE - $mid_page_l;
	 $end = $start + MAX_PAGES - 1;
      } elseif (PAGE >= $mid_page_h && $total - PAGE < $mid_page_h) {
	 /* Ex: Page 18 out of 20 */
	 $start = $total - MAX_PAGES + 1;
	 $end = $total;
      } elseif (PAGE < $mid_page_h && $total - PAGE >= $mid_page_h) {
	 /* Ex: Page 3 out of 20 */
	 $start = 1;
	 $end = $start + MAX_PAGES - 1;
      } else {
	 /* shouldn't get here */
	 $start = 1;
	 $end = $total;
      }

      $html  = $s.'<div class="pagination">'."\n";
      $html .= $s.'	<ul class="clearfix">'."\n";
      if (PAGE !== 1) $html .= $s.'		<li><a href="'.ROOT_URL.$cat.$date.'page'.urlencode(PAGE-1).'">&laquo; prev</a></li>'."\n";

      for ($i = $start; $i <= $end; ++$i) {
	 if ($i == PAGE)
	    $html .= $s.'		<li><span class="current">'.$i.'</span></li>'."\n";
	 else
	    $html.= $s.'		<li><a href="'.ROOT_URL.$cat.$date.'page'.urlencode($i).'">'.$i.'</a></li>'."\n";
      }

      if (PAGE < $total) 
	 $html .= $s.'		<li><a href="'.ROOT_URL.$cat.$date.'page'.urlencode(PAGE + 1).'">next &raquo;</a></li>'."\n";

      $html .= $s.'	</ul>'."\n";
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function modal_login() {
      $s = '			';
      $html  = $s.'<div id="modal-login" class="modal">'."\n";
      $html .= $s.'	<a href="#" class="close_button"><img src="/images/icons/ico-close-button.png" alt="close" class="close_modal" /></a>'."\n";
      $html .= $s.'	<h2 class="title">Log in</h2>'."\n";
      $html .= HTML::login_form('modalform', '');
      $html .= $s.'</div><!-- end #modal-login -->'."\n";

      return $html;
   }

   public function modal_signup() {
      $s = '			';
      $html  = $s.'<div id="modal-signup" class="modal">'."\n";
      $html .= $s.'	<a href="#" class="close_button"><img src="/images/icons/ico-close-button.png" alt="close" class="close_modal" /></a>'."\n";
      $html .= $s.'	<h2 class="title">Create a New Account</h2>'."\n";
      $html .= HTML::signup_form('modalform', '');
      $html .= $s.'</div><!-- end #modal-signup -->'."\n";

      return $html;
   }

   public function modal_change_password() {
      $s = '			';
      $html  = $s.'<div id="modal-change-password" class="modal">'."\n";
      $html .= $s.'	<a href="#" class="close_button"><img src="/images/icons/ico-close-button.png" alt="close" class="close_modal" /></a>'."\n";
      $html .= $s.'	<h2 class="title">Change My Password</h2>'."\n";
      $html .= HTML::change_password_form('modalform', '');
      $html .= $s.'</div><!-- end #modal-change-password -->'."\n";

      return $html;
   }

   public function modal_forgot_password() {
      $s = '			';
      $html  = $s.'<div id="modal-forgot-password" class="modal">'."\n";
      $html .= $s.'	<a href="#" class="close_button"><img src="/images/icons/ico-close-button.png" alt="close" class="close_modal" /></a>'."\n";
      $html .= $s.'	<h2 class="title">Reset My Password</h2>'."\n";
      $html .= HTML::forgot_password_form('modalform', '');
      $html .= $s.'</div><!-- end #modal-forgot-password -->'."\n";

      return $html;
   }

   public function modal_message() {
      $s = '			';
      $html  = $s.'<div id="modal-message" class="modal">'."\n";
      $html .= $s.'	<a href="#" class="close_button"><img src="/images/icons/ico-close-button.png" alt="close" class="close_modal" /></a>'."\n";
      $html .= $s.'	<h2 class="title">Important Message</h2>'."\n";
      $html .= $s.'	<span class="modal-message"></span>'."\n";
      $html .= $s.'</div><!-- end #modal-message -->'."\n";

      return $html;
   }

   public function modal_attendees() {
      $s = '			';
      $html  = $s.'<div id="modal-attendees" class="modal">'."\n";
      $html .= $s.'	<a href="#" class="close_button"><img src="/images/icons/ico-close-button.png" alt="close" class="close_modal" /></a>'."\n";
      $html .= $s.'	<h2 class="title">People Attending this Event</h2>'."\n";
      $html .= $s.'	<ul class="attendees" id="attendees_1">'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'	<ul class="attendees" id="attendees_2">'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'	<ul class="attendees" id="attendees_3">'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= $s.'	<div class="clearfix"></div>'."\n";
      $html .= $s.'	<div class="attendees">'."\n";
      $html .= $s.'		<span class="fml"><a href="#">Previous</a></span>'."\n";
      $html .= $s.'		<span class="fmr"><a href="#">Next</a></span>'."\n";
      $html .= $s.'	</div>'."\n";
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function login_form($class='', $error='') {
      $s = '				';
      $html = $s.'<form class="modalform" id="login-form" action="'.SROOT_URL.'login" method="post">'."\n";
      $html .= $s.'	<input type="text" name="ns" class="nospam" />'."\n";
      $html .= $s.'	<input type="text" name="ns2" class="nospam" value="ap" />'."\n";
      $html .= $s.'	<fieldset>'."\n";
      $html .= $s.'		<ol>'."\n";
      $html .= $s.'			<li><label for="login-username">Username :</label><input type="text" id="login-username" name="username" class="textfield" /></li>'."\n";
      $html .= $s.'			<li><label for="login-password">Password :</label><input type="password" id="login-password" name="password" class="textfield" />'."\n";
      $html .= $s.'				<span class="formnote fml"><a href="'.SROOT_URL.'signup" id="link-signup">Create an Account</a></span>'."\n";
      $html .= $s.'				<span class="formnote fmr"><a href="'.SROOT_URL.'forgot_password" id="forgot_my_password">forgot password?</a></span>'."\n";
      $html .= $s.'			</li>'."\n";
      $html .= $s.'		</ol>'."\n";
      $html .= $s.'		<button type="submit" class="btn-submit ib" id="btn-loginsubmit">Log In</button>'."\n";
      $html .= $s.'	</fieldset>'."\n";
      $html .= $s.'</form>'."\n";

      return $html;
   }

   public function signup_form($class='', $error='') {
      /* Set the default values in case the users first try was an error */
      if (isset($_POST['username'])) $username = $_POST['username'];
      else $username = '';
      if (isset($_POST['password'])) $password = $_POST['password'];
      else $password = '';
      if (isset($_POST['password2'])) $password2 = $_POST['password2'];
      else $password2 = '';
      if (isset($_POST['email'])) $email = $_POST['email'];
      else $email = '';
      if (isset($_POST['first_name'])) $first_name = $_POST['first_name'];
      else $first_name = '';
      if (isset($_POST['last_name'])) $last_name = $_POST['last_name'];
      else $last_name = '';

      $s = '				';
      $html  = $s.'<form class="'.$class.'" id="signup-form" action="'.SROOT_URL.'signup" method="post">'."\n";
      $html .= $s.'	<input type="text" name="ns" class="nospam" />'."\n";
      $html .= $s.'	<input type="text" name="ns2" class="nospam" value="ap" />'."\n";
      $html .= $s.'	<fieldset>'."\n";
      $html .= $s.'		<span class="errormsg">'.$error.'</span>'."\n";
      $html .= $s.'		<ol>'."\n";
      $html .= $s.'             <li><span class="formmsg">Required *</span></li>'."\n";
      $html .= $s.'			<li><label for="signup-username">Desired Username* :</label><input type="text" id="signup-username" name="username" class="textfield" value="'.$username.'"/></li>'."\n";
      $html .= $s.'			<li><label for="signup-email">Email address* :</label><input type="text" id="signup-email" name="email" class="textfield" value="'.$email.'"/></li>'."\n";
      $html .= $s.'			<li><label for="signup-password">Password* :</label><input type="password" id="signup-password" name="password" class="textfield" value="'.$password.'"/></li>'."\n";
      $html .= $s.'			<li><label for="signup-password2">Confirm Password* :</label><input type="password" id="signup-password2" name="password2" class="textfield" value="'.$password2.'"/></li>'."\n";
      $html .= $s.'			<li><label for="signup-first-name">First Name :</label><input type="text" id="signup-first-name" name="first_name" class="textfield" value="'.$first_name.'"/></li>'."\n";
      $html .= $s.'			<li><label for="signup-last-name">Last Name :</label><input type="text" id="signup-last-name" name="last_name" class="textfield" value="'.$last_name.'"/></li>'."\n";
      $html .= $s.'		</ol>'."\n";
      $html .= $s.'		<button type="submit" class="btn-submit ib" id="btn-signupsubmit">Register</button>'."\n";
      $html .= $s.'	</fieldset>'."\n";
      $html .= $s.'</form>'."\n";

      return $html;
   }

   public function change_password_form($class='', $error='') {
      $s = '				';
      $html  = $s.'<form class="'.$class.'" id="change_password_form" action="'.SROOT_URL.'change_password" method="post">'."\n";
      $html .= $s.'	<input type="text" name="ns" class="nospam" />'."\n";
      $html .= $s.'	<input type="text" name="ns2" class="nospam" value="ap" />'."\n";
      $html .= $s.'	<fieldset>'."\n";
      $html .= $s.'		<span class="errormsg">'.$error.'</span>'."\n";
      $html .= $s.'		<ol>'."\n";
      $html .= $s.'			<li><label for="change_password">New Password :</label><input type="password" id="change_password" name="password" class="textfield" /></li>'."\n";
      $html .= $s.'			<li><label for="change_password2">Confirm Password :</label><input type="password" id="change_password2" name="password2" class="textfield" /></li>'."\n";
      $html .= $s.'		</ol>'."\n";
      $html .= $s.'		<button type="submit" class="btn-submit ib" id="btn_submit_password">Change Password</button>'."\n";
      $html .= $s.'	</fieldset>'."\n";
      $html .= $s.'</form>'."\n";

      return $html;
   }

   public function forgot_password_form($class='', $error='') {
      $s = '				';
      $html = $s.'<form class="modalform" id="forgot-password-form" action="'.SROOT_URL.'forgot_password" method="post">'."\n";
      $html .= $s.'	<input type="text" name="ns" class="nospam" />'."\n";
      $html .= $s.'	<input type="text" name="ns2" class="nospam" value="ap" />'."\n";
      $html .= $s.'	<fieldset>'."\n";
      $html .= $s.'		<span class="errormsg">'.$error.'</span><br />'."\n";
      $html .= $s.'		<ol>'."\n";
      $html .= $s.'			<li><label for="login-username">Username :</label><input type="text" id="forgot-username" name="username" class="textfield" /></li>'."\n";
      $html .= $s.'			<li><label for="login-email">Email :</label><input type="text" id="forgot-email" name="email" class="textfield" />'."\n";
      $html .= $s.'			</li>'."\n";
      $html .= $s.'		</ol>'."\n";
      $html .= $s.'		<button type="submit" class="btn-submit ib" id="btn-resetpassword">Reset Password</button>'."\n";
      $html .= $s.'	</fieldset>'."\n";
      $html .= $s.'</form>'."\n";

      return $html;
   }

   public function submit_form($cat_opts, $error='') {
      /* Set defaults in case the users first try an error */
      /* Fuck ternary operators (they dont fit either, i tried ;( ) */
      if (isset($_POST['title'])) $title = $_POST['title']; 
      else $title = '';
      if (isset($_POST['date'])) $date = $_POST['date']; 
      else $date = 'MM/DD/YYYY';
      if (isset($_POST['time'])) $time = $_POST['time']; 
      else $time = 'HH:MM';
      if (isset($_POST['meridian'])) $meridian = $_POST['meridian']; 
      else $meridian = '';
      if (isset($_POST['address'])) $address = $_POST['address']; 
      else $address = '';
      if (isset($_POST['zip'])) $zip = $_POST['zip']; 
      else $zip = '';
      if (isset($_POST['url'])) $url = $_POST['url']; 
      else $url = '';
      if (isset($_POST['description'])) $description = $_POST['description']; 
      else $description = '';
      if (isset($_POST['category'])) $category = $_POST['category']; 
      else $category = '';
      if (isset($_POST['venue'])) $venue = $_POST['venue']; 
      else $venue = '';
      if (isset($_POST['venuephone'])) $venuephone = $_POST['venuephone']; 
      else $venuephone = '';


      /* Select the default category */
      $cat_opts = str_replace('value="'.$category.'"', 'value="'.$category.'" selected', $cat_opts);

      /* Select the default meridian */
      if ($meridian === 'am') {
	 $pm_sel = '';
	 $am_sel = ' selected ';
      } else {
	 $pm_sel = ' selected ';
	 $am_sel = '';
      }
      
      $s = '   ';
      $html  = $s.'<div id="submitform" class="submitform">'."\n";
      $html .= $s.'   <form method="post" action="'.SROOT_URL.'post_event" class="fullform">'."\n";
      $html .= $s.'	<input type="text" name="ns" class="nospam" />'."\n";
      $html .= $s.'	<input type="text" name="ns2" class="nospam" value="ap" />'."\n";
      $html .= $s.'   <fieldset>'."\n";
      $html .= $s.'      <span class="errormsg">'.$error.'</span><br />'."\n";
      $html .= $s.'      <ol>'."\n";
      $html .= $s.'         <li><span class="formmsg">Required *</span></li>'."\n";
      $html .= $s.'         <li><label for="title">Title* :</label>'."\n";
      $html .= $s.'            <input type="text" name="title" id="title" class="textfield" value="'.$title.'"/>'."\n";
      $html .= $s.'         </li>'."\n";
      //$html .= $s.'<!--'."\n";
      //$html .= $s.'         <li><label for="tags">Tags :</label>'."\n";
      //$html .= $s.'            <input type="text" name="tags" id="tags" class="textfield"/>'."\n";
      //$html .= $s.'         </li>'."\n";
      //$html .= $s.'-->'."\n";
      $html .= $s.'         <li><label for="date">Date* :</label>'."\n";
      $html .= $s.'            <input type="text" name="date" id="date" class="textfield defaultvalue" value="'.$date.'"/>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li><label for="time">Time* :</label>'."\n";
      $html .= $s.'            <input type="text" name="time" id="time" class="textfield time defaultvalue" value="'.$time.'""/>'."\n";
      $html .= $s.'            <select name="meridian" id="meridian">'."\n";
      $html .= $s.'               <option value="pm" '.$pm_sel.'>PM</option>'."\n";
      $html .= $s.'               <option value="am" '.$am_sel.'>AM</option>'."\n";
      $html .= $s.'            </select>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li><label for="category">Category* :</label>'."\n";
      $html .= $s.'            <select name="category" id="category" class="textfield">'.$cat_opts.'</select>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li><label for="venue">Venue Name* :</label>'."\n";
      $html .= $s.'            <input type="text" name="venue" id="venue" class="textfield" value="'.$venue.'"/>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li><label for="address">Street Address :</label>'."\n";
      $html .= $s.'            <input type="text" name="address" id="address" class="textfield" title="This is for the street address.  You do not need to include a city or state" value="'.$address.'"/>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li><label for="zip">Zip* :</label>'."\n";
      $html .= $s.'            <input type="text" name="zip" id="zip" class="textfield" value="'.$zip.'"/>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li><label for="venue">Venue Phone :</label>'."\n";
      $html .= $s.'            <input type="text" name="venuephone" id="venuephone" class="textfield" value="'.$venuephone.'"/>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li><label for="url">Link URL :</label>'."\n";
      $html .= $s.'            <input type="text" name="url" id="url" class="textfield" value="'.$url.'"/>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li><label for="description">Description* :</label>'."\n";
      $html .= $s.'            <textarea name="description" id="description" rows="5" cols="20" class="textfield">'.$description.'</textarea>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         <li class="submit_line">'."\n";
      $html .= $s.'            <label>&nbsp; </label>'."\n";
      $html .= $s.'            <button type="submit" class="submitter">Create</button>'."\n";
      $html .= $s.'         </li>'."\n";
      $html .= $s.'         </ol>'."\n";
      $html .= $s.'      </fieldset>'."\n";
      $html .= $s.'   </form>'."\n";
      $html .= $s.'</div>'."\n";
      return $html;
   }

   public function create_css_links($CSS) {
      $s = '	';

      if ( ! is_array($CSS)) $CSS = array($CSS);
      for ($i = 0, $iz = count($CSS), $csslinks = ''; $i < $iz; ++$i) {
	 if (strlen(trim($CSS[$i])) < 1) continue;
	 $cssfile = $CSS[$i];
	 if (substr($cssfile, 0, 5) !== 'http:' && substr($cssfile, 0, 6) !== 'https:')
	    $cssfile = '/css/'.$cssfile;
	 $csslinks .= $s.'<link rel="stylesheet" type="text/css" href="'.$cssfile.'" />'."\n";
      }

      return $csslinks;
   }

   public function create_js_links($JS) {
      $s = '	';
      if ( ! is_array($JS)) $JS = array($JS);
      for ($i = 0, $iz = count($JS), $jslinks = ''; $i < $iz; ++$i) {
	 if (strlen(trim($JS[$i])) == '') continue;
	 $jsfile = $JS[$i];
	 if (substr($jsfile, 0, 5) !== 'http:' && substr($jsfile, 0, 6) !== 'https:')
	    $jsfile = '/js/'.$jsfile;
	 $jslinks .= $s.'<script type="text/javascript" src="'.$jsfile.'"></script>'."\n";
      }

      return $jslinks;
   }

   public function header($title, $csslinks, $jslinks, $menu, $logged_in=FALSE) {
      $s = '			';
      if ($logged_in === TRUE) {
	 $status  = $s.'<ul id="loggedin">'."\n";
	 $status .= $s.'	<li id="btn-submit"><a href="'.ROOT_URL.'post_event">Post Event</a></li>'."\n";
	 $status .= $s.'	<li id="btn-profile"><a href="'.SROOT_URL.'profile">Profile</a></li>'."\n";
	 $status .= $s.'	<li id="btn-logout"><a href="'.ROOT_URL.'logout">Logout</a></li>'."\n";
	 $status .= $s.'</ul>';
      } else {
	 $status  = $s.'<ul id="loggedout">'."\n";
	 $status .= $s.'	<li id="btn-login"><a href="'.SROOT_URL.'login">Login</a></li>'."\n";
	 $status .= $s.'	<li id="btn-signup"><a href="'.SROOT_URL.'signup">Sign up</a></li>'."\n";
	 $status .= $s.'</ul>';
      }

      if (strlen(CATEGORY_TITLE) > 0) $cat = ','.CATEGORY_TITLE;
      else $cat = '';
      if (strlen(CATEGORY_PARENT_TITLE) > 0) $catp = ','.CATEGORY_PARENT_TITLE;
      else $catp = '';
      /* FIXME: Need to add area info */

      $meta_d = 'AreaPilot is a social effort to find out what\'s happening in your local area and vote for the ones you like.  Be your own promoter and post events for others to attend.';
      $meta_k = 'Post events,Search events,Find events,Whats happening,Promoter,Local,Advertise,Tonight,Something to do,Listings'.$cat.$catp;

      $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"'."\n";
      $html .= '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
      $html .= ''."\n";
      $html .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
      $html .= '<head>'."\n";
      $html .= '	<meta http-equiv="content-type" content="text/html; charset=utf-8" />'."\n";
      $html .= '	<meta name="description" content="'.$meta_d.'" />'."\n";
      $html .= '	<meta name="keywords" content="'.$meta_k.'" />'."\n";
      $html .= '	<title>'.$title.'</title>'."\n";
      $html .= $csslinks."\n";
      $html .= $jslinks."\n";
      $html .= HTML::google_analytics();
      $html .= '</head>'."\n";
      $html .= '<body>'."\n";
      $html .= '	<div id="container">'."\n";
      $html .= '		<div id="header">'."\n";
      $html .= '			<h1 id="logo"><a href="'.ROOT_URL.'">AreaPilot.com : Find popular events in your area</a></h1>'."\n";
      $html .= $menu."\n";
      $html .= $status."\n";
      $html .= '		</div><!-- end #header -->'."\n";
      $html .= '		<div id="main">'."\n";

      return $html;
   }

   public function map($event) {
      $s = '						';
      
      $address  = $event['address'].', ';
      $address .= $event['city'].', ';
      $address .= $event['state'].' ';
      $address .= $event['zip'];
      $address = urlencode($address);

      $img = '<img src="http://maps.google.com/maps/api/staticmap?center='.$address.'&zoom=14&size=300x200&format=JPEG&sensor=false&markers=color:blue|'.$address.'" alt="'.$address.'" />';
      $map = '<div id="map_canvas">'.$img.'</div>'."\n";

      return $map;
   }

   public function google_analytics() {
      if (PRODUCTION === FALSE) return '';

      $s = '	';
      $html  = $s.'<script type="text/javascript">'."\n";;
      $html .= $s.'	var _gaq = _gaq || [];'."\n";;
      $html .= $s.'	_gaq.push([\'_setAccount\', \'UA-22030249-1\']);'."\n";
      $html .= $s.'	_gaq.push([\'_setDomainName\', \'.areapilot.com\']);'."\n";
      $html .= $s.'	_gaq.push([\'_trackPageview\']);'."\n";
      $html .= $s.'	(function() {'."\n";
      $html .= $s.'		var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;'."\n";
      $html .= $s.'		ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';'."\n";
      $html .= $s.'		var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);'."\n";
      $html .= $s.'	})();'."\n";
      $html .= $s.'</script>'."\n";

      return $html;
   }

   public function FB_like($uri, $event) {
      $s = '					';
      $url = urlencode('http://'.$_SERVER['HTTP_HOST'].$uri);
      $ref = urlencode($event['id']);

      $html = $s.'<iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21&amp;ref='.$ref.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true" id="iframe_'.$ref.'"></iframe>'."\n";
      return $html;
   }

   public function Tweet($uri, $event) {
      $s = '					';
      $url = urlencode('http://'.$_SERVER['HTTP_HOST'].$uri);
      $date = $event['time'];
      $venue = ''; //FIXME
      $text = urlencode($event['title'].' on '.$date.' @ '.$venue);
      $id = htmlspecialchars($event['id']);
      
      $html  = $s.'<a id="tweet_'.$id.'" class="tweetit" href="http://twitter.com/share?url='.$url.'&text='.$text.'&count=horizontal" target="_blank" title="Share this event on Twitter">'."\n";
      $html .= '<img src="/images/icons/tweetn.png" alt="Tweet" />'."\n";
      $html .= '</a>'."\n";

      return $html;
   }

   public function message($msg) {
      $html  = '<span class="message">'."\n";
      $html .= '	<br />'."\n";
      $html .= '	'.$msg."\n";
      $html .= '	<br />'."\n";
      $html .= '</span>'."\n";

      return $html;
   }
}

?>

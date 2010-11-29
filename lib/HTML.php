<?php

define('HTMLCLASS', 1);

class HTML {

   public function entry($event, $count) {
      $s = '						';

      $id          = htmlspecialchars($event['id']);
      $uri_title   = htmlspecialchars($event['uri_title']);
      $title       = htmlspecialchars($event['title']);
      $description = htmlspecialchars(substr($event['description'], 0, 400));
      $time        = htmlspecialchars(time_convert_24_to_12(substr($event['time'], 11)));
      $area        = htmlspecialchars($event['area']);
      $city        = htmlspecialchars(ucwords($event['city']));
      $username    = htmlspecialchars($event['username']);
      $attendance  = htmlspecialchars($event['attendance']);

      $time = strtolower(substr($time, 0, 4).substr($time, 8));

      if ($attendance > 1)          $attendance .= ' People Attending';
      else if ($attendance === '1') $attendance .= ' Person Attending';
      else                          $attendance =  ' 0 People Attending';

      $html  = $s.'<div class="entry clearfix" id="entry_'.$count.'">'."\n";
      $html .= HTML::likebox($event);
      $html .= $s.'	<h3><a href="'.ROOT_URL.$uri_title.'">'.$title.'</a></h3>'."\n";
      $html .= $s.'	<h4><span>Location : </span><a href="#">'.$city.'</a> &nbsp;&#124;&nbsp; <span>Time : </span>'.$time.'</h4>'."\n";
      $html .= $s.'	<div class="description"><p>'.$description.'</p>'."\n";
      $html .= $s.'	</div><!-- end .description -->'."\n";
      $html .= $s.'	<ul class="actionlinks">'."\n";
      $html .= $s.'		<li><a href="vote.php?id='.$id.'&t=e&a=a&r=h" name="'.$id.'" class="attendthis">Attend This Event</a></li>'."\n";
      $html .= $s.'		<li><a href="#" class="attending">'.$attendance.'</a></li>'."\n";
      $html .= $s.'		<li><a href="'.ROOT_URL.$uri_title.'" class="commentsnum"><!--___COMMENT_COUNT___--></a></li>'."\n";
      $html .= $s.'	</ul>'."\n";
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
      $s = '							';
      $html  = $s.'<div class="likebox">'."\n";
      $html .= $s.'	<span class="numlikes" id="numlikes_'.$id.'">'.$rating.'</span>'."\n";
      $html .= $s.'	<span class="xtra">'.$liketext.'</span>'."\n";
      $html .= $s.'	<a href="vote.php?id='.$id.'&t=e&a=l&r=h" name="'.$id.'" class="likeit">I Like It</a>'."\n";
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
      $html .= $s.'	<div id="maincol">'."\n";
      $html .= $s.'		<div id="posts">'."\n";
      $html .= $s.'			<h2 id="title">'.$title.'</h2>'."\n";

      return $html;
   }

   public function body_footer() {
      $s = '			';
      $html  = $s.'		</div><!-- end #posts -->'."\n";
      $html .= $s.'	</div><!-- end #maincol -->'."\n";
      $html .= $s.'</div><!-- end #inner -->'."\n";

      return $html;
   }

   public function event($title, $eventdetails, $venuedetails, $map, $commentlist) {
      $s = '			';

      $html  = HTML::body_header($title);
      $html .= $s.'			<div id="eventdetailswrapper">'."\n";
      $html .= $eventdetails;
      $html .= $s.'			<br />'."\n";
      $html .= $venuedetails;
      $html .= $s.'			<br />'."\n";
      $html .= $s.'			<br />'."\n";
      $html .= $s.'			<br />'."\n";
      $html .= $s.'			</div>'."\n";
      $html .= $s.'			<div id="eventmapwrapper">'."\n";
      $html .= $map;
      $html .= $s.'			</div>'."\n";
      $html .= $s.'			<br class="clearfix" />'."\n";
      $html .= $commentlist;
      $html .= HTML:: body_footer();

      return $html;
   }

   public function sidecol($list) {
      $s = '				';

      if (strlen(CATEGORY_PARENT_TITLE) > 1) $category = ' in "'.CATEGORY_PARENT_TITLE.'"';
      else if (strlen(CATEGORY_TITLE) > 1) $category = ' in "'.CATEGORY_TITLE.'"';
      else $category = '';

      $html  = '<div id="sidecol">'."\n";
      $html .= '	<div class="sidebox" id="popincategory">'."\n";
      $html .= '		<h2>Popular '.$category.'</h2>'."\n";
      $html .= '		<div class="minievents">'."\n";

      /* Create entries for the sidecol */
      for ($i = 0, $iz = count($list); $i < $iz; ++$i) {
	 $html .= '			<a href="#" class="minievent clearfix"><span class="numlikes">'.$list[$i]['rating'].'</span><span class="title">'.$list[$i]['title'].'</span></a>'."\n";
      }

      $html .= '		</div>'."\n";
      $html .= '	</div><!-- end #popincategory -->'."\n";
      $html .= '</div><!-- end #sidecol -->'."\n";

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

      /* Create category menu */
      for ($i = 0, $iz = count($menu); $i < $iz; ++$i) {

	 $child_menu = HTML::create_submenu($menu[$i]['children']);
	 if ($child_menu !== FALSE) $has_menu = ' hasmenu '; else $has_menu = ' ';

	 if ( CATEGORY_ID === $menu[$i]['parent']['id'] || HTML::category_selected_in_children($menu[$i]['children']) === TRUE) {
	    $class = 'current';
	 } else {
	    $class = ' ';
	 }

	 $link = HTML::url_friendly_category($menu[$i]['parent']['title']);
	 $html .= $s.'		<li class="'.$has_menu.'"><a class="'.$class.'" href="'.ROOT_URL.$link.'">'.$menu[$i]['parent']['title'].'</a>'."\n";
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

      $html = $s.'<ul class="submenu">'."\n";
      for ($i = 0; $i < $child_count; ++$i) {
	 $link_cat = HTML::url_friendly_category($menu[$i]['title']);
	 $html .= $s.'	<li><a href="'.ROOT_URL.$link_cat.'">'.$menu[$i]['title'].'</a></li>'."\n";
      }
      $html .= $s.'</ul>'."\n";

      return $html;
   }

   public function url_friendly_category($category) {
      return strtolower(str_replace(' ', '_', $category));
   }

   public function datemenu($category='') {
      $s = '			';

      /* FIXME: This should be improved for better optimization */
      $current_dow = get_current_day_of_week(CURRENT_TIME);
      $first_dow = get_first_day_of_week(CURRENT_TIME);
      $last_dow  = get_last_day_of_week(CURRENT_TIME);
      $first_donw = get_first_day_of_week(CURRENT_TIME, 1); //$last_dow + 1;
      $last_donw  = get_last_day_of_week(CURRENT_TIME, 1); //$last_dow + 7;
      $first_dom = get_first_day_of_month($current_dow);
      $last_dom  = get_last_day_of_month($first_dom);
      $first_donm = get_first_day_of_month($current_dow, 1);
      $last_donm  = get_last_day_of_month($first_donm);

      if (strlen($category) > 1) $category .= '/';

      if (DATE_START >= $first_dow && DATE_START <= $last_dow) {
	 $tw = 'current';
	 $nw = '';
	 $tm = '';
	 $nm = '';
      } else if (DATE_START >= $first_donw && DATE_START <= $last_donw && DATE_END <= $last_donw) {
	 $tw = '';
	 $nw = 'current';
	 $tm = '';
	 $nm = '';
      } else if (DATE_START >= $first_dom && DATE_START <= $last_dom) {
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
      $html .= $s.'		<li><a href="/'.$category.'date-'.$first_dow.'-'.$last_dow.'" class="'.$tw.'" id="time-thisweek">This Week</a></li>'."\n";
      $html .= $s.'		<li><a href="/'.$category.'date-'.$first_donw.'-'.$last_donw.'" class="'.$nw.'" id="time-nextweek">Next Week</a></li>'."\n";
      $html .= $s.'		<li><a href="/'.$category.'date-'.$first_dom.'-'.$last_dom.'" class="'.$tm.'" id="time-thismonth">This Month</a></li>'."\n";
      $html .= $s.'		<li><a href="/'.$category.'date-'.$first_donm.'-'.$last_donm.'" class="'.$nm.'" id="time-nextmonth">Next Month</a></li>'."\n";
      $html .= $s.'	</ul>'."\n";
      $html .= HTML::sub_datemenu(DATE_START, $category);
      $html .= $s.'	<a href="#" id="rangeselect">Select Date Range</a>'."\n";
      $html .= $s.'</div><!-- end #timeline -->'."\n";

      return $html;
   }

   public function sub_datemenu($week_start, $category) {
      $s = '				';

      if (strlen($category) > 1) $category .= '/';

      $year  = substr($week_start, 0, 4);
      $month = substr($week_start, 4, 2);
      $day   = substr($week_start, 6, 2);

      $current_date = get_current_day_of_week(CURRENT_TIME);

      $ts = get_start_of_day($week_start);
      $first_dow = get_first_day_of_week($ts);
      $last_dow = get_last_day_of_week($ts);
      $ts = get_timestamp_from_datestamp($first_dow);

      $html  = $s.'<ul id="suboptions">'."\n";
      $html .= $s.'	<li><a href="/'.$category.'date-'.$first_dow.'-'.$last_dow.'" class="current">Any Day</a></li>'."\n";
      $html .= $s.'	<li><a href="#" class="" id="prevdates">&lt;&lt;</a></li>'."\n";


      for ($h = -5; $h < 8; ++$h) {
	 for ($i = 0 + ($h * 7); $i < 7 + ($h * 7);  ++$i) {
	    $cur_ts = $ts + ($i * 86400);
	    $week[date("Ymd", $cur_ts)] = date('D', $cur_ts);;
	 }

	 if ($h === 0) $class = 'show'; else $class = 'hidden';
	 $html .= HTML::create_dates($week, $current_date, $category, $class);
	 unset($week);
      }

      
      $html .= $s.'	<li><a href="#" class="" id="futuredates">&gt;&gt;</a></li>'."\n";
      $html .= $s.'</ul>'."\n";

      return $html;
   }

   public function create_dates($week, $current_date, $category, $class = ' ') {
      $s = '					';
      $html = '';
      while ((list($date, $dow) = each($week)) !== FALSE) {
	 $month_day = substr($date, 4, 2).'/'.substr($date, 6, 2);
	 if ($current_date > $date) {
	    $html .= $s.'<li class="selectdate '.$class.'"><span>'.$dow.'&nbsp;&nbsp;('.$month_day.')</span></li>'."\n";
	 } else {
	    $html .= $s.'<li class="selectdate '.$class.'"><a href="'.$category.'/date-'.$date.'">'.$dow.'&nbsp;&nbsp;('.$month_day.')</a></li>'."\n";
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
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function commentform($error, $id) {
      $s = '							';
      $html  = $s.'<form method="post" action="/comment.php" class="comment parent">'."\n";
      $html .= $s.'	<fieldset>'."\n";
      $html .= $s.'		<span class="errormsg">'.$error.'</span><br />'."\n";
      $html .= $s.'		<ol>'."\n";
      $html .= $s.'			<li><label for="add_comment">Title</label>'."\n";
      $html .= $s.'				<input type="hidden" name="event_id" value="'.$id.'" id="event_id" />'."\n";
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
      $likebox = HTML::likebox($event);
      $id          = htmlspecialchars($event['id']);
      //$title       = htmlspecialchars($event['title']);
      $time        = htmlspecialchars(date("l g:i A (F d, Y)", strtotime($event['time'])));
      $description = nl2br(htmlspecialchars($event['description']));
      $area        = htmlspecialchars($event['area']);
      $username    = htmlspecialchars($event['username']);

      $event_name  = htmlspecialchars($event['venuename']);
      $address     = htmlspecialchars($event['address']);
      $city        = htmlspecialchars(ucwords($event['city']));
      $state       = htmlspecialchars(strtoupper($event['state']));
      $zip         = htmlspecialchars($event['zip']);

      $categories = '';
      for ($i = 0, $iz = count($event['category']); $i < $iz; ++$i) {
	 if (strlen($event['category'][$i]['title']) < 1) continue;
	 if (strlen($categories) > 0) $categories .= ', ';

	 $ct = &$event['category'][$i]['title'];
	 $categories .= '<a href="/'.strtolower(str_replace(' ', '_', $ct)).'">';
	 $categories .= $ct;
	 $categories .= '</a>';
      }

      $s = '						';
      $html  = $s.'<div id="event_details">'."\n";
      $html .= $s.'	<h3 class="subtitle" id="time">'.$time.'</h3>';
      $html .= $s.'	<h3 class="subtitle" id="address">'.$address.'</h3>';
      $html .= $s.'	<h3 class="subtitle" id="citystatezip">'.$city.', '.$state.' '.$zip.'</h3><br />';
      $html .= $likebox;
      $html .= $s.'	<span id="description">'.$description.'</span><br />'."\n";
      $html .= $s.'	<br />'."\n";
      $html .= $s.'	<h6 class="footer">Submitted by ( <span id="username">'.$username.'</span> ) to &lt; '.$categories.' &gt;</h6>'."\n";
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
	 $cat = '/'.HTML::url_friendly_category(CATEGORY_TITLE);
      } else {
	 $cat = '';
      }

      if (DATE_START !== DATE_END) {
	 $date = '/date-'.DATE_START.'-'.DATE_END;
      } else {
	 $date = '/date-'.DATE_START;
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
      if (PAGE !== 1) $html .= $s.'		<li><a href="'.$cat.$date.'/page'.(PAGE-1).'">&laquo; prev</a></li>'."\n";

      for ($i = $start; $i <= $end; ++$i) {
	 if ($i == PAGE)
	    $html .= $s.'		<li><span class="current">'.$i.'</span></li>'."\n";
	 else
	    $html.= $s.'		<li><a href="'.$cat.$date.'/page'.$i.'">'.$i.'</a></li>'."\n";
      }

      if (PAGE < $total) 
	 $html .= $s.'		<li><a href="'.$cat.$date.'/page'.(PAGE + 1).'">next &raquo;</a></li>'."\n";

      $html .= $s.'	</ul>'."\n";
      $html .= $s.'</div>'."\n";

      return $html;
   }

   public function modal_login() {
      $s = '			';
      $html  = $s.'<div id="modal-login" class="modal">'."\n";
      $html .= $s.'	<form name="login-form" class="modalform" id="login-form" action="/login" method="post">'."\n";
      $html .= $s.'		<fieldset>'."\n";
      $html .= $s.'			<ol>'."\n";
      $html .= $s.'				<li><label for="login-username">Username :</label><input type="text" id="login-username" name="username" class="textfield" /></li>'."\n";
      $html .= $s.'				<li><label for="login-password">Password :</label><input type="password" id="login-password" name="password" class="textfield" />'."\n";
      $html .= $s.'					<span class="formnote"><a href="#">forgot password?</a></span>'."\n";
      $html .= $s.'				</li>'."\n";
      $html .= $s.'			</ol>'."\n";
      $html .= $s.'			<button type="submit" class="btn-submit ib" id="btn-loginsubmit">Log In</button>'."\n";
      $html .= $s.'		</fieldset>'."\n";
      $html .= $s.'	</form>'."\n";
      $html .= $s.'<div><!-- end #modal-login -->'."\n";

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
	 $status .= $s.'	<li id="btn-submit"><a href="/submit">Submit</a></li>'."\n";
	 $status .= $s.'	<li id="btn-profile"><a href="/profile">Profile</a></li>'."\n";
	 $status .= $s.'	<li id="btn-logout"><a href="/logout">Logout</a></li>'."\n";
	 $status .= $s.'</ul>';
      } else {
	 $status  = $s.'<ul id="loggedout">'."\n";
	 $status .= $s.'	<li id="btn-login"><a href="/login">Login</a></li>'."\n";
	 $status .= $s.'	<li id="btn-signup"><a href="/signup">Sign up</a></li>'."\n";
	 $status .= $s.'</ul>';
      }

      $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"'."\n";
      $html .= '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
      $html .= ''."\n";
      $html .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
      $html .= '<head>'."\n";
      $html .= '	<meta http-equiv="content-type" content="text/html; charset=utf-8" />'."\n";
      $html .= '	<title>'.$title.'</title>'."\n";
      $html .= $csslinks."\n";
      $html .= $jslinks."\n";
      $html .= '</head>'."\n";
      $html .= '<body>'."\n";
      $html .= '	<div id="container">'."\n";
      $html .= '		<div id="header">'."\n";
      $html .= '			<h1 id="logo"><a href="/">AreaPilot.com : Find popular events in your area</a></h1>'."\n";
      $html .= $menu."\n";
      $html .= $status."\n";
      $html .= '		</div><!-- end #header -->'."\n";
      $html .= '		<div id="main">'."\n";

      return $html;
   }

   public function map($event) {
      $s = '						';
      
      $address  = htmlspecialchars($event['address']).', ';
      $address .= htmlspecialchars($event['city']).', ';
      $address .= htmlspecialchars($event['state']).' ';
      $address .= htmlspecialchars($event['zip']);

      $map = '<div id="map_canvas">'.$address.'</div>'."\n";

      return $map;
   }

}

?>

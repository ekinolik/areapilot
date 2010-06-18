<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/ap_default.css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/rounded.js"></script>
	<script type="text/javascript" src="js/ap_basics.js"></script>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1 id="logo"><a href="/">AreaPilot.com : Find popular events in your area</a></h1>
			<div id="subhead" class="clearfix">
				<ul id="categories">
					<li><a href="#">Live Music</a></li>
					<li><a href="#">Stand-Up Comedy</a></li>
					<li><a href="#">Live Theatre</a></li>
					<li><a href="#">Festivals</a></li>
					<li><a href="#">Club Parties</a></li>
				</ul>
			</div><!-- end #subhead -->
		</div><!-- end #header -->
		<div id="main">
			<div id="timeline" class="clearfix">
				<ul id="timeoptions">
					<li><a href="#" class="current" id="time-today">Today</a></li>
					<li><a href="#" id="time-tomorrow">Tomorrow</a></li>
					<li><a href="#" id="time-thisweek">This Week</a></li>
					<li><a href="#" id="time-nextweek">Next Week</a></li>
					<li><a href="#" id="time-thismonth">This Month</a></li>
					<li><a href="#" id="time-nextmonth">Next Month</a></li>
				</ul>
				<a href="#" id="rangeselect">Select Date Range</a>
			</div><!-- end #timeline -->
			<div id="inner" class="clearfix">
				<div id="maincol">
					<div id="posts">
						<h2>Events for <?= date("F jS, Y"); ?></h2>
						<div class="entry clearfix" id="entry1">
							<div class="likebox">
								<span class="numlikes">123</span>
								<span class="xtra">people like it</span>
								<a href="#" class="likeit">I Like It</a>
							</div><!-- end .likebox -->
							<h3><a href="#">The Screaming Butt Monkeys @ The Avalon</a></h3>
							<div class="description"><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
							</div><!-- end .description -->
							<ul class="actionlinks">
								<li><a href="#" class="attendthis">Attend This Event</a></li>
								<li><a href="#" class="attending">89 People Attending</a></li>
								<li><a href="#" class="commentsnum">123 Comments</a></li>
							</ul>
						</div><!-- end .entry -->

						<div class="entry clearfix" id="entry2">
							<div class="likebox">
								<span class="numlikes">123</span>
								<span class="xtra">people like it</span>
								<a href="#" class="likeit">I Like It</a>
							</div><!-- end .likebox -->
							<h3><a href="#">The Screaming Butt Monkeys @ The Avalon</a></h3>
							<div class="description"><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
							</div><!-- end .description -->
							<ul class="actionlinks">
								<li><a href="#" class="attendthis">Attend This Event</a></li>
								<li><a href="#" class="attending">89 People Attending</a></li>
								<li><a href="#" class="commentsnum">123 Comments</a></li>
							</ul>
						</div><!-- end .entry -->

						<div class="entry clearfix" id="entry3">
							<div class="likebox">
								<span class="numlikes">123</span>
								<span class="xtra">people like it</span>
								<a href="#" class="likeit">I Like It</a>
							</div><!-- end .likebox -->
							<h3><a href="#">The Screaming Butt Monkeys @ The Avalon</a></h3>
							<div class="description"><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
							</div><!-- end .description -->
							<ul class="actionlinks">
								<li><a href="#" class="attendthis">Attend This Event</a></li>
								<li><a href="#" class="attending">89 People Attending</a></li>
								<li><a href="#" class="commentsnum">123 Comments</a></li>
							</ul>
						</div><!-- end .entry -->

						<div class="entry clearfix" id="entry4">
							<div class="likebox">
								<span class="numlikes">123</span>
								<span class="xtra">people like it</span>
								<a href="#" class="likeit">I Like It</a>
							</div><!-- end .likebox -->
							<h3><a href="#">The Screaming Butt Monkeys @ The Avalon</a></h3>
							<div class="description"><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
							</div><!-- end .description -->
							<ul class="actionlinks">
								<li><a href="#" class="attendthis">Attend This Event</a></li>
								<li><a href="#" class="attending">89 People Attending</a></li>
								<li><a href="#" class="commentsnum">123 Comments</a></li>
							</ul>
						</div><!-- end .entry -->

					</div><!-- end #posts -->
				</div><!-- end #maincol -->
				<div id="sidecol">
					<div class="sidebox" id="popincategory">
						<h2>Popular In "Live Music"</h2>
						<div class="minievents">
							<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>
							<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>
							<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>
							<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>
							<a href="#" class="minievent clearfix"><span class="numlikes">123</span><span class="title">Big Fun Event Thing Is Going Down, People!</span></a>
						</div>
					</div><!-- end #popincategory -->					
				</div><!-- end #sidecol -->
			</div><!-- end #inner -->
		</div><!-- end #main -->
	</div><!-- end #container -->
</body>
</html>
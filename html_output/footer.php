<?php

$page_footer = HTML::page_footer();
$loginmodal = HTML::modal_login();
$signupmodal = HTML::modal_signup();
$changepassmodal = HTML::modal_change_password();
$forgotpassmodal = HTML::modal_forgot_password();
$attendeesmodal = HTML::modal_attendees();

print <<<EOF
$page_footer
$loginmodal
$signupmodal
$changepassmodal
$forgotpassmodal
$attendeesmodal
		</div><!-- end #main -->
	</div><!-- end #container -->
</body>
</html>
EOF;

?>

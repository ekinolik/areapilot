<?php

$loginmodal = HTML::modal_login();
$signupmodal = HTML::modal_signup();
$changepassmodal = HTML::modal_change_password();
$forgotpassmodal = HTML::modal_forgot_password();

print <<<EOF
$loginmodal
$signupmodal
$changepassmodal
$forgotpassmodal
		</div><!-- end #main -->
	</div><!-- end #container -->
</body>
</html>
EOF;

?>

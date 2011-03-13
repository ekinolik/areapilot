<?php

$loginmodal = HTML::modal_login();
$signupmodal = HTML::modal_signup();
$changepassmodal = HTML::modal_change_password();

print <<<EOF
$loginmodal
$signupmodal
$changepassmodal
		</div><!-- end #main -->
	</div><!-- end #container -->
</body>
</html>
EOF;

?>

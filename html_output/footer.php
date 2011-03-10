<?php

$loginmodal = HTML::modal_login();
$signupmodal = HTML::modal_signup();

print <<<EOF
$loginmodal
$signupmodal
		</div><!-- end #main -->
	</div><!-- end #container -->
</body>
</html>
EOF;

?>

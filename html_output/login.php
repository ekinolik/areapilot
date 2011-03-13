<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');

$header = HTML::body_header('Login');
$footer = HTML::body_footer();
$login  = HTML::login_form('fullform', $error_class);

print <<<EOF

$header
$login
$footer
EOF;
?>

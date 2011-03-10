<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');

$header = HTML::body_header('Create a New Account');
$footer = HTML::body_footer();
$form = HTML::signup_form("fullform", $error_class);

print <<<EOF
$header
$form
$footer
EOF;
?>

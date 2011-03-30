<?php

if ( ! defined('HTMLCLASS')) require(LIB_DIR.'HTML.php');

$header = HTML::body_header($TITLE);
$footer = HTML::body_footer();

$doc_html = HTML::document($document);

print <<<EOF
$header
$doc_html
$footer
EOF;

?>

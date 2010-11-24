<?php

if ( ! defined('CATEGORYCLASS')) require(LIB_DIR.'Category.php');

$h_category = new Category($db_class, $error_class);
$h_category->get_all_categories(TRUE);
$h_category->create_md();

$jslinks  = HTML::create_js_links($JS);
$csslinks = HTML::create_css_links($CSS);
$menu = HTML::menu($h_category->category);

$header = HTML::header($TITLE, $csslinks, $jslinks, $menu, LOGGED_IN);
print <<<EOF
$header
EOF;

unset($h_category, $jsfile, $jslinks, $cssfile, $csslinks, $menu);
?>

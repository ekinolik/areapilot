<?php

/*
if ( ! is_array($JS)) $JS = array();
for ($i = 0, $iz = count($JS), $jslinks = ''; $i < $iz; ++$i) {
   if (strlen(trim($JS[$i])) == '') continue;
   $jsfile = $JS[$i];
   $jslinks .= '    <script language="Javascript" src="/js/'.$jsfile.'"></script>'."\n";
}

if ( ! is_array($CSS)) $CSS = array();
for ($i = 0, $iz = count($CSS), $csslinks = ''; $i < $iz; ++$i) {
   if (strlen(trim($CSS[$i])) < 1) continue;
   $cssfile = $CSS[$i];
   $csslinks .= '    <link rel="stylesheet" type="text/css" href="/css/'.$cssfile.'" />'."\n";
}
 */
$jslinks  = HTML::create_js_links($JS);
$csslinks = HTML::create_css_links($CSS);
//$menu = HTML::menu($h_category->category);
$menu='';
define('CATEGORY_TITLE', '');
define('CATEGORY_PARENT_TITLE', '');

$header = HTML::header($TITLE, $csslinks, $jslinks, $menu, LOGGED_IN);
print <<<EOF
$header
EOF;
/*
print <<<EOF
<html>
  <head>
$csslinks
$jslinks
  </head>
  <body>

EOF;
 */

?>

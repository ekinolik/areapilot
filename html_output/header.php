<?php

if ( ! is_array($JS)) $JS = array();
for ($i = 0, $iz = count($JS), $jslinks = ''; $i < $iz; ++$i) {
   if (strlen(trim($JS[$i])) == '') continue;
   $jsfile = $JS[$i];
   $jslinks .= '    <script type="text/javascript" src="/js/'.$jsfile.'"></script>'."\n";
}

if ( ! is_array($CSS)) $CSS = array();
for ($i = 0, $iz = count($CSS), $csslinks = ''; $i < $iz; ++$i) {
   if (strlen(trim($CSS[$i])) < 1) continue;
   $cssfile = $CSS[$i];
   $csslinks .= '   <link rel="stylesheet" type="text/css" href="/css/'.$cssfile.'" />'."\n";
}


print <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <title>$TITLE</title>
$csslinks
$jslinks
</head>
<body>

EOF;

?>

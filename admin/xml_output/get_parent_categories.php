<?php

$xml = '';
for ($i = 0, $iz = count($cat_a); $i < $iz; ++$i) {
   $xml .= '  <cat>'."\n";
   $xml .= '    <id>'.$cat_a[$i]['id'].'</id>'."\n";
   $xml .= '    <name>'.$cat_a[$i]['name'].'</name>'."\n";
   $xml .= '    <active>'.$cat_a[$i]['active'].'</active>'."\n";
   $xml .= '    <type_id>'.$cat_a[$i]['type_id'].'</type_id>'."\n";
   $xml .= '    <parent>'.$cat_a[$i]['parent'].'</parent>'."\n";
   $xml .= '  </cat>'."\n";
}

print <<<EOF
$xml
EOF;

?>

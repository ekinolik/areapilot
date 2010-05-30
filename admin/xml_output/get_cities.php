<?php

$error = $error_class->error;

$last = '';
$xml = '';
for ($i = 0, $iz = count($zc); $i < $iz; ++$i) {
   if ($last != $zc[$i]['name'] && $last != '') $xml .= '  </city>'."\n";

   if ($last != $zc[$i]['name']) {
      $xml .= '  <city>'."\n";
      $xml .= '    <name>'.ucwords(strtolower($zc[$i]['name'])).'</name>'."\n";
      $xml .= '    <id>'.$zc[$i]['city_id'].'</id>'."\n";
   }

   $xml .= '    <zip>'."\n";
   $xml .= '      <id>'.$zc[$i]['zip_id'].'</id>'."\n";
   $xml .= '      <code>'.$zc[$i]['zip'].'</code>'."\n";
   $xml .= '    </zip>'."\n";
   $xml .= '    <neighborhood>'."\n";
   $xml .= '      <id>'.$zc[$i]['neighborhood_id'].'</id>'."\n";
   $xml .= '      <name>'.$zc[$i]['neighborhood_name'].'</name>'."\n";
   $xml .= '    </neighborhood>'."\n";

   if ($i + 1 == $iz) $xml .= '  </city>'."\n";

   $last = $zc[$i]['name'];
}  

print <<<EOF

<status>$status</status>
<error>$error</error>

<cities>
$xml
</cities>

EOF;

?>

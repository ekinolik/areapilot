<?php

$error = $error_class->error;

$zxml = '';
for ($i = 0, $iz = count($loc->zips); $i < $iz; ++$i) {
   $zxml .= '  <zip>'."\n";
   $zxml .= '    <id>'.$loc->zips[$i]['id'].'</id>'."\n";
   $zxml .= '    <name>'.$loc->zips[$i]['zip'].'</name>'."\n";
   $zxml .= '    <time_zone>'.$loc->zips[$i]['time_zone'].'</time_zone>'."\n";
   $zxml .= '  </zip>'."\n";
}

$cxml = '';
for ($i = 0, $iz = count($loc->cities); $i < $iz; ++$i) {
   $cxml .= '  <city>'."\n";
   $cxml .= '    <id>'.$loc->cities[$i]['id'].'</id>'."\n";
   $cxml .= '    <name>'.$loc->cities[$i]['name'].'</name>'."\n";
   $cxml .= '  </city>'."\n";
}

$acxml = '';
for ($i = 0, $iz = count($loc->area_codes); $i < $iz; ++$i) {
   $acxml .= '  <area_code>'."\n";
   $acxml .= '    <id>'.$loc->area_codes[$i]['id'].'</id>'."\n";
   $acxml .= '    <name>'.$loc->area_codes[$i]['area_code'].'</name>'."\n";
   $acxml .= '  </area_code>'."\n";
}

$coxml = '';
for ($i = 0, $iz = count($loc->counties); $i < $iz; ++$i) {
   $coxml .= '  <county>'."\n";
   $coxml .= '    <id>'.$loc->counties[$i]['id'].'</id>'."\n";
   $coxml .= '    <name>'.$loc->counties[$i]['name'].'</name>'."\n";
   $coxml .= '  </county>'."\n";
}

$axml = '';
for ($i = 0, $iz = count($loc->areas); $i < $iz; ++$i) {
   if (verify_int($loc->areas[$i]['id']) === FALSE) continue;
   if (isset($loc->areas[$i]['location_count'])) $l_cnt = $loc->areas[$i]['location_count'];
   else $l_cnt = 0;
   $axml .= '  <area>'."\n";
   $axml .= '    <id>'.$loc->areas[$i]['id'].'</id>'."\n";
   $axml .= '    <name>'.$loc->areas[$i]['name'].'</name>'."\n";
   $axml .= '    <parent>'.$loc->areas[$i]['parent'].'</parent>'."\n";
   $axml .= '    <location_count>'.$l_cnt.'</location_count>'."\n";
   $axml .= '  </area>'."\n";
}

$pxml = '';
for ($i = 0, $iz = count($loc->parent_areas); $i < $iz; ++$i) {
   if (verify_int($loc->parent_areas[$i]['id']) === FALSE) continue;
   if (isset($loc->parent_areas[$i]['subarea_count'])) 
      $s_cnt = $loc->parent_areas[$i]['subarea_count'];
   else $s_cnt = 0;
   $pxml .= '  <parea>'."\n";
   $pxml .= '    <id>'.$loc->parent_areas[$i]['id'].'</id>'."\n";
   $pxml .= '    <name>'.$loc->parent_areas[$i]['name'].'</name>'."\n";
   $pxml .= '    <parent>'.$loc->parent_areas[$i]['parent'].'</parent>'."\n";
   $pxml .= '    <subarea_count>'.$s_cnt.'</subarea_count>'."\n";
   $pxml .= '  </parea>'."\n";
}
print <<<EOF

<status>$status</status>
<error>$error</error>

<zip_codes>
$zxml
</zip_codes>

<cities>
$cxml
</cities>

<area_codes>
$acxml
</area_codes>

<counties>
$coxml
</counties>

<areas>
$axml
</areas>

<parent_areas>
$pxml
</parent_areas>

EOF;

?>

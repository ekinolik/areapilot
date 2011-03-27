<?php

$html = '';
for ($i = 0, $iz = count($page_c->pages); $i < $iz; ++$i) {
   $id = &$page_c->pages[$i]['id'];
   $filename = &$page_c->pages[$i]['filename'];
   $directory = &$page_c->pages[$i]['directory'];
   $title = &$page_c->pages[$i]['name'];
   $desc  = &$page_c->pages[$i]['text'];
   $url = str_replace(ADMIN_DIR, ADMIN_URL, $directory).$filename;

   $html .= '<div>';
   $html .= '  <span class="formcaption" id="formcaption_'.$id.'">'."\n";
   $html .= '    <a class="downloadform" href="'.$url.'" title="click to download '.$title.' ">'.$title.'</a>'."\n";
   $html .= '    <a class="editform" href="upload.php">Edit</a>'."\n";
   $html .= '    <a class="deleteform" href="upload.php?id='.$id.'">Delete</a>'."\n";
   $html .= '    <span class="hidden">'.$id.'</span>'."\n";
   $html .= '    <span class="description">'.nl2br(htmlspecialchars($desc)).'</span>'."\n";
   $html .= '  </span>'."\n";
   $html .= '  <span class="formform" id="formform_'.$id.'">'."\n";
   $html .= '    <form method="post" action="upload.php" enctype="multipart/form-data" >'."\n";
   $html .= '		<ol>'."\n";
   $html .= '      <li><label for="title">Title : </label>'."\n";
   $html .= '      <input type="text" name="title" id="title" value="'.htmlspecialchars($title).'" /></li>'."\n";
   $html .= '      <li><label for="formfile">File : </label>'."\n";
   $html .= '      <input type="file" name="formfile" id="formfile" /></li>'."\n";
   $html .= '      <li><label for="description">Description : </label>'."\n";
   $html .= '      <textarea name="description" id="description">'.htmlspecialchars($desc).'</textarea></li>'."\n";
   $html .= '      <li class="hidden"><input type="hidden" name="id" value="'.htmlspecialchars($id).'" /></li>'."\n";
   $html .= '      <li class="submit_line"<button class="cancelform">Cancel</button>'."\n";
   $html .= '      <button class="submitform" type="submit">Submit</button></li>'."\n";
   $html .= '      </ol>'."\n";
   $html .= '    </form>'."\n";
   $html .= '  </span>'."\n";
   $html .= '</div>';
}

?>

<form method="post" class="fullpageform" action="upload.php" enctype="multipart/form-data" >
	<fieldset>
		<h3 class="formheader">Add Form</h3>
		<ol>
			<li><label for="title">Title</label><input type="text" name="title" id="title" /></li>
			<li><label for="formfile">File</label><input type="file" name="formfile" id="formfile" /></li>
			<li><label for="description">Description</label><textarea name="description" id="description"></textarea></li>
			<li class="submit_line"><button type="submit" class="submitter">Add Form</button></li>
		</ol>
   </fieldset>
</form>
<h2 class="pageheader">Current Forms</h2>
<?= $html ?>

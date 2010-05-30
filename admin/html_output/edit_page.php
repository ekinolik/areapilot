<?php

$id = htmlspecialchars($page_c->id);
$name = htmlspecialchars($page_c->name);
$text = htmlspecialchars($page_c->text);
print <<<EOF

<h2 class="pageheader">$TITLE</h2>
<form method="post" action="edit_page.php" class="fullpageform">
   <fieldset>
      <h3 class="formheader">Page Data</h3>
      <ol>
         <input type="hidden" name="id" value="$id" />
	 <li><label for="email">Page Name</label>
	     <input type="text" name="pagename" id="pagename" value="$name"/>
         </li>
	 <li><label for="contents">Page Contents</label>
	     <textarea name="contents" id="contents" class="contents" cols="40" rows="10">$text</textarea>
         </li>
      </ol>
   </fieldset>
   <fieldset>
      <ol>
         <li class="submit_line"><button type="submit" class="submitter">Save Page</button></li>
      </li>
   </fieldset>
</form>

EOF;
?>


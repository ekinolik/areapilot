<?php

$category_opts = '<option value="0">--No Parent--</option>';
for ($i = 0; $i < $category_cnt; ++$i) {
   $category_opts .= '<option value="'.$category->category[$i]['id'].'">';
   $category_opts .= $category->category[$i]['title'];
   $category_opts .= '</option>';
}

print <<<EOF

   <div id="addcategory" class="full_form">
      <form method="post" action="add_category.php" class="full" name="add_cat">
	 <fieldset>
            <span class="errormsg">$error_class</span><br />
            <ol>
	       <li><label for="title">Category Title</label>
                  <input type="text" name="title" id="title" />
	       </li>
	       <li><label for="parent">Parent Category</label>
                  <select name="parent">$category_opts</select>
	       </li>
	       <li><label for="sequence">Sequence</label>
                  <input type="text" name="sequence" id="sequence" value="0" />
	       </li>
               <li class="submit_line">
                  <button type="submit" class="submitter">Register</button>
               </li>
            </ol>
         </fieldset>
      </form>
   </div>
   <script type="text/javascript">
      document.forms['add_cat'].elements['title'].focus();
   </script>
EOF;

?>
